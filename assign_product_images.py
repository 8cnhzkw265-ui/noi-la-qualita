"""Assign distinct catalog photography to static and WordPress product cards.

The source PDF embeds product photography by catalog page. This script gives
each static catalog item an optimized local asset and writes the same assets
into the WordPress theme, where cards can use them as a featured-image fallback.
"""
import json
import os
import re
import shutil
from collections import defaultdict
from PIL import Image
import pymupdf

ROOT = os.path.dirname(os.path.abspath(__file__))
WORKSPACE = os.path.dirname(ROOT)
SOURCE = os.path.join(WORKSPACE, "_extract", "images")
EXTRA_SOURCE = os.path.join(WORKSPACE, "_extract", "more_images")
STATIC_IMG = os.path.join(ROOT, "assets", "img")
WP_IMG = os.path.join(WORKSPACE, "wordpress", "themes", "noi-la-qualita", "assets", "img")
PRODUCTS = os.path.join(WORKSPACE, "_extract", "products.json")
PDF = os.path.join(WORKSPACE, "Catalogo_Linkato_10-9.pdf")
HTML = os.path.join(ROOT, "catalogo.html")

PAGE_TYPES = {
    "pasta": (3, 7), "riso": (8, 8), "salumi": (9, 12),
    "formaggi": (13, 22), "miele": (23, 23), "sottoli": (24, 25),
    "tonno": (26, 26), "tartufi": (27, 27),
    "doppio-senso": (28, 30), "alcolici": (30, 30),
}

os.makedirs(STATIC_IMG, exist_ok=True)
os.makedirs(WP_IMG, exist_ok=True)

images_by_type = defaultdict(list)
for kind, (first_page, last_page) in PAGE_TYPES.items():
    candidates = []
    for page in range(first_page, last_page + 1):
        for source_dir in (SOURCE, EXTRA_SOURCE):
            if not os.path.isdir(source_dir):
                continue
            candidates.extend(
                os.path.join(source_dir, name)
                for name in os.listdir(source_dir)
                if re.match(rf"p{page:02d}_x\d+\.(?:jpe?g|png)$", name, re.I)
            )
    candidates.sort(key=lambda path: (int(re.search(r"_x(\d+)", path).group(1)), path))
    candidates = [path for path in candidates if not path.endswith("p29_x463.jpeg")]
    if kind == "formaggi":
        # These are full product-detail page images. Their photos are assigned
        # directly to the matching rows below after cropping the photo area.
        candidates = [path for path in candidates if not re.search(r"p(?:16|17|18|19|20)_x", path)]
    # Product-card images should be recognizable catalog items, not tiny marks.
    for path in candidates:
        try:
            with Image.open(path) as image:
                if image.width >= 200 and image.height >= 200:
                    images_by_type[kind].append(path)
        except OSError:
            pass

products = json.load(open(PRODUCTS, encoding="utf-8"))
usage = defaultdict(int)
mapping = {}
for product in products:
    kind = product["type"]
    pool = images_by_type[kind]
    if not pool:
        continue
    source = pool[usage[kind] % len(pool)]
    usage[kind] += 1
    filename = f"catalog-{product['code'].lower()}.jpg"
    out = os.path.join(STATIC_IMG, filename)
    with Image.open(source) as image:
        image = image.convert("RGB")
        image.thumbnail((900, 900), Image.Resampling.LANCZOS)
        image.save(out, "JPEG", quality=80, optimize=True, progressive=True)
    shutil.copy2(out, os.path.join(WP_IMG, filename))
    mapping[product["code"].lower()] = filename

# Match cheese photos to the product rows that use them in the PDF. Some of
# these PDF resources contain the complete product page, so crop out its photo.
doc = pymupdf.open(PDF)
for page_no in range(13, 23):
    page = doc[page_no - 1]
    photo_refs = [
        info for info in page.get_image_info(xrefs=True)
        if info.get("xref") and info.get("bbox")
    ]
    photo_refs.sort(key=lambda info: info["bbox"][1])
    page_products = [product for product in products if product["page"] == page_no]
    words = page.get_text("words")
    for product in page_products:
        code = product["code"].upper()
        code_words = [word for word in words if re.sub(r"\s+", "", word[4]).upper() == code]
        if not code_words or not photo_refs:
            continue
        text_y = min(word[1] for word in code_words)
        image_ref = min(photo_refs, key=lambda info: abs(info["bbox"][1] - text_y))
        xref = image_ref["xref"]
        source_match = next((
            os.path.join(folder, name)
            for folder in (SOURCE, EXTRA_SOURCE)
            if os.path.isdir(folder)
            for name in os.listdir(folder)
            if re.match(rf"p{page_no:02d}_x{xref}\.(?:jpe?g|png)$", name, re.I)
        ), None)
        if not source_match:
            continue
        filename = f"catalog-{code.lower()}.jpg"
        out = os.path.join(STATIC_IMG, filename)
        with Image.open(source_match) as image:
            image = image.convert("RGB")
            # The detail-page resources show the product photo across the top;
            # a centered square crop removes the printed product copy below it.
            if page_no in range(16, 21) and image.height > image.width * 1.25:
                side = min(image.width, round(image.height * .37))
                left = (image.width - side) // 2
                image = image.crop((left, 0, left + side, side))
            image.thumbnail((900, 900), Image.Resampling.LANCZOS)
            image.save(out, "JPEG", quality=80, optimize=True, progressive=True)
        shutil.copy2(out, os.path.join(WP_IMG, filename))
        mapping[code.lower()] = filename

# Keep the static catalog image source in sync with the PDF product order.
html = open(HTML, encoding="utf-8").read()
pattern = re.compile(r'<article class="product-card".*?</article>', re.S)
def replace_card(match):
    card = match.group(0)
    sku_match = re.search(r"Cod\.\s*([A-Z0-9]+)", card)
    if not sku_match:
        return card
    filename = mapping.get(sku_match.group(1).lower())
    if not filename:
        return card
    return re.sub(r'(<img src=")[^\"]+', r'\g<1>assets/img/' + filename, card, count=1)

html = pattern.sub(replace_card, html)
open(HTML, "w", encoding="utf-8", newline="").write(html)

# WordPress and static versions share the same styles and behavior.
for relative in ("assets/css/style.css", "assets/js/main.js"):
    source = os.path.join(ROOT, relative)
    target = os.path.join(WORKSPACE, "wordpress", "themes", "noi-la-qualita", relative)
    os.makedirs(os.path.dirname(target), exist_ok=True)
    shutil.copy2(source, target)

for name in os.listdir(STATIC_IMG):
    if not name.startswith("catalog-"):
        shutil.copy2(os.path.join(STATIC_IMG, name), os.path.join(WP_IMG, name))

print(f"Prepared {len(mapping)} per-product images; matched PDF product photos on pages 13–22.")

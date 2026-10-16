"""Prepare optimized web assets for the static site (and later the WP theme)."""
import os

from PIL import Image

SRC = r"c:\kdfnsd\_extract\images"
DST = r"c:\kdfnsd\site\assets\img"
os.makedirs(DST, exist_ok=True)

ASSETS = {
    # hero + categories
    "hero-cellar":      ("p01_x2661.jpeg", 1800),
    "cat-pasta":        ("p03_x29.jpeg",   900),
    "cat-riso":         ("p08_x189.jpeg",  900),
    "cat-salumi":       ("p09_x218.jpeg",  900),
    "cat-formaggi":     ("p15_x284.jpeg",  900),
    "cat-sottoli":      ("p24_x375.jpeg",  900),
    "cat-miele":        ("p23_x526.jpeg",  900),
    "cat-tonno":        ("p26_x436.jpeg",  900),
    "cat-tartufi":      ("p27_x447.jpeg",  900),
    "cat-doppio-senso": ("p28_x459.jpeg",  900),
    "cat-alcolici":     ("p30_x540.jpeg",  900),
    # product: spaghettoni
    "spaghettoni-1":    ("p03_x29.jpeg",  1200),
    "spaghettoni-2":    ("p04_x95.jpeg",  1200),
    "spaghettoni-3":    ("p03_x30.jpeg",  1200),
    "spaghettoni-4":    ("p04_x79.jpeg",  1200),
    # product: prosciutto san daniele
    "san-daniele-1":    ("p12_x246.jpeg", 1200),
    "san-daniele-2":    ("p12_x244.jpeg", 1200),
    "san-daniele-3":    ("p12_x248.jpeg", 1200),
    "san-daniele-4":    ("p12_x250.jpeg", 1200),
    # product: il cilentano
    "cilentano-1":      ("p15_x286.jpeg", 1200),
    "cilentano-2":      ("p13_x255.jpeg", 1200),
    # product: tonno rosso
    "tonno-1":          ("p26_x436.jpeg", 1200),
    "tonno-2":          ("p26_x440.jpeg", 1200),
    "tonno-3":          ("p26_x438.jpeg", 1200),
    # product: doppio senso
    "doppio-senso-1":   ("p28_x459.jpeg", 1200),
    "doppio-senso-2":   ("p30_x540.jpeg", 1200),
    "doppio-senso-3":   ("p30_x537.jpeg", 1200),
}

for out_name, (src_name, max_w) in ASSETS.items():
    src = os.path.join(SRC, src_name)
    if not os.path.exists(src):
        # try png variant
        alt = os.path.splitext(src_name)[0] + ".png"
        src = os.path.join(SRC, alt)
    if not os.path.exists(src):
        print("MISSING:", src_name)
        continue
    img = Image.open(src).convert("RGB")
    scale = min(1.0, max_w / max(img.size))
    if scale < 1.0:
        img = img.resize((round(img.width * scale), round(img.height * scale)), Image.LANCZOS)
    out = os.path.join(DST, out_name + ".jpg")
    img.save(out, "JPEG", quality=78, optimize=True, progressive=True)
    print(f"{out_name}.jpg  {img.width}x{img.height}  {os.path.getsize(out)//1024} KB")

# --- logo variants ---
logo = Image.open(r"c:\kdfnsd\logo.jpeg").convert("RGB")

def trim_white(img, thresh=245):
    """Crop away uniform white margins."""
    from PIL import ImageChops
    bg = Image.new("RGB", img.size, (255, 255, 255))
    diff = ImageChops.difference(img, bg)
    bbox = diff.getbbox()
    return img.crop(bbox) if bbox else img

logo_t = trim_white(logo)
logo_t.save(os.path.join(DST, "logo.jpg"), "JPEG", quality=90, optimize=True)
print("logo.jpg", logo_t.size)

# transparent-background logo (white removed) for light surfaces
rgba = logo_t.convert("RGBA")
px = rgba.load()
w, h = rgba.size
for y in range(h):
    for x in range(w):
        r, g, b, a = px[x, y]
        if r > 238 and g > 238 and b > 238:
            px[x, y] = (r, g, b, 0)
rgba.save(os.path.join(DST, "logo.png"))

# white monochrome logo for red/dark surfaces: alpha = darkness of original
mono = Image.new("RGBA", rgba.size, (255, 255, 255, 0))
src_px = rgba.load()
dst_px = mono.load()
for y in range(h):
    for x in range(w):
        r, g, b, a = src_px[x, y]
        if a == 0:
            continue
        lum = (r + g + b) / 3
        alpha = 0 if lum > 238 else 255
        dst_px[x, y] = (255, 255, 255, alpha)
mono.save(os.path.join(DST, "logo-white.png"))
print("logo.png / logo-white.png", rgba.size)

# favicon: the blue sphere crop
sphere = logo.crop((255, 75, 365, 185)).resize((180, 180), Image.LANCZOS)
sphere.save(os.path.join(DST, "favicon.png"))
print("favicon.png", sphere.size)

# --- sample cream color from the pasta card for CSS ---
card = Image.open(os.path.join(SRC, "p03_x29.jpeg")).convert("RGB")
patch = card.crop((5, 5, 60, 60))
data = list(patch.getdata())
avg = tuple(sum(c[i] for c in data) // len(data) for i in range(3))
print(f"cream: #{avg[0]:02X}{avg[1]:02X}{avg[2]:02X}")

# green accent (HIGH QUALITY FOOD strip) sample around y~100
patch2 = card.crop((40, 85, 200, 105))
data2 = [c for c in patch2.getdata() if c[1] > c[0] and c[1] > c[2] and c[1] < 200]
if data2:
    avg2 = tuple(sum(c[i] for c in data2) // len(data2) for i in range(3))
    print(f"green accent: #{avg2[0]:02X}{avg2[1]:02X}{avg2[2]:02X}")

total = sum(os.path.getsize(os.path.join(DST, f)) for f in os.listdir(DST))
print(f"total assets: {total // 1024} KB")

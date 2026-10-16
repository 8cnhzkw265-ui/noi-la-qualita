"""Validate static site: HTML tag balance, local references, basic SEO meta."""
import os
import re
from html.parser import HTMLParser

ROOT = r"c:\kdfnsd\site"
PAGES = ["index.html", "catalogo.html", "prodotto.html"]
VOID = {"area", "base", "br", "col", "embed", "hr", "img", "input",
        "link", "meta", "param", "source", "track", "wbr",
        "path", "circle", "rect", "line", "polygon", "polyline", "ellipse"}

class Checker(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stack = []
        self.errors = []
        self.refs = []
        self.meta = {"title": 0, "desc": 0, "h1": 0, "canonical": 0,
                     "og": 0, "jsonld": 0, "lang": None}

    def handle_starttag(self, tag, attrs):
        ad = dict(attrs)
        if tag == "html":
            self.meta["lang"] = ad.get("lang")
        if tag == "title":
            self.meta["title"] += 1
        if tag == "meta" and ad.get("name") == "description":
            self.meta["desc"] += 1
        if tag == "meta" and (ad.get("property") or "").startswith("og:"):
            self.meta["og"] += 1
        if tag == "h1":
            self.meta["h1"] += 1
        if tag == "link" and ad.get("rel") == "canonical":
            self.meta["canonical"] += 1
        if tag == "script" and ad.get("type") == "application/ld+json":
            self.meta["jsonld"] += 1
        for key in ("href", "src"):
            if key in ad:
                self.refs.append(ad[key])
        if tag not in VOID:
            self.stack.append((tag, self.getpos()))

    def handle_startendtag(self, tag, attrs):
        self.handle_starttag(tag, attrs)
        if tag not in VOID and self.stack and self.stack[-1][0] == tag:
            self.stack.pop()

    def handle_endtag(self, tag):
        if tag in VOID:
            return
        if not self.stack:
            self.errors.append(f"unexpected </{tag}> at {self.getpos()}")
            return
        open_tag, pos = self.stack.pop()
        if open_tag != tag:
            self.errors.append(
                f"mismatch: <{open_tag}> opened at {pos}, closed by </{tag}> at {self.getpos()}")

ok = True
for page in PAGES:
    path = os.path.join(ROOT, page)
    with open(path, encoding="utf-8") as fh:
        html = fh.read()
    c = Checker()
    c.feed(html)
    c.close()

    problems = list(c.errors)
    for tag, pos in c.stack:
        problems.append(f"unclosed <{tag}> opened at {pos}")

    # local reference check
    for ref in c.refs:
        if ref.startswith(("http", "mailto:", "tel:", "#", "data:")):
            continue
        local = ref.split("?")[0].split("#")[0]
        if local and not os.path.exists(os.path.join(ROOT, local)):
            problems.append(f"broken ref: {ref}")

    m = c.meta
    if m["lang"] != "it":
        problems.append(f"lang={m['lang']}")
    for key in ("title", "desc", "h1", "canonical", "og", "jsonld"):
        if m[key] < 1:
            problems.append(f"missing {key}")
    if m["h1"] > 1:
        problems.append(f"multiple h1 ({m['h1']})")

    status = "OK" if not problems else "FAIL"
    if problems:
        ok = False
    print(f"{page}: {status}")
    for p in problems:
        print(f"   - {p}")

# css brace balance
css_path = os.path.join(ROOT, "assets", "css", "style.css")
with open(css_path, encoding="utf-8") as fh:
    css = fh.read()
bal = css.count("{") - css.count("}")
print(f"style.css: {'OK' if bal == 0 else f'FAIL braces off by {bal}'}")
if bal:
    ok = False

print("ALL OK" if ok else "ISSUES FOUND")

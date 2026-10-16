# Noi, la qualità — High Quality Food

Static site + WordPress theme for the **Noi, la qualità** food catalog
(Golosaqualità brand): 273 products across 10 categories — pasta, riso,
salumi, formaggi, miele, sottoli, tonno, tartufi, Doppio Senso, alcolici.

## Layout

- `index.html`, `catalogo.html`, `prodotto.html` — static site (GitHub Pages
  serves it from the repo root). `assets/css/`, `assets/js/`, `assets/img/`
  with 273 `catalog-<code>.jpg` product photos. Open `index.html` in a browser.
- `wordpress/themes/noi-la-qualita/` — WordPress theme (custom post type
  `prodotto`, `tipo_prodotto` + `regione` taxonomies, catalog templates).

## Static site

No build step, no dependencies — plain HTML/CSS/vanilla JS.

## WordPress theme

Copy `wordpress/themes/noi-la-qualita/` into `wp-content/themes/`,
activate, and import products as `prodotto` posts.

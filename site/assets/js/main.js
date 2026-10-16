/**
 * Noi, la qualità — catalogo digitale
 * Vanilla JS: navigazione mobile, filtri catalogo, galleria + lightbox.
 * Nessuna dipendenza esterna (performance + manutenibilità a lungo termine).
 */
(function () {
  'use strict';

  /* ---------- Menu mobile ---------- */
  var navToggle = document.querySelector('.nav-toggle');
  var mainNav = document.getElementById('main-nav');

  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function () {
      var open = mainNav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      navToggle.setAttribute('aria-label', open ? 'Chiudi il menu' : 'Apri il menu');
    });
  }

  /* ---------- Filtri catalogo ---------- */
  var typeFilters = document.getElementById('type-filters');
  var grid = document.getElementById('product-grid');

  if (typeFilters && grid) {
    var pills = Array.prototype.slice.call(
      typeFilters.querySelectorAll('.filter-pill')
    );
    var cards = Array.prototype.slice.call(
      grid.querySelectorAll('.product-card')
    );
    var regionSelect = document.getElementById('region-filter');
    var searchInput = document.getElementById('search-input');
    var resultLine = document.getElementById('result-line');
    var emptyState = document.getElementById('empty-state');

    var state = { type: 'all', region: 'all', query: '' };

    function applyFilters() {
      var visible = 0;
      var q = state.query.trim().toLowerCase();

      cards.forEach(function (card) {
        var typeOk =
          state.type === 'all' || card.dataset.type === state.type;
        var regionOk =
          state.region === 'all' || card.dataset.region === state.region;
        var searchOk =
          !q || (card.dataset.search || '').indexOf(q) !== -1;
        var show = typeOk && regionOk && searchOk;

        card.classList.toggle('is-hidden', !show);
        if (show) { visible += 1; }
      });

      if (resultLine) {
        resultLine.innerHTML =
          '<strong>' + visible + '</strong> ' +
          (visible === 1 ? 'prodotto trovato' : 'prodotti trovati');
      }
      if (emptyState) {
        emptyState.classList.toggle('show', visible === 0);
      }
    }

    function setType(type) {
      state.type = type;
      pills.forEach(function (pill) {
        pill.setAttribute(
          'aria-pressed',
          pill.dataset.filter === type ? 'true' : 'false'
        );
      });
      applyFilters();
    }

    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        setType(pill.dataset.filter);
        // URL pulito e condivisibile (?type=pasta)
        var url = new URL(window.location.href);
        if (pill.dataset.filter === 'all') {
          url.searchParams.delete('type');
        } else {
          url.searchParams.set('type', pill.dataset.filter);
        }
        window.history.replaceState({}, '', url);
      });
    });

    if (regionSelect) {
      regionSelect.addEventListener('change', function () {
        state.region = regionSelect.value;
        applyFilters();
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', function () {
        state.query = searchInput.value;
        applyFilters();
      });
    }

    // Filtro iniziale da ?type=... (link dalle card categoria in home)
    var params = new URLSearchParams(window.location.search);
    var initialType = params.get('type');
    if (initialType && pills.some(function (p) { return p.dataset.filter === initialType; })) {
      setType(initialType);
    } else {
      applyFilters();
    }
  }

  /* ---------- Galleria prodotto + lightbox ---------- */
  var gallery = document.getElementById('gallery');

  if (gallery) {
    var mainImg = document.getElementById('gallery-main-img');
    var mainBox = document.getElementById('gallery-main');
    var thumbs = Array.prototype.slice.call(
      gallery.querySelectorAll('.gallery-thumbs button')
    );
    var lightbox = document.getElementById('lightbox');
    var lightboxImg = document.getElementById('lightbox-img');
    var caption = document.getElementById('lightbox-caption');
    var btnClose = lightbox && lightbox.querySelector('.lightbox-close');
    var btnPrev = lightbox && lightbox.querySelector('.lightbox-prev');
    var btnNext = lightbox && lightbox.querySelector('.lightbox-next');
    var current = 0;
    var lastFocus = null;

    var slides = thumbs.map(function (btn) {
      return { src: btn.dataset.full, alt: btn.dataset.alt || '' };
    });

    function selectSlide(index) {
      if (!slides.length) { return; }
      current = (index + slides.length) % slides.length;
      var slide = slides[current];
      if (mainImg) {
        mainImg.src = slide.src;
        mainImg.alt = slide.alt;
      }
      thumbs.forEach(function (btn, i) {
        btn.setAttribute('aria-selected', i === current ? 'true' : 'false');
      });
    }

    function openLightbox(index) {
      if (!lightbox) { return; }
      lastFocus = document.activeElement;
      selectSlide(index);
      if (lightboxImg) {
        lightboxImg.src = slides[current].src;
        lightboxImg.alt = slides[current].alt;
      }
      if (caption) { caption.textContent = slides[current].alt; }
      lightbox.classList.add('open');
      document.body.style.overflow = 'hidden';
      if (btnClose) { btnClose.focus(); }
    }

    function closeLightbox() {
      if (!lightbox) { return; }
      lightbox.classList.remove('open');
      document.body.style.overflow = '';
      if (lastFocus) { lastFocus.focus(); }
    }

    function stepLightbox(delta) {
      if (!lightbox || !lightbox.classList.contains('open')) { return; }
      selectSlide(current + delta);
      if (lightboxImg) {
        lightboxImg.src = slides[current].src;
        lightboxImg.alt = slides[current].alt;
      }
      if (caption) { caption.textContent = slides[current].alt; }
    }

    thumbs.forEach(function (btn, i) {
      btn.addEventListener('click', function () { selectSlide(i); });
    });

    if (mainBox) {
      mainBox.addEventListener('click', function () { openLightbox(current); });
      mainBox.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openLightbox(current);
        }
      });
    }

    if (btnClose) { btnClose.addEventListener('click', closeLightbox); }
    if (btnPrev) { btnPrev.addEventListener('click', function () { stepLightbox(-1); }); }
    if (btnNext) { btnNext.addEventListener('click', function () { stepLightbox(1); }); }

    if (lightbox) {
      lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) { closeLightbox(); }
      });
    }

    document.addEventListener('keydown', function (event) {
      if (!lightbox || !lightbox.classList.contains('open')) { return; }
      if (event.key === 'Escape') { closeLightbox(); }
      if (event.key === 'ArrowLeft') { stepLightbox(-1); }
      if (event.key === 'ArrowRight') { stepLightbox(1); }
    });
  }
})();


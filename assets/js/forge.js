/* Forge theme script — vanilla JS. */
(function () {
	'use strict';
	var D = window.forgeData || {};
	var T = D.i18n || {};
	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var store = {
		get: function (k) { try { var v = JSON.parse(localStorage.getItem(k)); return Array.isArray(v) ? v : []; } catch (e) { return []; } },
		set: function (k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
	};
	var post = function (data, method) {
		var qs = new URLSearchParams(data).toString();
		var opts = { credentials: 'same-origin' };
		var url = D.ajax;
		if (method === 'POST') {
			opts.method = 'POST';
			opts.body = qs;
			opts.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
		} else {
			url += '?' + qs;
		}
		return fetch(url, opts).then(function (r) { return r.json(); });
	};
	var toast = function (msg) {
		var t = document.createElement('div');
		t.className = 'frg-toast';
		t.setAttribute('role', 'status');
		t.textContent = msg;
		document.body.appendChild(t);
		setTimeout(function () { t.remove(); }, 2600);
	};

	/* ---------- Drawers / overlay ---------- */
	var overlay = $('.frg-overlay');
	var lastFocus = null;
	function openDrawer(el, burger) {
		if (!el) { return; }
		closeDrawers(true);
		lastFocus = document.activeElement;
		el.hidden = false;
		overlay.hidden = false;
		document.body.classList.add('frg-lock');
		requestAnimationFrame(function () { el.classList.add('is-open'); });
		if (burger) { burger.setAttribute('aria-expanded', 'true'); }
		var f = $('input,button,a', $('.frg-drawer__body', el) || el);
		if (f && el.id === 'frg-mobile' && burger && burger.dataset.frgOpen === 'mobile-search') { var s = $('input[type=search]', el); if (s) { s.focus(); } }
	}
	function closeDrawers(quiet) {
		$$('.frg-drawer.is-open').forEach(function (d) {
			d.classList.remove('is-open');
			setTimeout(function () { if (!d.classList.contains('is-open')) { d.hidden = true; } }, 320);
		});
		overlay.hidden = true;
		document.body.classList.remove('frg-lock');
		var b = $('.frg-burger'); if (b) { b.setAttribute('aria-expanded', 'false'); }
		if (!quiet && lastFocus && lastFocus.focus) { lastFocus.focus(); }
	}
	function cartDrawer() { return $('#frg-cart'); }

	/* ---------- Modal ---------- */
	var modal = $('#frg-modal');
	function openModal(title, html) {
		$('.frg-modal__title', modal).textContent = title || '';
		$('.frg-modal__title', modal).hidden = !title;
		$('.frg-modal__body', modal).innerHTML = html;
		lastFocus = document.activeElement;
		modal.hidden = false;
		document.body.classList.add('frg-lock');
		sync();
		var c = $('[data-frg-close-modal]', modal); if (c) { c.focus(); }
	}
	function closeModal() {
		if (modal.hidden) { return; }
		modal.hidden = true;
		document.body.classList.remove('frg-lock');
		if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
	}
	function loading() { return '<p class="frg-empty">…</p>'; }

	/* ---------- Wishlist + compare ---------- */
	function sync() {
		var w = store.get('frg_wish'), c = store.get('frg_compare');
		$$('[data-frg-wish]').forEach(function (b) {
			var on = w.indexOf(b.dataset.frgWish) > -1;
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		$$('[data-frg-compare]').forEach(function (b) {
			var on = c.indexOf(b.dataset.frgCompare) > -1;
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		$$('[data-frg-wish-count]').forEach(function (n) { n.textContent = w.length; n.dataset.zero = w.length ? '0' : '1'; });
		var bar = $('.frg-comparebar');
		if (bar) {
			bar.hidden = c.length === 0;
			$('[data-frg-compare-n]', bar).textContent = c.length;
		}
	}
	function toggle(key, id, max) {
		var l = store.get(key), i = l.indexOf(id);
		if (i > -1) { l.splice(i, 1); }
		else {
			if (max && l.length >= max) { toast(T.compareMax); return; }
			l.push(id);
		}
		store.set(key, l);
		sync();
	}
	function showWishlist() {
		var ids = store.get('frg_wish');
		if (!ids.length) { openModal(T.saved, '<p class="frg-empty">' + T.noSaved + '</p>'); return; }
		openModal(T.saved, loading());
		post({ action: 'forge_cards', ids: ids.join(',') }).then(function (r) {
			$('.frg-modal__body', modal).innerHTML = '<div class="frg-grid">' + (r.data ? r.data.html : '') + '</div>';
			sync();
		});
	}
	function showCompare() {
		var ids = store.get('frg_compare');
		if (!ids.length) { return; }
		openModal(T.compare, loading());
		post({ action: 'forge_compare', ids: ids.join(',') }).then(function (r) {
			$('.frg-modal__body', modal).innerHTML = r.data ? r.data.html : '';
		});
	}

	/* ---------- Recently viewed ---------- */
	function recent() {
		var cur = $('.single-product [data-frg-product]');
		var list = store.get('frg_recent');
		var curId = cur ? cur.dataset.frgProduct : '';
		if (curId) {
			list = list.filter(function (x) { return x !== curId; });
			list.unshift(curId);
			store.set('frg_recent', list.slice(0, 12));
		}
		var ids = list.filter(function (x) { return x !== curId; }).slice(0, 4);
		$$('[data-frg-recent]').forEach(function (box) {
			if (!ids.length) { return; }
			post({ action: 'forge_cards', ids: ids.join(',') }).then(function (r) {
				if (r.data && r.data.html) {
					$('.frg-grid', box).innerHTML = r.data.html;
					box.hidden = false;
					sync();
				}
			});
		});
	}

	/* ---------- Live search ---------- */
	function liveSearch(box) {
		var input = $('input[type=search]', box), out = $('.frg-search__results', box), tm, seq = 0;
		input.addEventListener('input', function () {
			clearTimeout(tm);
			var q = input.value.trim();
			if (q.length < 2) { out.hidden = true; return; }
			tm = setTimeout(function () {
				var my = ++seq;
				post({ action: 'forge_live_search', q: q }).then(function (r) {
					if (my !== seq || !r.data) { return; }
					out.innerHTML = r.data.html;
					out.hidden = false;
				});
			}, 220);
		});
		document.addEventListener('click', function (e) { if (!box.contains(e.target)) { out.hidden = true; } });
		input.addEventListener('keydown', function (e) { if (e.key === 'Escape') { out.hidden = true; } });
	}

	/* ---------- Hero ---------- */
	function hero(el) {
		var slides = $$('.frg-hero__slide', el), dots = $$('.frg-dot', el), i = 0, timer;
		if (slides.length < 2) { return; }
		function go(n) {
			i = (n + slides.length) % slides.length;
			slides.forEach(function (s, k) { s.classList.toggle('is-active', k === i); });
			dots.forEach(function (d, k) { d.classList.toggle('is-active', k === i); });
		}
		function play() {
			if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
			stop();
			timer = setInterval(function () { go(i + 1); }, 6500);
		}
		function stop() { clearInterval(timer); }
		dots.forEach(function (d, k) { d.addEventListener('click', function () { go(k); play(); }); });
		var p = $('[data-frg-prev]', el), n = $('[data-frg-next]', el);
		if (p) { p.addEventListener('click', function () { go(i - 1); play(); }); }
		if (n) { n.addEventListener('click', function () { go(i + 1); play(); }); }
		el.addEventListener('mouseenter', stop);
		el.addEventListener('mouseleave', play);
		el.addEventListener('focusin', stop);
		play();
	}

	/* ---------- Tabs ---------- */
	function tabs(el) {
		var btns = $$('.frg-tab', el), panels = $$('.frg-panel', el);
		btns.forEach(function (b, k) {
			b.addEventListener('click', function () {
				btns.forEach(function (x, j) { x.classList.toggle('is-active', j === k); x.setAttribute('aria-selected', j === k ? 'true' : 'false'); });
				panels.forEach(function (p, j) { p.hidden = j !== k; });
			});
		});
	}

	/* ---------- Countdown ---------- */
	function countdown(el) {
		var end = parseInt(el.dataset.frgCountdown, 10);
		function pad(n) { return n < 10 ? '0' + n : String(n); }
		function tick() {
			var d = Math.max(0, end - Date.now()), s = Math.floor(d / 1000);
			var v = { d: Math.floor(s / 86400), h: pad(Math.floor(s % 86400 / 3600)), m: pad(Math.floor(s % 3600 / 60)), s: pad(s % 60) };
			Object.keys(v).forEach(function (k) { var n = $('[data-u=' + k + ']', el); if (n) { n.textContent = v[k]; } });
			if (d <= 0) { clearInterval(iv); }
		}
		var iv = setInterval(tick, 1000);
		tick();
	}

	/* ---------- Forms ---------- */
	function form(f) {
		var status = $('.frg-form__status', f);
		f.addEventListener('submit', function (e) {
			e.preventDefault();
			var bad = $$('[required]', f).some(function (x) { return !x.value.trim(); });
			status.className = 'frg-form__status';
			if (bad) { status.textContent = T.required; status.classList.add('is-err'); return; }
			var data = { action: 'forge_form', nonce: D.nonce, type: f.dataset.frgForm };
			new FormData(f).forEach(function (v, k) { data[k] = v; });
			status.textContent = T.sending;
			post(data, 'POST').then(function (r) {
				status.textContent = (r.data && r.data.message) || T.error;
				status.classList.add(r.success ? 'is-ok' : 'is-err');
				if (r.success) { f.reset(); }
			}).catch(function () { status.textContent = T.error; status.classList.add('is-err'); });
		});
	}

	/* ---------- Mega / dropdown ---------- */
	function dropdowns() {
		var pairs = $$('[data-frg-mega],[data-frg-drop]').map(function (b) { return { b: b, p: document.getElementById(b.getAttribute('aria-controls')) }; });
		function closeAll(except) { pairs.forEach(function (x) { if (x.p && x !== except) { x.p.hidden = true; x.b.setAttribute('aria-expanded', 'false'); } }); }
		pairs.forEach(function (x) {
			if (!x.p) { return; }
			var wrap = x.b.closest('.frg-nav__item'), t;
			function open() { clearTimeout(t); closeAll(x); x.p.hidden = false; x.b.setAttribute('aria-expanded', 'true'); }
			function shut() { t = setTimeout(function () { x.p.hidden = true; x.b.setAttribute('aria-expanded', 'false'); }, 160); }
			x.b.addEventListener('click', function () { x.p.hidden ? open() : closeAll(); });
			if (window.matchMedia('(hover: hover)').matches) {
				wrap.addEventListener('mouseenter', open);
				wrap.addEventListener('mouseleave', shut);
			}
		});
		document.addEventListener('click', function (e) { if (!e.target.closest('.frg-nav__item')) { closeAll(); } });
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeAll(); } });
	}

	/* ---------- Delegated clicks ---------- */
	document.addEventListener('click', function (e) {
		var t = e.target;
		var o = t.closest('[data-frg-open]');
		if (o) {
			var k = o.dataset.frgOpen;
			if (k === 'cart') {
				if (document.body.classList.contains('woocommerce-cart') || document.body.classList.contains('woocommerce-checkout')) { return; }
				e.preventDefault(); openDrawer(cartDrawer(), o); return;
			}
			if (k === 'mobile' || k === 'mobile-search') { e.preventDefault(); openDrawer($('#frg-mobile'), o); return; }
			if (k === 'wish') { e.preventDefault(); closeDrawers(true); showWishlist(); return; }
		}
		if (t.closest('[data-frg-close]')) { closeDrawers(); return; }
		if (t.closest('[data-frg-close-modal]') || t === modal) { closeModal(); return; }
		var w = t.closest('[data-frg-wish]'); if (w) { e.preventDefault(); toggle('frg_wish', w.dataset.frgWish); return; }
		var c = t.closest('[data-frg-compare]'); if (c) { e.preventDefault(); toggle('frg_compare', c.dataset.frgCompare, 4); return; }
		if (t.closest('[data-frg-compare-open]')) { showCompare(); return; }
		if (t.closest('[data-frg-compare-clear]')) { store.set('frg_compare', []); sync(); return; }
		var q = t.closest('[data-frg-quickview]');
		if (q) {
			e.preventDefault();
			openModal('', loading());
			post({ action: 'forge_quickview', ids: q.dataset.frgQuickview }).then(function (r) {
				if (r.success) { openModal('', r.data.html); } else { closeModal(); }
			});
		}
	});
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModal(); closeDrawers(); } });
	if (window.jQuery) {
		window.jQuery(document.body).on('added_to_cart', function () { closeModal(); var d = cartDrawer(); if (d) { openDrawer(d); } });
	}

	/* CRM request modal. The plugin fills #leads-form-div; the theme opens and closes it. */
	(function () {
		var popup = $('#tw-crm-popup.frg-crm');
		if (!popup) { return; }
		function setState(on) {
			popup.classList.toggle('is-open', on);
			popup.setAttribute('aria-hidden', on ? 'false' : 'true');
		}
		document.addEventListener('click', function (e) {
			if (e.target.closest('#tw-request-info-btn, [data-tw-crm-open]')) {
				setState(true);
				var field = $('#leads-form-div input:not([type="hidden"]), #leads-form-div select, #leads-form-div textarea', popup);
				if (field) { setTimeout(function () { field.focus(); }, 40); }
				return;
			}
			if (e.target.closest('[data-tw-crm-close]') && popup.contains(e.target)) { setState(false); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && popup.classList.contains('is-open')) { setState(false); }
		});
	})();

	/* Shop grid / list. The choice is remembered for the next visit. */
	(function () {
		var list = $('[data-frg-products]');
		var buttons = $$('.frg-view [data-frg-view]');
		if (!list || !buttons.length) { return; }
		function apply(view) {
			list.setAttribute('data-frg-view', view);
			buttons.forEach(function (b) {
				b.setAttribute('aria-pressed', b.getAttribute('data-frg-view') === view ? 'true' : 'false');
			});
		}
		var saved = '';
		try { saved = localStorage.getItem('frgProductView') || ''; } catch (e) {}
		if (saved === 'list' || saved === 'grid') { apply(saved); }
		buttons.forEach(function (b) {
			b.addEventListener('click', function () {
				var view = b.getAttribute('data-frg-view');
				apply(view);
				try { localStorage.setItem('frgProductView', view); } catch (e) {}
			});
		});
	})();

	/* ---------- Init ---------- */
	$$('[data-frg-search]').forEach(liveSearch);
	$$('[data-frg-hero]').forEach(hero);
	$$('[data-frg-tabs]').forEach(tabs);
	$$('[data-frg-countdown]').forEach(countdown);
	$$('[data-frg-form]').forEach(form);
	dropdowns();
	sync();
	recent();
})();

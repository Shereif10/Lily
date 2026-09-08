(function () {
	'use strict';

	/* Sticky header shadow */
	var header = document.querySelector('.lily-site-header');

	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};

		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* Mobile menu */
	var toggle = document.querySelector('.lily-menu-toggle');
	var nav = document.querySelector('.lily-primary-nav');

	if (toggle && nav) {
		var label = toggle.querySelector('.screen-reader-text');

		toggle.addEventListener('click', function () {
			var expanded = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			if (label) {
				label.textContent = expanded ? window.lilyI18n.openMenu || 'Open menu' : window.lilyI18n.closeMenu || 'Close menu';
			}
			nav.classList.toggle('is-open', !expanded);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}
	/* Header search bar */
	var searchToggle = document.querySelector('.lily-search-toggle');
	var searchBar = document.getElementById('lily-search-bar');

	if (searchToggle && searchBar) {
		var searchInput = searchBar.querySelector('input[type="search"]');

		var closeSearch = function () {
			searchBar.hidden = true;
			searchToggle.setAttribute('aria-expanded', 'false');
		};

		searchToggle.addEventListener('click', function () {
			var open = searchBar.hidden;
			searchBar.hidden = !open;
			searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');

			if (open && searchInput) {
				searchInput.focus();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !searchBar.hidden) {
				closeSearch();
				searchToggle.focus();
			}
		});

		document.addEventListener('click', function (event) {
			if (!searchBar.hidden && !searchBar.contains(event.target) && !searchToggle.contains(event.target)) {
				closeSearch();
			}
		});
	}

	/* Navigation dropdowns: click toggles everywhere; ESC/outside close.
	   Items flagged lily-nav-item--hover also open on pointer hover (desktop). */
	var dropdownItems = Array.prototype.slice.call(document.querySelectorAll('.lily-nav-item--dropdown'));
	var hoverDesktop = window.matchMedia('(hover: hover) and (pointer: fine) and (min-width: 901px)');

	var closeDropdowns = function (exceptItem) {
		dropdownItems.forEach(function (item) {
			if (item === exceptItem) {
				return;
			}

			item.classList.remove('is-open');
			var btn = item.querySelector('.lily-nav-link');

			if (btn) {
				btn.setAttribute('aria-expanded', 'false');
			}
		});
	};

	var setDropdownState = function (item, open) {
		item.classList.toggle('is-open', open);
		var btn = item.querySelector('.lily-nav-link');

		if (btn) {
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
	};

	dropdownItems.forEach(function (item) {
		var btn = item.querySelector('.lily-nav-link');

		if (!btn) {
			return;
		}

		btn.addEventListener('click', function () {
			var open = !item.classList.contains('is-open');
			closeDropdowns(item);
			setDropdownState(item, open);
		});

		/* Hover behavior for flagged items only (Company): opens on pointer enter,
		   stays open while the pointer is inside the item or its dropdown. */
		if (item.classList.contains('lily-nav-item--hover')) {
			item.addEventListener('mouseenter', function () {
				if (!hoverDesktop.matches) {
					return;
				}
				closeDropdowns(item);
				setDropdownState(item, true);
			});

			item.addEventListener('mouseleave', function (event) {
				if (!hoverDesktop.matches) {
					return;
				}
				if (event.relatedTarget && item.contains(event.relatedTarget)) {
					return;
				}
				setDropdownState(item, false);
			});
		}

		/* Mega dropdown: category buttons switch the visible submenu panel */
		item.addEventListener('click', function (event) {
			var cat = event.target.closest('.lily-mega__cat');
			if (!cat) { return; }

			var index = cat.getAttribute('data-lily-mega-cat');
			item.querySelectorAll('.lily-mega__cat').forEach(function (c) {
				c.classList.toggle('is-active', c === cat);
				c.setAttribute('aria-expanded', c === cat ? 'true' : 'false');
			});
			item.querySelectorAll('.lily-mega__panel').forEach(function (panel) {
				panel.classList.toggle('is-active', panel.getAttribute('data-lily-mega-panel') === index);
			});
		});
	});

	document.addEventListener('click', function (event) {
		if (!event.target.closest('.lily-primary-nav')) {
			closeDropdowns();
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			var openItem = dropdownItems.filter(function (item) {
				return item.classList.contains('is-open');
			})[0];

			if (openItem) {
				closeDropdowns();
				var btn = openItem.querySelector('.lily-nav-link');

				if (btn) {
					btn.focus();
				}
			}
		}
	});

	window.addEventListener('resize', function () {
		closeDropdowns();
	}, { passive: true });

	/* Hero carousel */
	var carousel = document.querySelector('[data-lily-carousel]');

	if (carousel) {
		var heroSlides = Array.prototype.slice.call(carousel.querySelectorAll('[data-lily-slide]'));
		var heroDots = Array.prototype.slice.call(carousel.querySelectorAll('[data-lily-carousel-dot]'));
		var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
		var heroCurrent = 0;
		var heroTimer = null;
		var HERO_INTERVAL = 6500;

		var heroGo = function (index) {
			index = (index + heroSlides.length) % heroSlides.length;

			heroSlides[heroCurrent].classList.remove('is-active');
			heroSlides[heroCurrent].setAttribute('aria-hidden', 'true');

			if (heroDots[heroCurrent]) {
				heroDots[heroCurrent].classList.remove('is-active');
				heroDots[heroCurrent].setAttribute('aria-selected', 'false');
			}

			heroCurrent = index;

			heroSlides[heroCurrent].classList.add('is-active');
			heroSlides[heroCurrent].setAttribute('aria-hidden', 'false');

			if (heroDots[heroCurrent]) {
				heroDots[heroCurrent].classList.add('is-active');
				heroDots[heroCurrent].setAttribute('aria-selected', 'true');
			}
		};

		var heroStop = function () {
			if (heroTimer) {
				clearInterval(heroTimer);
				heroTimer = null;
			}
		};

		var heroStart = function () {
			heroStop();
			if (!reduceMotion.matches && heroSlides.length > 1) {
				heroTimer = setInterval(function () {
					heroGo(heroCurrent + 1);
				}, HERO_INTERVAL);
			}
		};

		var prevBtn = carousel.querySelector('[data-lily-carousel-prev]');
		var nextBtn = carousel.querySelector('[data-lily-carousel-next]');

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				heroGo(heroCurrent - 1);
				heroStart();
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				heroGo(heroCurrent + 1);
				heroStart();
			});
		}

		heroDots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				heroGo(parseInt(dot.getAttribute('data-index'), 10) || 0);
				heroStart();
			});
		});

		carousel.addEventListener('mouseenter', heroStop);
		carousel.addEventListener('mouseleave', heroStart);
		carousel.addEventListener('focusin', heroStop);
		carousel.addEventListener('focusout', heroStart);

		carousel.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') {
				heroGo(heroCurrent - 1);
				heroStart();
			} else if (event.key === 'ArrowRight') {
				heroGo(heroCurrent + 1);
				heroStart();
			}
		});

		/* Touch swipe (direction-aware for RTL) */
		var touchStartX = null;

		carousel.addEventListener('touchstart', function (event) {
			if (event.touches.length === 1) {
				touchStartX = event.touches[0].clientX;
			}
		}, { passive: true });

		carousel.addEventListener('touchend', function (event) {
			if (touchStartX === null) {
				return;
			}

			var dx = event.changedTouches[0].clientX - touchStartX;
			touchStartX = null;

			if (Math.abs(dx) < 40) {
				return;
			}

			var rtl = document.documentElement.getAttribute('dir') === 'rtl';
			var forward = (dx < 0) !== rtl;
			heroGo(heroCurrent + (forward ? 1 : -1));
			heroStart();
		}, { passive: true });

		if (reduceMotion.addEventListener) {
			reduceMotion.addEventListener('change', function () {
				heroStart();
			});
		}

		heroStart();
	}

	/* Announcement bar marquee */
	var announcement = document.querySelector('.lily-announcement');

	if (announcement) {
		var announcementViewport = announcement.querySelector('.lily-announcement__viewport');
		var announcementTrack = announcement.querySelector('.lily-announcement__track');

		var syncAnnouncement = function () {
			if (!announcementViewport || !announcementTrack) {
				return;
			}

			announcement.classList.remove('is-scrolling');

			if (announcementTrack.scrollWidth > announcementViewport.clientWidth + 1) {
				announcement.classList.add('is-scrolling');
			}
		};

		window.addEventListener('resize', syncAnnouncement, { passive: true });
		syncAnnouncement();
	}
}());

	/**
	 * Subtle scroll reveal: section headings and cards fade/slide in once.
	 * Progressive enhancement only â€” without JS, everything stays visible.
	 */
	function initLilyReveal() {
		var candidates = document.querySelectorAll(
			'.lily-section-heading, .lily-collections__grid > *, .lily-colors__grid > *, .lily-products-row > *'
		);

		if (!candidates.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			return;
		}

		document.documentElement.classList.add('lily-reveal');

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -5% 0px' }
		);

		candidates.forEach(function (el) {
			el.classList.add('lily-reveal');

			if (el.getBoundingClientRect().top < window.innerHeight) {
				el.classList.add('is-visible');
				return;
			}

			observer.observe(el);
		});
	}

	initLilyReveal();

	/* --------------------------------------------------------------------
	 * Shop: mobile filter drawer
	 * ------------------------------------------------------------------ */
	function initShopDrawer() {
		var sidebar = document.getElementById('lily-shop-sidebar');
		var toggle = document.querySelector('.lily-shop-filter-toggle');
		var closeBtn = document.querySelector('.lily-shop-drawer-close');
		var overlay = document.querySelector('.lily-shop-drawer-overlay');

		if (!sidebar || !toggle) {
			return;
		}

		function setOpen(open) {
			sidebar.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			if (overlay) {
				if (open) {
					overlay.hidden = false;
					requestAnimationFrame(function () {
						overlay.classList.add('is-visible');
					});
				} else {
					overlay.classList.remove('is-visible');
				}
			}
			document.documentElement.style.overflow = open ? 'hidden' : '';
		}

		toggle.addEventListener('click', function () {
			setOpen(!sidebar.classList.contains('is-open'));
		});

		if (closeBtn) {
			closeBtn.addEventListener('click', function () { setOpen(false); });
		}
		if (overlay) {
			overlay.addEventListener('click', function () { setOpen(false); });
		}
		document.addEventListener('keydown', function (event) {
			if ('Escape' === event.key && sidebar.classList.contains('is-open')) {
				setOpen(false);
			}
		});
	}

	/* --------------------------------------------------------------------
	 * Shop: dual-handle price range (syncs with the number fields)
	 * ------------------------------------------------------------------ */
	function initPriceRange() {
		var wrap = document.querySelector('.lily-price-range');
		if (!wrap) {
			return;
		}

		var minInput = wrap.querySelector('.lily-price-range__input--min');
		var maxInput = wrap.querySelector('.lily-price-range__input--max');
		var fill = wrap.querySelector('.lily-price-range__fill');
		var hintMin = document.querySelector('.lily-price-hint__min');
		var hintMax = document.querySelector('.lily-price-hint__max');
		var numMin = document.querySelector('.lily-price-fields input[name="min_price"]');
		var numMax = document.querySelector('.lily-price-fields input[name="max_price"]');
		var lo = parseInt(wrap.getAttribute('data-min'), 10) || 0;
		var hi = parseInt(wrap.getAttribute('data-max'), 10) || 100;

		function clamp(value, bound, other) {
			value = parseInt(value, 10);
			if (isNaN(value)) { value = bound; }
			return Math.min(Math.max(value, Math.min(bound, other)), Math.max(bound, other));
		}

		function render() {
			var a = parseInt(minInput.value, 10);
			var b = parseInt(maxInput.value, 10);
			fill.style.insetInlineStart = ((a - lo) / (hi - lo)) * 100 + '%';
			fill.style.insetInlineEnd = 100 - ((b - lo) / (hi - lo)) * 100 + '%';
			if (hintMin) { hintMin.querySelector('.lily-price-num').textContent = a.toLocaleString(); }
			if (hintMax) { hintMax.querySelector('.lily-price-num').textContent = b.toLocaleString(); }
		}

		minInput.addEventListener('input', function () {
			minInput.value = clamp(minInput.value, lo, maxInput.value);
			render();
		});
		maxInput.addEventListener('input', function () {
			maxInput.value = clamp(maxInput.value, hi, minInput.value);
			render();
		});
		minInput.addEventListener('change', function () { if (numMin) { numMin.value = minInput.value > lo ? minInput.value : ''; } });
		maxInput.addEventListener('change', function () { if (numMax) { numMax.value = maxInput.value < hi ? maxInput.value : ''; } });
		if (numMin) { numMin.addEventListener('input', function () { minInput.value = clamp(numMin.value || lo, lo, maxInput.value); render(); }); }
		if (numMax) { numMax.addEventListener('input', function () { maxInput.value = clamp(numMax.value || hi, hi, minInput.value); render(); }); }

		render();
	}

	/* --------------------------------------------------------------------
	 * Shop: sort select submits natively on change; "show more" brands
	 * ------------------------------------------------------------------ */
	function initSortAndMore() {
		var sortSelect = document.getElementById('lily-shop-orderby');
		if (sortSelect) {
			sortSelect.addEventListener('change', function () {
				sortSelect.form.submit();
			});
		}

		Array.prototype.forEach.call(
			document.querySelectorAll('[data-lily-show-more]'),
			function (button) {
				button.addEventListener('click', function () {
					var list = button.closest('.lily-shop-acc');
					var extras = list.querySelectorAll('.is-extra');
					var showing = extras.length && extras[0].classList.contains('is-hidden');
					Array.prototype.forEach.call(extras, function (li) {
						li.classList.toggle('is-hidden', !showing);
					});
					button.textContent = showing ? button.getAttribute('data-less-text') : button.getAttribute('data-more-text');
				});
			}
		);
	}

	initShopDrawer();
	initPriceRange();
	initSortAndMore();

	/* --------------------------------------------------------------------
	 * Single product gallery: thumbnail → main image swap + prev/next arrows
	 * ------------------------------------------------------------------ */
	function initProductGallery() {
		var gallery = document.querySelector('.lily-sp-gallery');
		if (!gallery) { return; }

		var mainImg = gallery.querySelector('.lily-sp-gallery__main img');
		var thumbs = Array.prototype.slice.call(gallery.querySelectorAll('.lily-sp-gallery__thumb'));

		function setActive(thumb) {
			var full = thumb.getAttribute('data-full');
			if (!full) { return; }

			mainImg.style.opacity = '0';
			setTimeout(function () {
				mainImg.src = full;
				mainImg.style.opacity = '1';
			}, 150);

			thumbs.forEach(function (t) { t.classList.remove('is-active'); });
			thumb.classList.add('is-active');
		}

		if (mainImg && thumbs.length) {
			thumbs.forEach(function (thumb) {
				thumb.addEventListener('click', function () { setActive(thumb); });
			});

			var prev = gallery.querySelector('[data-lily-gallery-prev]');
			var next = gallery.querySelector('[data-lily-gallery-next]');

			var step = function (dir) {
				var current = thumbs.findIndex(function (t) { return t.classList.contains('is-active'); });
				var target = (current + dir + thumbs.length) % thumbs.length;
				setActive(thumbs[target]);
			};

			if (prev) { prev.addEventListener('click', function () { step(-1); }); }
			if (next) { next.addEventListener('click', function () { step(1); }); }
		}
	}

	initProductGallery();

	/* --------------------------------------------------------------------
	 * Quantity: [ − ] input [ + ] around the native input
	 * Single Product + Cart
	 * ------------------------------------------------------------------ */
	function initProductQty() {
		var wraps = document.querySelectorAll('.lily-sp-cart-area .quantity, .woocommerce-cart-form .quantity, #lily-cart-drawer .quantity');
		if (!wraps.length) { return; }

		var i18n = window.lilyI18n || {};

		Array.prototype.forEach.call(wraps, function (wrap) {
			if (wrap.querySelector('.lily-qty-btn')) { return; }

			var input = wrap.querySelector('input.qty');
			if (!input) { return; }

			function clamp(value) {
				var min = input.min !== '' && !isNaN(parseFloat(input.min)) ? parseFloat(input.min) : 1;
				var max = input.max !== '' && !isNaN(parseFloat(input.max)) ? parseFloat(input.max) : Infinity;
				return Math.min(Math.max(value, min), max);
			}

			function apply(delta) {
				var step = parseFloat(input.step) || 1;
				var current = parseFloat(input.value) || clamp(0);
				var next = clamp(current + delta * step);
				if (next === current) { return; }
				input.value = next;
				input.dispatchEvent(new Event('change', { bubbles: true }));
				input.dispatchEvent(new Event('input', { bubbles: true }));
			}

			var minus = document.createElement('button');
			minus.type = 'button';
			minus.className = 'lily-qty-btn lily-qty-btn--minus';
			minus.setAttribute('aria-label', i18n.decreaseQty || 'Decrease quantity');
			minus.textContent = '\u2212';
			minus.addEventListener('click', function () { apply(-1); });

			var plus = document.createElement('button');
			plus.type = 'button';
			plus.className = 'lily-qty-btn lily-qty-btn--plus';
			plus.setAttribute('aria-label', i18n.increaseQty || 'Increase quantity');
			plus.textContent = '+';
			plus.addEventListener('click', function () { apply(1); });

			wrap.insertBefore(minus, input);
			wrap.appendChild(plus);
		});
	}

	initProductQty();

	/* --------------------------------------------------------------------
	 * PRESCRIPTION POWER — quiet client-side gate (UX layer only).
	 * Highlights ONLY the missing eye selector(s), shows one restrained
	 * Lily-styled message, and clears automatically as soon as valid
	 * values are chosen. The server always re-validates authoritative.
	 * ------------------------------------------------------------------ */
	function initRxValidation() {
		function selectsIn(form) {
			return form.querySelectorAll('.lily-rx__select');
		}

		function missingFields(form) {
			var missing = [];
			Array.prototype.forEach.call(selectsIn(form), function (sel) {
				if (!sel.value) { missing.push(sel); }
			});
			return missing;
		}

		function clearErrors(form) {
			Array.prototype.forEach.call(form.querySelectorAll('.lily-rx__select.is-invalid'), function (sel) {
				sel.classList.remove('is-invalid');
				sel.removeAttribute('aria-invalid');
			});
			var msg = form.querySelector('.lily-rx__error');
			if (msg) { msg.hidden = true; }
		}

		function showErrors(form, missing) {
			var i18n = window.lilyI18n || {};
			missing.forEach(function (sel) {
				sel.classList.add('is-invalid');
				sel.setAttribute('aria-invalid', 'true');
			});
			var rx = form.querySelector('.lily-rx');
			if (!rx) { return; }
			var msg = rx.querySelector('.lily-rx__error');
			if (!msg) {
				msg = document.createElement('p');
				msg.className = 'lily-rx__error';
				msg.setAttribute('role', 'alert');
				var hint = rx.querySelector('.lily-rx__hint');
				rx.insertBefore(msg, hint || null);
			}
			msg.textContent = i18n.selectPowerForBothEyes || 'Please select the prescription power for both eyes.';
			msg.hidden = false;
		}

		/* Live feedback: choosing a value clears that field instantly; the
		   message disappears only once both eyes have valid selections. */
		document.addEventListener('change', function (e) {
			var sel = e.target;
			if (!sel.classList || !sel.classList.contains('lily-rx__select') || !sel.value) { return; }
			sel.classList.remove('is-invalid');
			sel.removeAttribute('aria-invalid');
			var form = sel.closest('form');
			if (form && !missingFields(form).length) { clearErrors(form); }
		});

		window.lilyRx = {
			validate: function (form) {
				var missing = missingFields(form);
				if (!missing.length) {
					clearErrors(form);
					return true;
				}
				clearErrors(form);
				showErrors(form, missing);
				try { missing[0].focus(); } catch (err) { /* no-op */ }
				return false;
			}
		};

		/* The selects carry required for no-JS semantics, but native browser
		   bubbles would swallow the Lily feedback — hand validation to the gate. */
		Array.prototype.forEach.call(document.querySelectorAll('form.cart'), function (form) {
			if (form.querySelector('.lily-rx__select')) {
				form.setAttribute('novalidate', 'novalidate');
			}
		});
	}

	initRxValidation();
	/* --------------------------------------------------------------------
	 * CART DRAWER — native WooCommerce presentation layer.
	 * Every mutation goes through wc-ajax endpoints that use core cart
	 * methods (add_to_cart / set_quantity / remove_cart_item), preserving
	 * validation (incl. Prescription Power), shipping and totals.
	 * ------------------------------------------------------------------ */
	function lilyParseJson(response) {
		// Tolerate stray PHP notices before the JSON payload.
		return response.text().then(function (text) {
			var start = text.indexOf('{');
			if (start < 0) { throw new Error('Invalid JSON'); }
			return JSON.parse(text.slice(start));
		});
	}

	function initCartDrawer() {
		var cfg = window.lilyCartConfig || {};
		var drawer = document.getElementById('lily-cart-drawer');
		if (!drawer || !cfg.ajaxUrl) { return; }

		var overlay   = document.querySelector('.lily-drawer-overlay');
		var bodyEl    = drawer.querySelector('.lily-drawer__body');
		var noticesEl = drawer.querySelector('.lily-drawer__notices');
		var isOpen    = false;
		var queue     = Promise.resolve();

		function countBadges() {
			return document.querySelectorAll('.lily-cart-count');
		}

		function setCounts(n) {
			Array.prototype.forEach.call(countBadges(), function (b) {
				b.textContent = n;
				b.classList.toggle('is-empty', !n);
			});
			var badge = drawer.querySelector('.lily-drawer__count-badge');
			if (badge) { badge.textContent = n; }
		}

		function open() {
			isOpen = true;
			drawer.hidden = false;
			if (overlay) { overlay.hidden = false; }
			requestAnimationFrame(function () {
				drawer.classList.add('is-open');
				if (overlay) { overlay.classList.add('is-open'); }
			});
			document.body.classList.add('lily-drawer-open');
			drawer.setAttribute('aria-hidden', 'false');
			var closeBtn = drawer.querySelector('.lily-drawer__close');
			if (closeBtn) { closeBtn.focus(); }
		}

		function close() {
			if (!isOpen) { return; }
			isOpen = false;
			drawer.classList.remove('is-open');
			if (overlay) { overlay.classList.remove('is-open'); }
			document.body.classList.remove('lily-drawer-open');
			drawer.setAttribute('aria-hidden', 'true');
			window.setTimeout(function () {
				if (!isOpen) {
					drawer.hidden = true;
					if (overlay) { overlay.hidden = true; }
				}
			}, 280);
		}

		function applyState(data) {
			bodyEl.innerHTML = data.body || '';
			setCounts(parseInt(data.count, 10) || 0);
			initProductQty();
			bindItems(bodyEl);
		}

		function showNotices(html) {
			if (!html) { return false; }
			noticesEl.innerHTML = html;
			noticesEl.hidden = false;
			return true;
		}

		function hideNotices() {
			noticesEl.hidden = true;
			noticesEl.innerHTML = '';
		}

		function request(endpoint, params) {
			var body = new URLSearchParams(params);
			body.set('action', 'lily_' + endpoint);
			queue = queue.then(function () {
				return fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString()
				}).then(lilyParseJson);
			});
			return queue;
		}

		function updateItem(params) {
			request('update_cart', params).then(function (data) {
				hideNotices();
				showNotices(data.notices);
				applyState(data);
			});
		}

		function bindItems(root) {
			Array.prototype.forEach.call(root.querySelectorAll('.lily-cart-item__remove'), function (a) {
				if (a.dataset.lilyBound) { return; }
				a.dataset.lilyBound = '1';
				a.addEventListener('click', function (e) {
					e.preventDefault();
					updateItem({ key: a.dataset.cart_item_key, cart_action: 'remove' });
				});
			});

			Array.prototype.forEach.call(root.querySelectorAll('.lily-cart-item input.qty'), function (input) {
				if (input.dataset.lilyBound) { return; }
				input.dataset.lilyBound = '1';
				var item = input.closest('.lily-cart-item');
				var keyEl = item ? item.querySelector('[data-cart_item_key]') : null;
				input.addEventListener('change', function () {
					if (!keyEl) { return; }
					updateItem({ key: keyEl.dataset.cart_item_key, qty: parseFloat(input.value) || 0 });
				});
			});
		}

		/* Add to Cart — intercept native submits on Single Product */
		document.addEventListener('submit', function (e) {
			var form = e.target;
			if (!form.classList.contains('cart') || !form.querySelector('.single_add_to_cart_button')) { return; }

			/* Prescription Power gate — blocks the request with field-level
			   feedback before anything is sent (server re-validates too). */
			if (window.lilyRx && !window.lilyRx.validate(form)) { e.preventDefault(); return; }

			e.preventDefault();

			var btn = form.querySelector('.single_add_to_cart_button');
			btn.disabled = true;

			// Build the payload explicitly. The ID must NOT travel as `add-to-cart`,
			// otherwise WooCommerce's Form Handler hijacks admin-ajax with a redirect.
			var fd = new URLSearchParams(new FormData(form));
			var pidBtn = form.querySelector('[name="add-to-cart"]');
			fd.delete('add-to-cart');
			if (pidBtn && pidBtn.value) { fd.set('lily_product_id', pidBtn.value); }
			fd.set('action', 'lily_add_to_cart');

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: fd.toString()
			})
 				.then(lilyParseJson)
 				.then(function (data) {
 					btn.disabled = false;
 					if (data.added === true) {
 						hideNotices();
 						applyState(data);
 						open();
 					} else {
 						// Preserve real WooCommerce error behavior — no fake success, no drawer.
 						showNotices(data.notices);
 						open(); // the drawer is the theme's notice surface — reveal the reason
 					}
 				})
 				.catch(function () {
 					btn.disabled = false;
 					form.submit(); // graceful fallback to native WooCommerce flow
 				});
		});

		/* Best Sellers card Add to Cart — same drawer endpoint + validation as single product */
		document.addEventListener('click', function (e) {
			var btn = e.target.closest ? e.target.closest('.lily-best-card__cart--ajax') : null;
			if (!btn) { return; }
			e.preventDefault();
			if (btn.disabled) { return; }
			btn.disabled = true;

			var body = 'action=lily_add_to_cart'
				+ '&lily_product_id=' + encodeURIComponent(btn.getAttribute('data-product_id') || '')
				+ '&quantity=' + encodeURIComponent(btn.getAttribute('data-quantity') || '1');

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body
			})
				.then(lilyParseJson)
				.then(function (data) {
					btn.disabled = false;
					if (data.added === true) {
						hideNotices();
						applyState(data);
						open();
					} else {
						// Real WooCommerce notices (stock, RX validation) — no fake success.
						showNotices(data.notices);
						open(); // reveal the reason the add failed
					}
				})
				.catch(function () {
					btn.disabled = false;
					window.location.href = btn.href; // graceful fallback to native WooCommerce flow
				});
		});

		/* Navbar cart icon(s) open the same drawer */
		document.addEventListener('click', function (e) {
			var link = e.target.closest ? e.target.closest('.lily-cart-link') : null;
			if (link) {
				e.preventDefault();
				open();
				return;
			}
			if (e.target.closest && e.target.closest('.lily-drawer__close')) { close(); }
			if (overlay && e.target === overlay) { close(); }
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { close(); }
		});

		bindItems(bodyEl);
	}

	function initCheckoutShippingSync() {
		/* WooCommerce's update trigger must react to the Lily area select
		   so shipping totals recalculate the moment an area is chosen. */
		var form = document.querySelector('form.checkout');
		var area = document.getElementById('lily_delivery_area');
		if (!form || !area || !window.jQuery) { return; }
		window.jQuery(area).on('change input', function () {
			window.jQuery(document.body).trigger('update_checkout');
		});
	}

	function initContactForm() {
		/* Contact page: fetch the form via REST so TranslatePress cannot
		   rewrite its markup. Submission stays a native POST. */
		var slot = document.querySelector('.lily-contact-slot');
		if (!slot || slot.dataset.lilyLoaded) { return; }
		slot.dataset.lilyLoaded = '1';
		var base = (window.lilyCartConfig && window.lilyCartConfig.ajaxUrl)
			? window.lilyCartConfig.ajaxUrl.replace('wp-admin/admin-ajax.php', '')
			: '/';
		fetch(base + 'wp-json/lily/v1/contact-form')
			.then(function (r) { return r.json(); })
			.then(function (data) {
				slot.innerHTML = (data && data.html) ? data.html : '';
			})
			.catch(function () { /* leave the quiet placeholder */ });
	}

	function initBestSellersCarousel() {
		/* Best Sellers: arrows scroll the track; dots appear on tablet/mobile. */
		var wrap = document.querySelector('.lily-best-sellers__carousel');
		if (!wrap || wrap.dataset.lilyBestInit) { return; }
		wrap.dataset.lilyBestInit = '1';

		var track = wrap.querySelector('[data-lily-best-track]');
		var prev = wrap.querySelector('[data-lily-carousel-prev]');
		var next = wrap.querySelector('[data-lily-carousel-next]');
		var dotsWrap = document.querySelector('[data-lily-best-dots]');
		if (!track || !prev || !next) { return; }

		var step = function () {
			var card = track.querySelector('.lily-product-card');
			if (!card) { return 300; }
			var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 24;
			return card.getBoundingClientRect().width + gap;
		};

		prev.addEventListener('click', function () {
			track.scrollBy({ left: -step(), behavior: 'smooth' });
		});

		next.addEventListener('click', function () {
			track.scrollBy({ left: step(), behavior: 'smooth' });
		});

		if (!dotsWrap) { return; }

		var buildDots = function () {
			var max = track.scrollWidth - track.clientWidth;
			if (max <= 4 || !window.matchMedia('(max-width: 900px)').matches) {
				dotsWrap.style.display = '';
				dotsWrap.innerHTML = '';
				return;
			}
			dotsWrap.style.display = 'flex';
			var n = Math.round(max / step()) + 1;
			var html = '';
			for (var i = 0; i < n; i++) {
				html += '<span class="lily-best-sellers__dot' + (i === 0 ? ' is-active' : '') + '"></span>';
			}
			dotsWrap.innerHTML = html;
		};

		var syncDots = function () {
			var dots = dotsWrap.children;
			if (!dots.length) { return; }
			var max = track.scrollWidth - track.clientWidth;
			var idx = max > 0 ? Math.round(Math.abs(track.scrollLeft) / max * (dots.length - 1)) : 0;
			for (var i = 0; i < dots.length; i++) {
				dots[i].classList.toggle('is-active', i === idx);
			}
		};

		track.addEventListener('scroll', function () { window.requestAnimationFrame(syncDots); }, { passive: true });
		window.addEventListener('resize', function () { buildDots(); syncDots(); }, { passive: true });
		buildDots();
		syncDots();
	}

	/* --------------------------------------------------------------------
	 * Single product accordions: Description / Details / How to Use / Reviews.
	 * Closed by default; only the clicked item toggles. Mouse clicks blur
	 * the button afterwards so no persistent focus styling remains, while
	 * keyboard users keep the :focus-visible ring (see lily.css).
	 * ------------------------------------------------------------------ */
	function initProductAccordions() {
		var wrap = document.querySelector('[data-lily-sp-acc]');
		if (!wrap || wrap.dataset.lilySpAccInit) { return; }
		wrap.dataset.lilySpAccInit = '1';

		var setItem = function (item, open) {
			item.classList.toggle('is-open', open);
			var btn = item.querySelector('[data-lily-sp-acc-toggle]');
			if (btn) { btn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
		};

		wrap.addEventListener('click', function (event) {
			var btn = event.target.closest ? event.target.closest('[data-lily-sp-acc-toggle]') : null;
			if (!btn || !wrap.contains(btn)) { return; }
			var item = btn.closest('[data-lily-sp-acc-item]');
			if (!item) { return; }
			setItem(item, !item.classList.contains('is-open'));
			if (event.detail !== 0 && btn.blur) { btn.blur(); }
		});

		/* After a native review POST (or a #lily-reviews / #comment- link),
		   reveal the reviews panel so the notice/content is visible. */
		var shouldOpenReviews = false;
		try {
			if (window.location.hash === '#lily-reviews' ||
				(window.location.hash && window.location.hash.indexOf('#comment-') === 0)) {
				shouldOpenReviews = true;
			} else if (window.URLSearchParams && new window.URLSearchParams(window.location.search).get('lily_review') === 'submitted') {
				shouldOpenReviews = true;
			}
		} catch (err) { /* keep accordions closed */ }

		if (shouldOpenReviews) {
			var tab = wrap.querySelector('#lily-sp-tab-reviews');
			var reviewsItem = tab ? tab.closest('[data-lily-sp-acc-item]') : null;
			if (reviewsItem) {
				setItem(reviewsItem, true);
				var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
				var target = document.getElementById('lily-reviews');
				if (target && target.scrollIntoView) {
					window.setTimeout(function () {
						target.scrollIntoView({ block: 'start', behavior: reduceMotion ? 'auto' : 'smooth' });
					}, 60);
				}
			}
		}
	}

	/* --------------------------------------------------------------------
	 * Reviews: WRITE A REVIEW toggles the native review form; file input
	 * is trimmed to 3 client-side (server-side validation is authoritative).
	 * ------------------------------------------------------------------ */
	function initLilyReviewForms() {
		var toggles = document.querySelectorAll('[data-lily-review-form-toggle]');
		var formWrap = document.getElementById('lily-review-form');
		if (!toggles.length || !formWrap) { return; }

		var setToggles = function (expanded) {
			Array.prototype.forEach.call(toggles, function (t) {
				t.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			});
		};

		Array.prototype.forEach.call(toggles, function (toggle) {
			toggle.addEventListener('click', function (event) {
				var opening = formWrap.hidden;
				formWrap.hidden = !opening;
				setToggles(opening);
				if (opening) {
					var first = formWrap.querySelector('input[name="rating"], textarea, input[name="author"], input[name="email"]');
					if (first && first.focus) { first.focus({ preventScroll: false }); }
				} else if (event.detail !== 0 && toggle.blur) {
					toggle.blur();
				}
			});
		});

		var fileInput = document.getElementById('lily-review-images');
		if (fileInput) {
			fileInput.addEventListener('change', function () {
				if (fileInput.files && fileInput.files.length > 3 && window.DataTransfer) {
					try {
						var dt = new window.DataTransfer();
						for (var i = 0; i < 3; i++) { dt.items.add(fileInput.files[i]); }
						fileInput.files = dt.files;
					} catch (err) { /* server trims to 3 regardless */ }
				}
			});
		}
	}

	/* --------------------------------------------------------------------
	 * Reviews: minimal accessible lightbox for customer photo thumbnails.
	 * No dependency; ESC / backdrop / close button all dismiss.
	 * ------------------------------------------------------------------ */
	function initReviewLightbox() {
		var box = document.querySelector('[data-lily-review-lightbox]');
		if (!box || box.dataset.lilyReviewLbInit) { return; }
		box.dataset.lilyReviewLbInit = '1';

		var img = box.querySelector('[data-lily-review-lightbox-img]');
		var lastFocus = null;

		var open = function (src) {
			if (!src || !img) { return; }
			lastFocus = document.activeElement;
			img.setAttribute('src', src);
			box.hidden = false;
			var closeBtn = box.querySelector('[data-lily-review-lightbox-close]');
			if (closeBtn && closeBtn.focus) { closeBtn.focus(); }
		};

		var close = function () {
			if (box.hidden) { return; }
			box.hidden = true;
			if (img) { img.removeAttribute('src'); }
			if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
		};

		document.addEventListener('click', function (event) {
			var zoom = event.target.closest ? event.target.closest('[data-lily-review-zoom]') : null;
			if (zoom) {
				open(zoom.getAttribute('data-lily-review-zoom'));
				return;
			}
			if (event.target.closest && event.target.closest('[data-lily-review-lightbox-close]')) {
				close();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') { close(); }
		});
	}

	function bootLilyModules() {
		initProductQty();
		initCartDrawer();
		initCheckoutShippingSync();
		initContactForm();
		initBestSellersCarousel();
		initProductAccordions();
		initLilyReviewForms();
		initReviewLightbox();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bootLilyModules);
	} else {
		bootLilyModules();
	}


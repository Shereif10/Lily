/* ==========================================================================
   LILY Wishlist — guest wishlist persisted as product IDs in localStorage.
   Isolated module: never touches cart, filters, sorting, search or i18n
   switching. Event delegation only.
   ========================================================================== */
(function () {
	'use strict';

	var STORAGE_KEY = 'lily_wishlist_v1';
	var SELECTOR = '[data-lily-wishlist-btn]';

	function readIds() {
		var raw = null;

		try {
			raw = window.localStorage.getItem(STORAGE_KEY);
		} catch (err) {
			return [];
		}

		if (!raw) {
			return [];
		}

		var parsed = null;

		try {
			parsed = JSON.parse(raw);
		} catch (err) {
			return [];
		}

		if (!Array.isArray(parsed)) {
			return [];
		}

		var seen = {};
		var ids = [];

		parsed.forEach(function (value) {
			var id = parseInt(value, 10);

			if (!isNaN(id) && id > 0 && !seen[id]) {
				seen[id] = true;
				ids.push(id);
			}
		});

		return ids;
	}

	function writeIds(ids) {
		try {
			window.localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
		} catch (err) {
			/* Storage unavailable (private mode) — wishlist stays session-only. */
		}
	}

	function isSaved(id) {
		return readIds().indexOf(id) !== -1;
	}

	function wishlistConfig() {
		return window.lilyWishlist || {};
	}

	function buttonLabel(saved) {
		var cfg = wishlistConfig();
		return saved ? (cfg.removeLabel || 'Remove from wishlist') : (cfg.addLabel || 'Add to wishlist');
	}

	function paintButton(btn, saved) {
		btn.classList.toggle('is-saved', saved);
		btn.setAttribute('aria-pressed', saved ? 'true' : 'false');
		btn.setAttribute('aria-label', buttonLabel(saved));
	}

	function syncButtons(root) {
		var scope = root || document;
		var ids = readIds();

		Array.prototype.forEach.call(scope.querySelectorAll(SELECTOR), function (btn) {
			var id = parseInt(btn.getAttribute('data-product-id'), 10);

			if (isNaN(id) || id <= 0) {
				return;
			}

			paintButton(btn, ids.indexOf(id) !== -1);
		});
	}

	function countText(n) {
		var cfg = wishlistConfig();
		var template = n === 1 ? (cfg.one || '%s item') : (cfg.other || '%s items');

		return template.replace('%s', String(n));
	}

	/* Wishlist page regions (absent on every other page). */
	function pageRegions() {
		return {
			grid: document.querySelector('[data-lily-wishlist-grid]'),
			empty: document.querySelector('[data-lily-wishlist-empty]'),
			count: document.querySelector('[data-lily-wishlist-count]')
		};
	}

	function updatePageState() {

		var regions = pageRegions();

		if (!regions.grid || !regions.empty) {
			return;
		}

		var cards = regions.grid.querySelectorAll('.lily-product-card');
		var hasItems = cards.length > 0;

		regions.empty.hidden = hasItems;
		regions.grid.hidden = !hasItems;

		if (regions.count) {
			if (hasItems) {
				regions.count.textContent = countText(cards.length);
				regions.count.hidden = false;
			} else {
				regions.count.textContent = '';
				regions.count.hidden = true;
			}
		}
	}

	/* Navbar wishlist count badge — same storage, hidden when empty. */
	function syncNavCount() {
		var badges = document.querySelectorAll('[data-lily-wishlist-nav-count]');

		if (!badges.length) {
			return;
		}

		var n = readIds().length;

		Array.prototype.forEach.call(badges, function (badge) {
			badge.textContent = n > 0 ? String(n) : '';
			badge.hidden = n === 0;
		});
	}

	function toggleProduct(id, btn) {
		var ids = readIds();
		var index = ids.indexOf(id);
		var saved = index === -1;

		if (saved) {
			ids.push(id);
		} else {
			ids.splice(index, 1);
		}

		writeIds(ids);
		syncNavCount();

		/* Keep every rendered heart consistent (grids, related, wishlist). */
		Array.prototype.forEach.call(document.querySelectorAll(SELECTOR), function (other) {
			if (parseInt(other.getAttribute('data-product-id'), 10) === id) {
				paintButton(other, saved);
			}
		});

		/* On the wishlist page, removing drops the card immediately. */
		var regions = pageRegions();

		if (regions.grid && !saved) {
			var card = btn && btn.closest ? btn.closest('.lily-product-card') : null;

			if (card && regions.grid.contains(card)) {
				card.remove();
			}
		}

		updatePageState();
	}

	function renderWishlistPage() {
		var regions = pageRegions();

		if (!regions.grid || !regions.empty) {
			return;
		}

		var ids = readIds();

		if (!ids.length) {
			updatePageState();
			return;
		}

		var cfg = wishlistConfig();

		if (!cfg.restUrl) {
			return;
		}

		fetch(cfg.restUrl + '?ids=' + encodeURIComponent(ids.join(',')), { credentials: 'same-origin' })
			.then(function (response) { return response.json(); })
			.then(function (data) {
				if (!data || !Array.isArray(data.products)) {
					return;
				}

				regions.grid.innerHTML = data.products.map(function (item) { return item.html; }).join('');

				/* Prune IDs of products that no longer exist. */
				if (Array.isArray(data.valid)) {
					writeIds(data.valid.map(function (v) { return parseInt(v, 10); }));
				}

				syncButtons(regions.grid);
				syncNavCount();
				updatePageState();
			})
			.catch(function () {
				/* Quiet failure: leave the calm empty state in place. */
				updatePageState();
			});
	}

	document.addEventListener('click', function (event) {
		var btn = event.target && event.target.closest ? event.target.closest(SELECTOR) : null;

		if (!btn) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();

		var id = parseInt(btn.getAttribute('data-product-id'), 10);

		if (isNaN(id) || id <= 0 || btn.disabled) {
			return;
		}

		toggleProduct(id, btn);
	});

	syncButtons(document);
	syncNavCount();
	renderWishlistPage();

	/* Re-sync hearts if other scripts re-render product grids. */
	document.addEventListener('lily:wishlist:sync', function () {
		syncButtons(document);
	});
}());

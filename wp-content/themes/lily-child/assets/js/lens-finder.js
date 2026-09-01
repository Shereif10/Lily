(function () {
	'use strict';

	var config = window.lilyLensFinder || {};
	var startButton = document.querySelector('[data-lily-lens-start]');
	var app = document.querySelector('[data-lily-lens-finder]');

	if (!startButton || !app) {
		return;
	}

	var i18n = config.i18n || {};
	var selections = {};
	var stepIndex = 0;

	var coloredFlow = ['lens_type', 'prescription', 'look', 'eye_color', 'skin_tone', 'color', 'duration'];
	var clearFlow = ['lens_type', 'prescription', 'duration'];

	function getFlow() {
		return selections.lens_type === 'clear' ? clearFlow : coloredFlow;
	}

	function getOptions(step) {
		var terms = config.terms || {};
		var statics = config.static || {};
		var map = {
			lens_type: terms.lensType || [],
			prescription: terms.prescription || [],
			look: terms.look || [],
			eye_color: statics.eyeColor || [],
			skin_tone: statics.skinTone || [],
			color: terms.color || [],
			duration: terms.duration || []
		};

		return map[step] || [];
	}

	function getLabel(step) {
		return (i18n.questions && i18n.questions[step]) || step;
	}

	function findOptionLabel(step, value) {
		var option = getOptions(step).find(function (item) {
			return item.value === value;
		});

		return option ? option.label : value;
	}

	function renderStep() {
		var flow = getFlow();
		var step = flow[stepIndex];
		var options = getOptions(step);

		app.hidden = false;
		app.innerHTML = [
			'<div class="lily-lens-finder__panel">',
			'<div class="lily-lens-finder__progress">' + String(stepIndex + 1) + ' / ' + String(flow.length + 1) + '</div>',
			'<h3>' + escapeHtml(getLabel(step)) + '</h3>',
			'<p>' + escapeHtml(i18n.chooseOne || '') + '</p>',
			'<div class="lily-lens-options">',
			options.map(function (option) {
				var checked = selections[step] === option.value ? ' aria-pressed="true"' : ' aria-pressed="false"';
				return '<button type="button" class="lily-lens-option"' + checked + ' data-value="' + escapeAttribute(option.value) + '">' + escapeHtml(option.label) + '</button>';
			}).join(''),
			'</div>',
			'<div class="lily-lens-controls">',
			stepIndex > 0 ? '<button type="button" class="lily-button lily-button--secondary" data-action="back">' + escapeHtml(i18n.back || 'Back') + '</button>' : '',
			'<button type="button" class="lily-button lily-button--primary" data-action="next" ' + (selections[step] ? '' : 'disabled') + '>' + escapeHtml(i18n.next || 'Next') + '</button>',
			'</div>',
			'</div>'
		].join('');
	}

	function renderReview() {
		var flow = getFlow();
		var rows = flow.map(function (step) {
			return '<dt>' + escapeHtml(getLabel(step)) + '</dt><dd>' + escapeHtml(findOptionLabel(step, selections[step])) + '</dd>';
		}).join('');

		app.innerHTML = [
			'<div class="lily-lens-finder__panel">',
			'<div class="lily-lens-finder__progress">' + String(flow.length + 1) + ' / ' + String(flow.length + 1) + '</div>',
			'<h3>' + escapeHtml(i18n.review || 'Review your selections') + '</h3>',
			'<dl class="lily-lens-review">' + rows + '</dl>',
			'<label class="lily-lens-confirm"><input type="checkbox" data-lily-confirm> <span>' + escapeHtml(i18n.acknowledge || '') + '</span></label>',
			'<p class="lily-lens-error" data-lily-error hidden>' + escapeHtml(i18n.requiredCheck || '') + '</p>',
			'<div class="lily-lens-controls">',
			'<button type="button" class="lily-button lily-button--secondary" data-action="back">' + escapeHtml(i18n.back || 'Back') + '</button>',
			'<button type="button" class="lily-button lily-button--primary" data-action="find">' + escapeHtml(i18n.find || 'Find My Lenses') + '</button>',
			'</div>',
			'</div>'
		].join('');
	}

	function renderResults(products) {
		var productHtml = products.length ? products.map(function (product) {
			return [
				'<article class="lily-product-card">',
				'<a class="lily-product-card__image" href="' + escapeAttribute(product.url) + '">',
				product.image ? '<img src="' + escapeAttribute(product.image) + '" alt="' + escapeAttribute(product.title) + '" loading="lazy">' : '',
				'</a>',
				'<div class="lily-product-card__content">',
				'<h3><a href="' + escapeAttribute(product.url) + '">' + escapeHtml(product.title) + '</a></h3>',
				'<div class="lily-product-card__price">' + product.price + '</div>',
				'<a class="lily-button lily-button--small" href="' + escapeAttribute(product.url) + '">' + escapeHtml(product.buttonText) + '</a>',
				'</div>',
				'</article>'
			].join('');
		}).join('') : '<p>' + escapeHtml(i18n.noResults || '') + '</p>';

		app.innerHTML = [
			'<div class="lily-lens-finder__panel">',
			'<h3>' + escapeHtml(i18n.resultsTitle || 'Recommended lenses') + '</h3>',
			'<div class="lily-products-row lily-products-row--results">' + productHtml + '</div>',
			'</div>'
		].join('');
	}

	function findProducts() {
		var confirm = app.querySelector('[data-lily-confirm]');
		var error = app.querySelector('[data-lily-error]');

		if (!confirm || !confirm.checked) {
			if (error) {
				error.hidden = false;
			}
			return;
		}

		app.classList.add('is-loading');
		app.insertAdjacentHTML('beforeend', '<p class="lily-lens-loading">' + escapeHtml(i18n.loading || '') + '</p>');

		window.fetch(config.endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				nonce: config.nonce,
				selections: selections
			})
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (data) {
				renderResults(data.products || []);
			})
			.catch(function () {
				renderResults([]);
			})
			.finally(function () {
				app.classList.remove('is-loading');
			});
	}

	startButton.addEventListener('click', function () {
		stepIndex = 0;
		selections = {};
		renderStep();
		app.scrollIntoView({ behavior: 'smooth', block: 'start' });
	});

	app.addEventListener('click', function (event) {
		var option = event.target.closest('.lily-lens-option');
		var action = event.target.closest('[data-action]');
		var flow = getFlow();
		var step = flow[stepIndex];

		if (option) {
			selections[step] = option.getAttribute('data-value');
			if (step === 'lens_type' && selections.lens_type === 'clear') {
				delete selections.look;
				delete selections.eye_color;
				delete selections.skin_tone;
				delete selections.color;
			}
			renderStep();
			return;
		}

		if (!action) {
			return;
		}

		if (action.dataset.action === 'back') {
			stepIndex = Math.max(0, stepIndex - 1);
			renderStep();
		}

		if (action.dataset.action === 'next') {
			if (stepIndex >= flow.length - 1) {
				renderReview();
			} else {
				stepIndex += 1;
				renderStep();
			}
		}

		if (action.dataset.action === 'find') {
			findProducts();
		}
	});

	function escapeHtml(value) {
		return String(value || '').replace(/[&<>"']/g, function (char) {
			return {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			}[char];
		});
	}

	function escapeAttribute(value) {
		return escapeHtml(value).replace(/`/g, '&#096;');
	}
}());


(function ($) {
	'use strict';

	function openMedia($input, $preview) {
		var frame = wp.media({
			title: 'Choose Image',
			button: { text: 'Use Image' },
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
			$input.val(attachment.id);
			$preview.html('<img src="' + thumb + '" alt="">');
		});

		frame.open();
	}

	$(document).on('click', '[data-lily-image-select]', function (event) {
		event.preventDefault();
		var $wrap = $(this).closest('.lily-image-field, .lily-brand-row, td, .form-field');
		openMedia($wrap.find('[data-lily-image-input]').first(), $wrap.find('[data-lily-image-preview]').first());
	});

	$(document).on('click', '[data-lily-image-remove]', function (event) {
		event.preventDefault();
		var $wrap = $(this).closest('.lily-image-field, .lily-brand-row, td, .form-field');
		$wrap.find('[data-lily-image-input]').first().val('');
		$wrap.find('[data-lily-image-preview]').first().empty();
	});

	$(document).on('click', '[data-lily-add-brand]', function (event) {
		event.preventDefault();
		var template = $('#tmpl-lily-brand-row').html();
		var index = Date.now();
		$('[data-lily-brand-rows]').append(template.replaceAll('__INDEX__', index));
	});

	$(document).on('click', '[data-lily-remove-brand]', function (event) {
		event.preventDefault();
		$(this).closest('.lily-brand-row').remove();
	});

	$(document).on('click', '[data-lily-add-announcement]', function (event) {
		event.preventDefault();
		var template = $('#tmpl-lily-announcement-row').html();
		var index = Date.now();
		$('[data-lily-announcement-rows]').append(template.replaceAll('__INDEX__', index));
	});

	$(document).on('click', '[data-lily-remove-announcement]', function (event) {
		event.preventDefault();
		$(this).closest('.lily-announcement-row').remove();
	});

	$(document).on('click', '[data-lily-add-hero]', function (event) {
		event.preventDefault();
		var template = $('#tmpl-lily-hero-row').html();
		var index = Date.now();
		$('[data-lily-hero-rows]').append(template.replaceAll('__INDEX__', index));
	});

	$(document).on('click', '[data-lily-remove-hero]', function (event) {
		event.preventDefault();
		$(this).closest('.lily-hero-row').remove();
	});

	$(document).on('click', '[data-lily-add-best]', function (event) {
		event.preventDefault();
		var template = $('#tmpl-lily-best-row').html();
		var index = Date.now();
		$('[data-lily-best-rows]').append(template.replaceAll('__INDEX__', index));
	});

	$(document).on('click', '[data-lily-remove-best]', function (event) {
		event.preventDefault();
		$(this).closest('.lily-best-row').remove();
	});

	$('[data-lily-brand-rows]').sortable({
		handle: '.dashicons-move'
	});

	$('[data-lily-best-rows]').sortable({
		handle: '.dashicons-move'
	});

	$('[data-lily-hero-rows]').sortable({
		handle: '.dashicons-move'
	});

	$('[data-lily-announcement-rows]').sortable({
		handle: '.dashicons-move'
	});

	$('.lily-admin-tabs a').on('click', function (event) {
		event.preventDefault();
		var target = $(this).attr('href');
		$('.lily-admin-tabs a').removeClass('is-active');
		$(this).addClass('is-active');
		$('.lily-admin-panel').removeClass('is-active');
		$(target).addClass('is-active');
	});

	$('.lily-admin-tabs a').first().trigger('click');
}(jQuery));


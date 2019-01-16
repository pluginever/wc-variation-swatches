/**
 * WC Variation Swatches
 * https://www.pluginever.com
 *
 * Copyright (c) 2018 pluginever
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false */

window.wc_variation_swatches = (function (window, document, $, undefined) {
	'use strict';

	var app = {

		initialize: function () {
			$('#term-wcvs-color').wpColorPicker();
			$(document).on('click', '.wc-variation-swatches-upload-image', wc_variation_swatches.handle_term_image_upload);
			$(document).on('click', '.wc-variation-swatches-remove-image', wc_variation_swatches.remove_term_image);
			$(document).on('submit', '#addtag', wc_variation_swatches.clear_term_add_form);
		},

		handle_term_image_upload: function (e) {
			e.preventDefault();
			var $button = $(this), frame;

			// If the media frame already exists, reopen it.
			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media.frames.downloadable_file = wp.media({
				title: 'Choose an image',
				button: {
					text: 'Use image'
				},
				multiple: false
			});

			// When an image is selected, run a callback.
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();

				$button.siblings('.wc-variation-swatches-term-image').val(attachment.id);
				$button.siblings('.wc-variation-swatches-remove-image').show();
				$button.parent().prev('.wc-variation-swatches-preview').find('img').attr('src', attachment.sizes.thumbnail.url);
			});

			// Finally, open the modal.
			frame.open();

		},

		remove_term_image: function (e) {
			e.preventDefault();
			// var $button = $(this);
		},

		clear_term_add_form:function (e) {
			console.log(e);
			console.log(this);
		}
	};

	$(document).ready(app.initialize);

	return app;

})(window, document, jQuery);

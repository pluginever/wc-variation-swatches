/**
 * WC Variation Swatches
 * https://www.pluginever.com
 *
 * Copyright (c) 2018 pluginever
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false */

(function (window, document, $, undefined) {
	'use strict';

	var app = {

		initialize: function () {
			$('#term-color').wpColorPicker();
			$(document).on('click', '.wc-variation-swatches-upload-image', app.handle_term_image_upload);
			$(document).on('click', '.wc-variation-swatches-remove-image', app.remove_term_image);
			//$(document).on('submit', '#addtag', app.clear_term_add_form);
			app.settings_display();
			$(document).on('click', '.enable_stylesheet_check input[type=checkbox]', app.settings_display);
			//$(document).on('click', '.round-box.wcvs-border-style, .square-box.wcvs-border-style', app.border_style);
		},

		settings_display: function(){

			var $enable_stylesheet_check = $('.enable_stylesheet_check');
			var $checkbox = $('.enable_stylesheet_check input[type=checkbox]');

			if ($checkbox.is(':checked')) {
				$enable_stylesheet_check.nextAll().show(300);
			}else{
				$enable_stylesheet_check.nextAll().hide(300);
			}

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
				$button.parent().prev('.wc-variation-swatches-preview').find('img').attr('src', attachment.sizes.full.url);
			});

			// Finally, open the modal.
			frame.open();

		},

		remove_term_image: function (e) {

			e.preventDefault();

			var $button = $(this);

			$button.siblings('.wc-variation-swatches-term-image').attr('value', '');
			$button.parent().prev('.wc-variation-swatches-preview').find('img').attr('src', wpwvs.placeholder_img);
			$button.hide();

		}



	};

	$(document).ready(app.initialize);

	return app;

})(window, document, jQuery);

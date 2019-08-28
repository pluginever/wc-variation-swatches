(function ($) {
	'use strict';

	var app = {

		initialize: function () {

			app.ever_variation_swatches_form();

			$(document).on('click', '.swatch', app.handle_swatches);
			$(document).on('click', '.reset_variations', app.reset_variations);
			$(document).on('ever_no_matching_variations', app.variation_nomatching);
			$(document).on('ready', app.variation_check);
		},


		ever_variation_swatches_form: function () {
			var variationForm = $('.variations_form');
			variationForm.addClass('ever-swatches-role');

			setTimeout(app.variation_check, 1000);
		},

		handle_swatches: function (e) {

			var variationForm = $('.variations_form');
			var selected = [];

			e.preventDefault();

			var fullData = $(this),
				selectData = fullData.closest('.value').find('select'),
				attribute_name = fullData.closest('.value').children('.wc-ever-swatches').attr('data-attribute_name'),
				optionValue = fullData.data('value');

			selectData.trigger('focusin');

			if (!selectData.find('option[value=\'' + optionValue + '\']').length) {

				fullData.siblings('.swatch').removeClass('selected');

				selectData.val('').change();
				variationForm.trigger('ever_no_matching_variations', [fullData]);

				return;
			}

			if (selected.indexOf(attribute_name) === -1) {
				selected.push(attribute_name);
			}

			if (fullData.hasClass('selected')) {
				selectData.val('');
				fullData.removeClass('selected');

				delete selected[selected.indexOf(attribute_name)];
			} else {
				fullData.addClass('selected').siblings('.selected').removeClass('selected');
				selectData.val(optionValue);
			}

			selectData.change();
		},

		reset_variations: function () {
			$(this).closest('.variations_form').find('.swatch.selected').removeClass('selected');
		},

		variation_nomatching: function () {
			window.alert(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
		},

		variation_check: function () {
			$('.swatch').each(function () {

				var fullData = $(this),
					selectData = fullData.closest('.value').find('select'),
					optionValue = fullData.data('value');

				if (!selectData.find('option[value=\'' + optionValue + '\']').length) {
					fullData.children('.variation_check').addClass('disabled');
				}

			});
		}

	};

	$(document).ready(app.initialize);

	return app;

})(jQuery);

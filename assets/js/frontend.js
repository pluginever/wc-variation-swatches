(function ($) {
	'use strict';

	var app = {

		initialize: function () {

			app.ever_variation_swatches_form();
			app.variation_check();

			$(document).on('click', '.wcvs-swatch', app.handle_swatches);
			$(document).on('click', '.reset_variations', app.reset_variations);
			$(document).on('ever_no_matching_variations', app.variation_nomatching);

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

			var swatch = $(this),
				selectData = swatch.parent().parent().prev().find('select'),
				attribute_name = swatch.parent().parent().attr('data-attribute_name'),
				optionValue = swatch.attr('data-value');

			selectData.trigger('focusin');

			if (!selectData.find('option[value=\'' + optionValue + '\']').length) {
				swatch.siblings().removeClass('selected');

				selectData.val('').change();
				variationForm.trigger('ever_no_matching_variations', [swatch]);
				return;
			}

			if (selected.indexOf(attribute_name) === -1) {
				selected.push(attribute_name);
			}

			if (swatch.hasClass('selected')) {
				selectData.val('');
				swatch.removeClass('selected');

				delete selected[selected.indexOf(attribute_name)];
			} else {
				swatch.addClass('selected').parent().siblings().children().removeClass('selected');
				swatch.removeClass('swatch-hide').find('.variation_check').removeClass('disabled');
				selectData.val(optionValue);
			}
			selectData.change();
			app.variation_check();
		},

		reset_variations: function () {
			$(this).closest('.variations_form').find('.wcvs-swatch.selected').removeClass('selected');
		},

		variation_nomatching: function () {
			window.alert(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
		},

		variation_check: function () {
			$('.wcvs-swatch').each(function () {

				var swatch = $(this),
					selectData = swatch.parent().parent().prev().find('select'),
					optionValue = swatch.attr('data-value');

				if (!selectData.find('option[value=\'' + optionValue + '\']').length) {
					swatch.find('.variation_check').addClass('disabled');

					if (swatch.find('.variation_check').hasClass('hide')) {
						swatch.addClass('swatch-hide');
					}

				} else {
					swatch.removeClass('swatch-hide').find('.variation_check').removeClass('disabled');
				}
			});
		}

	};

	$(document).ready(app.initialize);

	return app;

})(jQuery);

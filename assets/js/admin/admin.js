jQuery(document).ready(function ($) {

	$('.taxonomy-color-field').wpColorPicker();



	$('#term-value_button').on('click', function () {
		var file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Upload Image',
			button: {
				text: 'Insert',
			},
			library: {
				type: ['image']
			},
		});
		file_frame.on('select', function () {
			var images = file_frame.state().get('selection').toJSON();
			var html = images.map(function (image) {
				var imageUrl = image.sizes.thumbnail.url;
				return imageUrl;
			});
			console.log(html[0]);
			$('#term_value').val(html[0]);
		});
		file_frame.open();
	});

});

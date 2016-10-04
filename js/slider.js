jQuery(document).ready(function($){
	'use strict';
	var sliders = $('.cts-slider');
	if (sliders.length ){
		$.each(sliders, function (i, slider) {
			set_slider_height( slider );
            $(slider).slick();
        });
	}


	function set_slider_height( slider ) {
		var height_data = $(slider).data('slider');
		switch ( height_data.height_type ){
			case 'fixed':
				$(slider).css({
					'height': height_data.height_value + 'px'
					//'min-height': height_data.height_value + 'px'
				});
				break;
			case 'auto':
				//$(slider).css('height', 'auto');
				break;
			case 'full':
				$(slider).css('height', '100vh');
				break;

		}
	}

	$('.cts-slide-button ').hover(function () {
		var data = $(this).data('cts-slide-btn');
		if ( 'undefined' !== typeof data ){
			$(this).css({
				'background-color'	: data.btn_bg_color_hover,
				'color'				: data.btn_text_color_hover
			});
		}
	},
	function () {
		var data = $(this).data('cts-slide-btn');

		if ( 'undefined' !== typeof data ){
			$(this).css({
				'background-color'	: data.btn_bg_color,
				'color'				: data.btn_text_color
			});
		}
	});

});
jQuery(document).ready(function($){
	var sliders = $('.cts-slider');
	if (sliders.length ){
		$.each(sliders, function (i, slider) {
			set_slider_height( slider );
            $(slider).slick();
        })

	}


	function set_slider_height( slider ) {
		var height_data = $(slider).data('slider');

		switch ( height_data.height_type ){
			case 'fixed':
				$(slider).children('.cts-slide').css('height', height_data.height_value + 'px');
				break;
			case 'auto':
				$(slider).children('.cts-slide').css('height', 'auto');
				break;
			case 'full':
				$(slider).children('.cts-slide').css('height', '100%');
				break;

		}
	}

});
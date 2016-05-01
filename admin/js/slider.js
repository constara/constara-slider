/**
 * Created by kutas on 21.04.2016.
 */
jQuery(document).ready(function ($) {
    "use strict";
    //media
    $('#get-slide-img-url').click(function () {
        var frame;
        if (frame){
            frame.open();
            return;
        }
        
        frame = wp.media({
            title: 'Choose image',
            multiple: false,
            library: {
                type: 'image'
            },
            button:{
                text: 'Choose'
            }
        });
        frame.open();

        frame.on('select', function(){
            var url_field = $('#cts_slide_img_url');
            var img = $('.img-preview');
            var attachment = frame.state().get('selection').first().toJSON();
            var url = attachment.url;
            url_field.val(url);
            img.attr('src', url);

        });

    });

    $('#cts_slide_img_url').change(function () {
        var url = $(this).val();
        $('.img-preview').attr('src', url);
    });
    
    $('#rm-slide-img-url').click(function () {
        var url_field = $('#cts_slide_img_url');
        var img = $('.img-preview');
        url_field.val('');
        img.attr('src', '');
    });
    //options
    var default_position = 40;//set default value
    var title_position = parseInt($('#cts_slide_title_position').val());
    if(isNaN(title_position)){
        title_position = default_position;
    }
    $('#cts_slide_title_position').val(title_position);
    $('#title-position').slider({
        min: 0,
        max: 70,
        value: title_position,
        slide: function (event, ui) {
            $('#cts_slide_title_position').val(ui.value);
        }
    });

    $('#set_default_title_position').click(function () {
        $('#cts_slide_title_position').val(default_position);
        $('#title-position').slider('option', 'value', default_position);
    });
});
/**
 * Created by kutas on 21.04.2016.
 */
jQuery(document).ready(function ($) {
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
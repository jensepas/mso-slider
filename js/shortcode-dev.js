/*
 * Plugin Name: mso slider
 *
 * Authored by Gael Andre
 *
 * Copyright 2011, Gael Andre
 * License: GNU General Public License, version 3 (GPL-3.0)
 * http://www.opensource.org/licenses/gpl-3.0.html
 *
 */
(function ($) {
    "use strict";
    function generateShortcode(msoSlider) {
        return '[mso_slider slider="' + msoSlider + '"]';
    }
    $('input').on('input', function () {
        $('#mso_slider_script_admin').val(generateShortcode($('input[name=new_post_name]').val()));
    });
    $('#copy').on('click', function () {
        $('#mso_slider_script_admin').select();
        if (document.execCommand('copy')) {
            $('#copy').addClass('copied');
            var temp = setInterval(function () {
                $('#copy').removeClass('copied');
                clearInterval(temp);
            }, 600);
        }
        return false;
    });
    $("#sc_select").change(function () {
        let msoSelect = $('#sc_select').val();
        if (msoSelect === '') return false;
        let shortcode = generateShortcode(msoSelect);
        send_to_editor(shortcode);
        return false;
    });
    $('#mso_slider_script_admin').val(generateShortcode($('input[name=new_post_name]').val()));
})(jQuery);
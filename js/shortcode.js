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
(function(e){"use strict";function n(e){return'[mso_slider slider="'+e+'"]'}e("input").on("input",function(){e("#mso_slider_script_admin").val(n(e("input[name=new_post_name]").val()))}),e("#copy").on("click",function(){if(e("#mso_slider_script_admin").select(),document.execCommand("copy")){e("#copy").addClass("copied");var n=setInterval(function(){e("#copy").removeClass("copied"),clearInterval(n)},600)}return!1}),e("#sc_select").change(function(){let t=e("#sc_select").val();if(""===t)return!1;let i=n(t);return send_to_editor(i),!1}),e("#mso_slider_script_admin").val(n(e("input[name=new_post_name]").val()))})(jQuery);
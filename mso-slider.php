<?php
/*
  Plugin Name: mso slider
  Description: Plugin fournissant un slider
  Author: ms-only
  Version: 1.0.0
  Author URI: https://www.ms-only.fr/
 */

define('MSO_SLIDER_BASENAME', plugin_basename(__FILE__));
require plugin_dir_path(__FILE__) . 'classes/msoSlider.php';
$multilingual = new msoSlider();


if (is_admin()) {
    require plugin_dir_path(__FILE__) . 'classes/msoSliderAdmin.php';
    $multilingual = new msoSliderAdmin();
    add_action('plugins_loaded', 'plugin_init');
    function plugin_init()
    {
        load_plugin_textdomain('mso-slider', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
}
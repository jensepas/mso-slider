<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

function mso_slider_delete_plugin()
{

    $post_types = array('mso_slider', 'mso_slide');
    foreach ($post_types as $post_type) {
        $posts = get_posts(
            array(
                'numberposts' => -1,
                'post_type' => $post_type,
                'post_status' => 'any',
            )
        );

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
    $GLOBALS['wpdb']->query("OPTIMIZE TABLE `" . $GLOBALS['wpdb']->prefix . "posts`");
    $GLOBALS['wpdb']->query("OPTIMIZE TABLE `" . $GLOBALS['wpdb']->prefix . "postmeta`");
}

mso_slider_delete_plugin();
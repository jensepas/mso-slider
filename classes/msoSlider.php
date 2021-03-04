<?php
/*
  Plugin Name: mso slider
  Description: Plugin fournissant un slider
  Author: ms-only
  Version: 1.0.0
  Author URI: https://www.ms-only.fr/
 */


if (!class_exists('msoSlider')) {
    class msoSlider
    {
        public function __construct()
        {
            add_shortcode('mso_slider', array(&$this, 'mso_slider_show'));
        }

        /**
         * @param $args
         * @param int $limit
         * @return string
         */
        public function mso_slider_show($args, $limit = 10)
        {
            global $wpdb;

            wp_enqueue_style('bjqs', plugins_url() . '/' . dirname(MSO_SLIDER_BASENAME) . '/css/bjqs.css', false, '1.1', 'all');

            wp_enqueue_script('bjqs', plugins_url() . '/' . dirname(MSO_SLIDER_BASENAME) . '/js/bjqs-1.3.js', array('jquery'), '1.1', true);

            $slider = $args['slider'];

            $page = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $slider, 'mso_slider'));

            $args['width'] = get_post_meta($page, '_mso_slider_width', true);
            $args['height'] = get_post_meta($page, '_mso_slider_height', true);
            $args['animtype'] = get_post_meta($page, '_mso_slider_animate', true);

            $home = get_post_meta($page, '_mso_slider_home', true);
            $classname = '';

            if ($home != '') $classname = ' class="' . $home . '"';

            add_action('wp_footer', function () use ($args) {
                $this->mso_slider_script($args);
            }, 30, 1);

            $args = array(
                'post_type' => 'mso_slide',
                'post_status' => 'publish',
                'posts_per_page' => $limit,
                'meta_query' => array(
                    'slider' => array(
                        'key' => '_mso_slider',
                        'compare' => 'EXISTS',
                        'value' => $slider
                    )
                ),
                'orderby' => array(
                    'date' => 'ASC',
                ),
            );

            $slides = new WP_query($args);
            $out = '<div id="' . $slider . '"' . $classname . '>' . "\n";
            $out .= '<ul class="bjqs">' . "\n";
            while ($slides->have_posts()) {
                $slides->the_post();
                global $post;
                $out .= '<li>' . "\n";
                //$out .= get_the_post_thumbnail($post->ID);
                $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size = 'full', $icon = false);
                $out .= '<img src="' . $image_attributes[0] . '"  />' . "\n";
                if (get_the_content($post->ID) != '') {
                    $out .= '<div class="desc_slider_home">' . "\n";
                    $out .= get_the_content($post->ID);
                    $out .= '</div>' . "\n";
                }
                $out .= '</li>' . "\n";
            }
            $out .= '</ul>' . "\n";
            $out .= '</div>' . "\n";

            return $out;
        }

        /**
         * @param $slider
         */
        private function mso_slider_script($slider)
        {
            ?>
            <script type="text/javascript">
                (function ($) {
                        $('#<?php echo $slider['slider']; ?>').bjqs({
                            animtype: '<?php echo $slider['animtype']; ?>',
                            height: <?php echo $slider['height']; ?>,
                            width: <?php echo $slider['width']; ?>,
                            responsive: true,
                            showmarkers: false,
                            showcontrols: true,
                            //centermarkers: false,
                            animduration: 450, // how fast the animation are
                            animspeed: 4000 // the delay between each slide
                        });
                    }
                )
                (jQuery);
            </script>
            <?php
        }
    }
}
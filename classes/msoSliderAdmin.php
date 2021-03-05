<?php
/*
  Plugin Name: mso slider
  Description: Plugin fournissant un slider
  Author: ms-only
  Version: 1.0.0
  Author URI: https://www.ms-only.fr/
 */


if (!class_exists('msoSliderAdmin')) {
    class msoSliderAdmin
    {
        public function __construct()
        {
            add_action('init', array(&$this, 'mso_slider_init'));
        }

        /**
         *
         */
        public function mso_slider_init()
        {
            wp_enqueue_style('mso_slider', plugins_url() . '/' . dirname(MSO_SLIDER_BASENAME) . '/css/shortcode.css', false, '1.1', 'all');
            wp_enqueue_script('mso_slider', plugins_url() . '/' . dirname(MSO_SLIDER_BASENAME) . '/js/shortcode.js', null, '1.1', true);

            $label = array(
                'parent_item_colon' => '',
                'name' => __('All Sliders', 'mso-slider'),
                'singular_name' => __('Slider', 'mso-slider'),
                'add_new' => __('Add a Slider', 'mso-slider'),
                'add_new_item' => __('Add a new Slider', 'mso-slider'),
                'edit_item' => __('Edit a Slider', 'mso-slider'),
                'new_item' => __('New Slider', 'mso-slider'),
                'view_item' => __('See the Slider', 'mso-slider'),
                'not_found' => __('No Slider', 'mso-slider'),
                'not_found_in_trash' => __('No Slider in the trash', 'mso-slider'),
            );

            register_post_type('mso_slider', array(
                'public' => false,
                'show_ui' => true,
                'exclude_from_search' => true,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => false,
                'publicly_queryable' => false,
                'query_var' => false,
                'has_archive' => false,
                'labels' => $label,
                'menu_position' => 20,
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-slides',
                'supports' => array('title', 'post_name'),
            ));

            register_post_type('mso_slide',
                array(
                    'labels' => array(
                        'name' => __('All Slides', 'mso-slider'),
                        'singular_name' => __('Slide', 'mso-slider'),
                        'add_new' => __('Add a new Slide', 'mso-slider'),
                        'add_new_item' => __('Add a new Slide', 'mso-slider'),
                        'edit_item' => __('Edit a Slide', 'mso-slider'),
                        'new_item' => __('New Slide', 'mso-slider'),
                        'view_item' => __('See the Slide', 'mso-slider'),
                        'not_found' => __('No Slide', 'mso-slider'),
                        'not_found_in_trash' => __('No Slide in the trash', 'mso-slider'),
                        'menu_position' => 1,
                    ),
                    'public' => false,
                    'show_ui' => true,
                    'exclude_from_search' => true,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => false,
                    'publicly_queryable' => false,
                    'query_var' => false,
                    'has_archive' => false,
                    'capability_type' => 'post',
                    'supports' => array('title', 'editor', 'thumbnail'),
                    'show_in_menu' => 'edit.php?post_type=mso_slider',
                )
            );

            add_action('save_post', array(&$this, 'mso_slider_savepost'), 10, 2);
            add_action('pre_get_posts', array(&$this, 'mso_slider_posts_orderby'));
            add_action('manage_posts_custom_column', array(&$this, 'mso_slider_column'));
            add_action('add_meta_boxes', array(&$this, 'mso_slider_metaboxes_slider'));
            add_action('add_meta_boxes', array(&$this, 'mso_slider_metaboxes_slide'));
            add_action('media_buttons', array(&$this, 'mso_slider_add_slider_select'), 11);

            add_filter('manage_edit-mso_slide_columns', array(&$this, 'mso_slider_columnfilter_slide'));
            add_filter('manage_edit-mso_slide_sortable_columns', array(&$this, 'mso_slider_sortable_columns'));
            add_filter('manage_edit-mso_slider_columns', array(&$this, 'mso_slider_columnfilter_slider'));
        }

        /**
         *
         */
        public function mso_slider_add_slider_select()
        {
            $posts = ($this->get_post_values('mso_slider'));
            echo '<select id="sc_select"><option value="">' . __('Select a slide group', 'mso-slider') . '</option>';
            foreach ($posts as $key => $val) {
                echo '<option value="' . $val->post_name . '">' . $val->post_title . '</option>';
            }
            echo '</select>';
        }

        /**
         *
         */
        public function mso_slider_admin_submenu()
        {
            add_submenu_page(
                'edit.php?post_type=mso_slider',
                __('Add a Slider', 'mso-slider'),
                __('Add a Slider', 'mso-slider'),
                'manage_options',
                'edit.php?post_type=mso_slide',
                array(&$this, 'mso_slider_manage_slider'),
                1
            );
        }

        /**
         * @param $query
         */
        public function mso_slider_posts_orderby($query)
        {
            if (!is_admin() || !$query->is_main_query()) {
                return;
            }
            if ('_mso_slider' === $query->get('orderby')) {
                $query->set('orderby', 'meta_value');
                $query->set('meta_key', '_mso_slider');
            }
        }

        /**
         * @param $columns
         * @return mixed
         */
        public function mso_slider_sortable_columns($columns)
        {
            $columns['_mso_slider'] = '_mso_slider';

            return $columns;
        }

        /**
         * @param $columns
         * @return array
         */
        public function mso_slider_columnfilter_slide($columns)
        {
            $columns = array(
                'cb' => $columns['cb'],
                'title' => __('Title', 'mso-slider'),
                'thumbnail' => __('Image', 'mso-slider'),
                '_mso_slider' => __('Slider', 'mso-slider'),
                'date' => __('Date', 'mso-slider'),
            );

            return $columns;
        }

        /**
         * @param $columns
         * @return array
         */
        public function mso_slider_columnfilter_slider($columns)
        {
            $columns = array(
                'cb' => $columns['cb'],
                'title' => __('Title', 'mso-slider'),
                '_mso_nb_slider' => __('Number of slides', 'mso-slider'),
                '_mso_slider_size' => __('Settings', 'mso-slider'),
                '_mso_slider_shortcode' => __('Shortcode', 'mso-slider'),
                'date' => __('Date', 'mso-slider'),
            );

            return $columns;
        }

        /**
         * @param $column
         */
        public function mso_slider_column($column)
        {
            global $post, $wpdb;
            if ($column === 'thumbnail') {
                echo edit_post_link(get_the_post_thumbnail($post->ID, 'thumbnail'));
            }
            if ($column === '_mso_slider') {
                $post_name = get_post_meta($post->ID, '_mso_slider', true);
                $post = $wpdb->get_var($wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE post_name= %s", $post_name));
                if ($post) {
                    echo $post;
                }
            }
            if ($column === '_mso_nb_slider') {
                $post_slug = get_post_field('post_name', $post->ID);
                $args = array(
                    'post_type' => 'mso_slide',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'slider' => array(
                            'key' => '_mso_slider',
                            'compare' => 'EXISTS',
                            'value' => $post_slug
                        )
                    ),
                );
                $query = new WP_Query($args);
                echo $query->found_posts;
            }
            if ($column === '_mso_slider_shortcode') {
                $post_slug = get_post_field('post_name', $post->ID);
                echo '[mso_slider slider="' . $post_slug . '"]';
            }
            if ($column === '_mso_slider_size') {
                $post_metas = get_post_meta($post->ID);
                $post_metas = array_combine(array_keys($post_metas), array_column($post_metas, '0'));
                $width = $post_metas['_mso_slider_width'];
                $height = $post_metas['_mso_slider_height'];
                $animate = $post_metas['_mso_slider_animate'];
                $class = $post_metas['_mso_slider_class'];
                echo $animate . ' (' . $width . 'X' . $height . ') ';
                if ($class !== '') echo 'class="' . $class . '"';
            }
        }

        /**
         *
         */
        public function mso_slider_metaboxes_slide()
        {
            add_meta_box('msslider', __('Slide grouping', 'mso-slider'), array(&$this, 'mso_slider_metabox_slide'), 'mso_slide', 'normal', 'high');
        }

        /**
         *
         */
        public function mso_slider_metaboxes_slider()
        {
            $post_slug = get_post_field('post_name', get_post());
            if ($post_slug) {
                add_meta_box('msslider', __('Shortcode generator', 'mso-slider'), array(&$this, 'mso_slider_metabox_slider'), 'mso_slider', 'normal', 'high');
            }
        }

        /**
         * @param $object
         */
        public function mso_slider_metabox_slide($object)
        {
            $posts = ($this->get_post_values('mso_slider'));
            ?>
            <div><h4><?php _e('Select a slide group', 'mso-slider'); ?></h4></div>
            <div>

                <select name="mso_slider_slider" id="mso_slider_slider" onchange="generateShortcode()">
                    <?php
                    foreach ($posts as $key => $val) :
                        $select = '';
                        if (get_post_meta($object->ID, '_mso_slider', true) == $val->post_name) {
                            $select = ' selected="selected';
                        }
                        ?>
                        <option<?php echo $select; ?>
                                value="<?php echo $val->post_name; ?>"><?php echo $val->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php
        }

        /**
         * @param $object
         */
        public function mso_slider_metabox_slider($object)
        {
            $post_slug = get_post_field('post_name', get_post());
            $post_metas = get_post_meta(get_post()->ID);
            $post_metas = array_combine(array_keys($post_metas), array_column($post_metas, '0'));
            $mso_slider_slider_width = $post_metas['_mso_slider_width'];
            $mso_slider_slider_height = $post_metas['_mso_slider_height'];
            $mso_slider_slider_animate = $post_metas['_mso_slider_animate'];
            $mso_slider_slider_class = $post_metas['_mso_slider_class'];
            ?>
            <div><h4><?php _e('Generate slider shortcode', 'mso-slider'); ?></h4></div>

            <div>
                <label for="new_post_name"><?php _e('Slug', 'mso-slider'); ?> : </label><input type="text"
                                                                                               id="new_post_name"
                                                                                               name="new_post_name"
                                                                                               value="<?php echo $post_slug; ?>"/>
                <label for="mso_slider_slider_width"><?php _e('Width', 'mso-slider'); ?> : </label><input type="text"
                                                                                                          id="mso_slider_slider_width"
                                                                                                          name="mso_slider_slider_width"
                                                                                                          value="<?php echo $mso_slider_slider_width; ?>"
                                                                                                          placeholder="576"/>
                <label for="mso_slider_slider_height"><?php _e('Height', 'mso-slider'); ?> : </label><input type="text"
                                                                                                            id="mso_slider_slider_height"
                                                                                                            name="mso_slider_slider_height"
                                                                                                            value="<?php echo $mso_slider_slider_height; ?>"
                                                                                                            placeholder="394"/>
                <label for="mso_slider_slider_height"><?php _e('Animation', 'mso-slider'); ?> : </label>
                <select name="mso_slider_slider_animate">
                    <option>slide</option>
                    <option <?php if ($mso_slider_slider_animate === 'fade') echo ' selected="selected"'; ?>>fade
                    </option>
                </select>
                <label for="mso_slider_slider_class"><?php _e('Additional CSS class', 'mso-slider'); ?> : </label><input
                        type="text"
                        name="mso_slider_slider_class" value="<?php echo $mso_slider_slider_class; ?>"/>
            </div>
            <div>
                <label for="mso_slider_script_admin"><?php _e('Code to copy and paste', 'mso-slider'); ?>
                    : </label>
            </div>
            <div>
                <input type="text" style="width:100%" id="mso_slider_script_admin" value=""/>
                <br/><br/>
                <button id="copy" class="button button-primary button-large"
                        type="button"><?php _e('Copy to clipboard', 'mso-slider'); ?><span
                            class="copiedtext" aria-hidden="true"><?php _e('Copied!', 'mso-slider'); ?></span></button>
            </div>
            <?php
        }

        /**
         * @param $post_id
         * @param $post
         * @return mixed
         */
        public function mso_slider_savepost($post_id, $post)
        {
            $type = get_post_type_object($post->post_type);
            if (!current_user_can($type->cap->edit_post)) {
                return $post_id;
            }
            if (!empty($_POST['mso_slider_slider_new'])) {
                $_POST['mso_slider_slider'] = $_POST['mso_slider_slider_new'];
            }
            if (isset($_POST['mso_slider_slider'])) {
                update_post_meta($post_id, '_mso_slider', $_POST['mso_slider_slider']);

                return $post_id;
            }
            $post_slug = get_post_field('post_name', $post_id);
            $new_post_slug = $_POST['new_post_name'];
            if (isset($new_post_slug)) {
                update_post_meta($post_id, '_mso_slider_height', $_POST['mso_slider_slider_height']);
                update_post_meta($post_id, '_mso_slider_width', $_POST['mso_slider_slider_width']);
                update_post_meta($post_id, '_mso_slider_animate', $_POST['mso_slider_slider_animate']);
                update_post_meta($post_id, '_mso_slider_class', $_POST['mso_slider_slider_class']);
                remove_action('save_post', array(&$this, 'mso_slider_savepost'), 10, 2);
                $my_post = array(
                    'ID' => $post_id,
                    'post_name' => $new_post_slug,
                );
                wp_update_post($my_post);
                add_action('save_post', array(&$this, 'mso_slider_savepost'), 10, 2);

                return $post_id;
            }
        }

        /**
         * @param string $type
         * @param string $status
         * @return void
         */
        private function get_post_values($type = 'post', $status = 'publish')
        {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare("
                SELECT post_name, post_title FROM {$wpdb->posts} 
                WHERE post_status = %s 
                AND post_type = %s
            ", $status, $type));
        }
    }
}
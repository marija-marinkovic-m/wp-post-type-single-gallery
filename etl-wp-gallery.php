<?php
/**
* Plugin Name: Wordpress Cycle Gallery Plugin
* Plugin URI: https://bitbucket.org/eutelnet/etl-wp-gallery/overview
* Description: Customizable Wordpress Gallery Plugin
* Author: EUTELNET Team
* Author URI: http://www.eutelnet.biz/
* Version: 1.0
* Text Domain: etl-gallery
*
* Copyright: (c) 2016 eutelnet (marija@eutelnet.biz)
*
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
* @author    EUTELNET team
* @copyright Copyright (c) 2016, EUTELNET
* @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
*
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'WC_ETL_TEMPLATE_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/' );

if ( ! class_exists( 'ETL_WP_Gallery' ) ) {

    /**
     * Localisation
     **/
    load_plugin_textdomain( 'etl-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );

	class ETL_WP_Gallery {
		/**
		* Singleton
		*/
		protected static $_instance = null;

        public $meta_value = 'gallery_media_item';
        public $shortcode = 'etl-gallery';

        public $hidden_qe_flag = 'etl_gallery_hidden_qe_flag';
        public $settings_db_name = 'etlwpgal_settings';
        public $option_group_name = 'etlwpga_plugin_page';
        public $screens = array('post', 'page');

        /**
        * Settings
        */
        public $lightbox = false;
        public $pager = false;
        public $cycle_timeout = 0;
        public $cycle_speed = 700;

		/**
		* Main ETL_WP_Gallery Instance
		* Ensures only one (singleton) Instance of class is loaded or can be loaded.
		* @static
		* @see ETL_Gallery()
		* @return ETL_WP_Gallery - Main instance
		*/
		public static function instance() {
			if ( is_null(self::$_instance) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
            $this->options = (array)get_option($this->settings_db_name);
            $this->lightbox = isset($this->options['etlwpgal_checkbox_lightbox']) ? true : false;
            $this->pager = isset($this->options['etlwpgal_checkbox_pager']) ? true : false;
            $this->cycle_timeout = (isset($this->options['etlwpgal_checkbox_timeout']) && !empty($this->options['etlwpgal_checkbox_timeout'])) ? $this->options['etlwpgal_checkbox_timeout'] : 0;
            $this->cycle_speed = (isset($this->options['etlwpgal_checkbox_speed']) && !empty($this->options['etlwpgal_checkbox_speed'])) ? $this->options['etlwpgal_checkbox_speed'] : 700;

			// include required classes and functions
			$this->includes();
			// hooks
            $this->hooks();
		}

        public function hooks () {
            // called just before the woocommerce template functions are included
			add_action( 'init', array( &$this, 'on_init' ) );
            add_shortcode($this->shortcode, array($this, 'the_shortcode_code'));

			// indicates we are running the admin
			if ( is_admin() ) {
				// add admin styles and scripts
				add_action('admin_enqueue_scripts', array( &$this, 'backend_scripts' ));
                add_action('save_post', array($this, 'save_post'));

                // meta revisions
                add_filter( 'wp_post_revision_meta_keys', array($this, 'add_meta_keys_to_revision') );

                // settings page
                add_action( 'admin_menu', array($this, 'add_admin_menu') );
                add_action( 'admin_init', array($this, 'settings_init') );

			} else {
				// add frontend styles and scripts
				add_action('wp_enqueue_scripts', array( &$this, 'frontend_scripts' ));
			}
        }

        public function add_admin_menu() {
            add_submenu_page( 'options-general.php', 'ETL Gallery Settings', 'ETL Gallery Settings', 'manage_options', 'etlwpgal_admin', array($this, 'options_page_cb') );
        }

        public function settings_init() {
            register_setting( $this->option_group_name, $this->settings_db_name );

            add_settings_section(
                'etlwpgal_pluginpage_section',
                __( 'Gallery Options', 'etl-gallery' ),
                array($this, 'settings_section_cb'),
                'etlwpgal_admin'
            );
            add_settings_field(
                'etlwpgal_checkbox_lightbox',
                __( 'Enable lighbox view for slides?', 'etl-gallery' ),
                array($this, 'lightbox_setting_render'),
                'etlwpgal_admin',
                'etlwpgal_pluginpage_section'
            );
            add_settings_field(
                'etlwpgal_checkbox_pager',
                __( 'Enable carousel pager on galleries?', 'etl-gallery' ),
                array($this, 'pager_setting_render'),
                'etlwpgal_admin',
                'etlwpgal_pluginpage_section'
            );
            add_settings_field(
                'etlwpgal_checkbox_timeout',
                __( 'Slider timeout (in miliseconds)', 'etl-gallery' ),
                array($this, 'timeout_setting_render'),
                'etlwpgal_admin',
                'etlwpgal_pluginpage_section'
            );
            add_settings_field(
                'etlwpgal_checkbox_speed',
                __( 'Sliding speed', 'etl-gallery' ),
                array($this, 'speed_setting_render'),
                'etlwpgal_admin',
                'etlwpgal_pluginpage_section'
            );
        }

        public function settings_section_cb() {
            echo __( 'Customize Gallery Views', 'etl-gallery' );
        }
        public function lightbox_setting_render() {
            $options = get_option($this->settings_db_name);
            ?>
            <input type="checkbox" name="<?php echo $this->settings_db_name ?>[etlwpgal_checkbox_lightbox]" <?php checked($options['etlwpgal_checkbox_lightbox']) ?> value="1" />
            <?php
        }
        public function pager_setting_render() {
            $options = get_option($this->settings_db_name);
            ?>
            <input type="checkbox" name="<?php echo $this->settings_db_name ?>[etlwpgal_checkbox_pager]" <?php checked($options['etlwpgal_checkbox_pager']) ?> value="1" />
            <?php
        }
        public function timeout_setting_render() {
            $options = get_option($this->settings_db_name);
            $timeout = empty($options['etlwpgal_checkbox_timeout']) ? '0' : $options['etlwpgal_checkbox_timeout']; // default timeout 0
            ?>
            <input type="number" name="<?php echo $this->settings_db_name ?>[etlwpgal_checkbox_timeout]" value="<?php echo $timeout ?>" />
            <?php
        }
        public function speed_setting_render() {
            $options = get_option($this->settings_db_name);
            $speed = empty($options['etlwpgal_checkbox_speed']) ? '700' : $options['etlwpgal_checkbox_speed']; // default speed 700
            ?>
            <input type="number" name="<?php echo $this->settings_db_name ?>[etlwpgal_checkbox_speed]" value="<?php echo $speed ?>" />
            <?php
        }

        public function options_page_cb() {
            ?>
            <form action="options.php" method="post">
                <?php
                settings_fields( $this->option_group_name );
                do_settings_sections( 'etlwpgal_admin' );
                submit_button();
                ?>
            </form>
            <?php
        }

        public function add_meta_keys_to_revision( $keys ) {
            $keys[] = $this->meta_value;
            return $keys;
        }

		/**
		* Include required core files used in admin and on the frontend
		*/
		public function includes() {
			// libs
			include_once('libs/wp-editor-select-gallery.php');
		}

        /**
        * On init functions
        * register_post_type
        */
        public function on_init () {
            add_action('add_meta_boxes', array($this, 'gallery_ui_callback'));
        }

		/**
		* Load Backend styles and scripts method
		*/
		public function backend_scripts () {
			wp_register_style('etl-gallery-backend-styles', plugin_dir_url(__FILE__) . 'assets/css/backend.css');
			wp_enqueue_style('etl-gallery-backend-styles');

            wp_enqueue_media( );
			wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-dialog');

            wp_enqueue_script('mustache', plugin_dir_url(__FILE__) . 'assets/node_modules/mustache/mustache.min.js', array(), false, true);

			wp_enqueue_script('etl_gallery_backend_script', plugin_dir_url(__FILE__) . 'assets/js/backend.js', array('mustache'), false, true);

			// In JS, object properties are accessed as ajax_object.ajax_url
			wp_localize_script( 'etl_gallery_backend_script', 'be_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
		/**
		* Load Frontend styles and scripts
		*/
		public function frontend_scripts () {
            // styles
			wp_register_style('etl-gallery-frontent-styles', plugin_dir_url(__FILE__) . 'assets/css/frontend.css');
			wp_enqueue_style('etl-gallery-frontent-styles');

            // scripts
            // If jQuery isn't already enqueued, register and enqueue it
            wp_enqueue_script('jquery');

            wp_enqueue_script('malsup-cycle', plugin_dir_url(__FILE__) . 'assets/js/libs/jquery.cycle2.min.js', array('jquery'), false, true);
            wp_enqueue_script('maslup-cycle', plugin_dir_url(__FILE__) . 'assets/js/libs/jquery.cycle2.caption2.min.js', array('malsup-cycle'), false, true);
            wp_enqueue_script('maslup-swipe', plugin_dir_url(__FILE__) . 'assets/js/libs/jquery.cycle2.swipe.min.js', array('malsup-cycle'), false, true);

            // admin: plugin settings page (option show pager)
            if ($this->pager) {
                wp_enqueue_script('malsup-caro', plugin_dir_url(__FILE__) . 'assets/js/libs/jquery.cycle2.carousel.min.js', array('malsup-cycle'), false, true);
                wp_enqueue_script('frontendjs', plugin_dir_url(__FILE__) . 'assets/js/frontend.js', array('malsup-cycle'), false, true);
            }

            // admin: add plugin settings page (option include lightbox)
            // check if already included
            if ($this->lightbox && !wp_script_is('lightcase.js') && !wp_style_is('lightcase.css')) {
                wp_register_style('lightcase.css', plugin_dir_url(__FILE__) . 'assets/css/lightcase.css');
                wp_enqueue_style('lightcase.css');
                wp_register_script('lightcase.js', plugin_dir_url(__FILE__) . 'assets/js/libs/lightcase.js', array('jquery'), false, true);
                wp_enqueue_script('lightcase.js');
            }

            // finally
            wp_enqueue_script('etl-gallery-init', plugin_dir_url(__FILE__) . 'assets/js/init.js', array('jquery'), false, true);

		}


        public function gallery_ui_callback ($post) {
            foreach ($this->screens as $screen) {
                add_meta_box(
                    'etl_gallery_ui',
                    'Gallery images: ',
                    array($this, 'render_meta'),
                    $screen,
                    'normal',
                    'high'
                );
            }
        }

        public function render_meta($post) {
            include('views/gallery-placeholder.php');
            printf('<input type="hidden" value="1" name="%s" />', $this->hidden_qe_flag);
        }

        public function save_post ($post_id) {

            if ( isset($_POST[$this->hidden_qe_flag]) ) :
                // exit on autosave
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
                    return $post_id;
                }

                //update or delete
                if(isset($_POST[$this->meta_value])) {
                    $data = $_POST[$this->meta_value];
                    update_post_meta($post_id, $this->meta_value, $data);
                } else {
                    delete_post_meta($post_id, $this->meta_value);
                }
            endif; // hidden flag detected

        }

        public function the_shortcode_code ($atts) {
            extract(shortcode_atts(array(
                'id' => '',
                'title' => '',
            ), $atts));

            if ( 'publish' === get_post_status(intval($id)) ) {
                $attachments = get_post_meta( intval($id), ETL_Gallery()->meta_value, true );
                if ( !empty($attachments) ) :
                    include('views/shortcode-gallery.php');
                    return ob_get_clean();
                endif;
            } else {
                return false;
            }
        }

        /**
        * Data gettr
        */
        public static function get_slides ( $id, $bgr_image = false, $image_size = 'thumbnail' ) {
            ob_start();
            $get_attachments = get_post_meta( intval($id), ETL_Gallery()->meta_value, true );
            $cnt = 0;
            if (!empty($get_attachments)) :
                foreach ($get_attachments as $attached) {
                    $pid = $attached['id'];
                    $attachment = get_post( $pid );
                    $image = wp_get_attachment_image_src( $pid, $image_size );

                    // FIELDS
                    $title = $attachment->post_title;
                    $caption = $attachment->post_excerpt;
                    $alt = get_post_meta( $pid, '_wp_attachment_image_alt', true );

                    $image_src = false !== $image ? $image[0] : '';
                    $bgr_image = $bgr_image;
                    include('views/single-slide.php');
                    $cnt++;
                }
            endif;

            return ob_get_clean();
        }

	} // end class declaration

}

/**
* Returns the main instance of WC_Etl to prevent the need to use Globals.
* @return ETL_WP_Gallery
*/
function ETL_Gallery() {
	return ETL_WP_Gallery::instance();
}

ETL_Gallery();

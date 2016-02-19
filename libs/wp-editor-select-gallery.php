<?php
/**
* ETL Gallery plugin selectbox
*/
class ETL_Gallery_Editor_Select {
    public static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->hooks();
    }

    public function hooks () {
        add_action('media_buttons', array($this, 'get_data_for_etl_gallery_select'), 12);
    }

    public function get_data_for_etl_gallery_select() {
        $res = $this->get_db_posts();
        if ($res) {
            echo $this->render_etl_gallery_select($res);
        } else {
            return false;
        }
    }

    public function get_db_posts () {
        global $wpdb;
        $query_string = "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='%s' AND post_status='publish'";

        return $wpdb->get_results(
            $wpdb->prepare($query_string, ETL_Gallery()->post_type)
        );
    }

    public function render_etl_gallery_select ($data) {
        ob_start();
        ?>
        <select id="gallery-select-sc">
            <option>ETL Gallery</option>
            <?php foreach ($data as $post_obj) {
                printf(
                    '<option value="%s">%s</option>',
                    ETL_Gallery()->get_scode_construct($post_obj->ID),
                    $post_obj->post_title
                );
            } ?>
        </select>
        <?php
        return ob_get_clean();
    }
}

function ETL_Gallery_Editor_Select () {
    return ETL_Gallery_Editor_Select::instance();
}

ETL_Gallery_Editor_Select();

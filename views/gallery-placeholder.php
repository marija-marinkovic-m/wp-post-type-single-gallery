<?php
ob_start();

    $post_id = $post->ID;
    $data = (array)get_post_meta( $post_id, $this->meta_value, true );

    $first_el = key($data);
    if ( !isset($data[$first_el]['id']) || empty($data[$first_el]['id']) ) {
        $data = '';
    }

    if (!empty($data)) :
        array_walk($data, function (&$item, $key) {
            $attachment = get_post( intval($item['id']) );
            $alt = get_post_meta( $item['id'], '_wp_attachment_image_alt', true );
            $image = wp_get_attachment_image_src( $item['id'], 'thumbnail' );
            // $item['i'] = $key;
            // get fields (title, url, caption, alt)
            $item['title'] = $attachment->post_title;
            $item ['caption'] = $attachment->post_excerpt;
            $item['alt'] = $alt;
            $item['url'] = false !== $image ? $image[0] : '';
        });
        $data = array_values($data);
    endif;

?>

<div>
    <a class="button" href="#" id="feat_media_button" data-btn-text="Insert Selected" data-box-title="Select image/images for the slider">
        <span class="dashicons dashicons-format-gallery"></span>
        Add Images
    </a>
    <a class="button" href="#" id="remove_all">Delete All</a>

    <script type="text/javascript">
        var etlGalleryData = {
            count: <?php echo count($data) + 1 ?>,
            pid: "<?php echo $post_id ?>",
            metaValue: "<?php echo $this->meta_value ?>"
        };
        var galleryItems = {
            items: <?php echo !empty($data) ? json_encode($data) : 'false' ?>
        };
    </script>

    <div id="target">Loading...</div>
    <ul id="image-list">
    </ul>
    <script id="template" type="x-tmpl-mustache">
        {{#items}}
            <li id="image-{{ id }}">
                <span class="dashicons dashicons-dismiss" title="Delete Item"></span>
                <span class="dashicons dashicons-edit" title="Edit Item" data-id="{{ id }}"></span>
                <img src="{{ url }}" />
                <input type="hidden" name="<?php echo $this->meta_value ?>[][id]" value="{{ id }}" />
            </li>
        {{/items}}
    </script>
</div>

<?php
echo ob_get_clean();

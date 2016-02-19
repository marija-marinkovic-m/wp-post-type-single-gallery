<a
     class="panel"
     <?php echo $bgr_image && ETL_Gallery()->lightbox ? 'data-rel="lightcase:gallery-'. get_the_ID() .'-'. $id .'" href="'. $image_src .'"' : '' ?>
     <?php echo (!empty($title)) ? 'title="'. $title .'" data-cycle-desc="'. $caption .'"' : 'data-cycle-overlay-template=""' ?>
     data-slider-id="<?php echo $id ?>"
     data-hard-index="<?php echo $cnt ?>"
     <?php echo $bgr_image ? 'style="background-image: url('. $image_src .')" data-background="'. $image_src .'"' : '' ?>>
    <img src="<?php echo $image_src ?>" width="<?php echo $image[1] ?>" height="<?php echo $image[2] ?>" alt="<?php echo $alt ?>">
</a>

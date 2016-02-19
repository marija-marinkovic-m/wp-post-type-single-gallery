<?php
ob_start();
?>
<div class="etl-slider">
    <div id="slideshow-<?php echo $id ?>" class="slides">

        <div class="main-holder cycle-slideshow cycle-slideshow-<?php echo $id ?>" data-slider="<?php echo $id ?>"
            data-cycle-slides="> .panel"
            data-cycle-timeout="<?php echo $this->cycle_timeout ?>"
            data-cycle-speed="<?php echo $this->cycle_speed ?>"
            data-cycle-fx="fade"
            data-cycle-caption-plugin="caption2"
            data-cycle-overlay-fx-out="slideUp"
            data-cycle-overlay-fx-in="slideDown"
            data-cycle-swipe="true"
            data-cycle-log="false"
            >
            <div class="cycle-overlay"></div>

            <?php echo forward_static_call_array(array('ETL_WP_Gallery', 'get_slides'), array($id, true, 'large')); ?>

        </div>
        <?php if ($this->pager) : ?>
        <div id="template-pager-<?php echo $id ?>" class="cycle-pager external caro-pager">
            <?php echo forward_static_call_array(array('ETL_WP_Gallery', 'get_slides'), array($id)); ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($this->pager) : ?>
    <div id="caro-pager-<?php echo $id ?>" class="caro-pager">
        <div class="cycle-slideshow cycle-slideshow-<?php echo $id ?>" data-slider="<?php echo $id ?>"
            data-cycle-slides="> .panel"
            data-cycle-timeout="0"
            data-cycle-prev="#caro-pager-<?php echo $id ?> .cycle-prev"
            data-cycle-next="#caro-pager-<?php echo $id ?> .cycle-next"
            data-cycle-fx="carousel"
            data-allow-wrap="true"
            data-cycle-log="false"
            data-cycle-carousel-visible="5"
            data-cycle-carousel-fluid=true
            data-cycle-swipe="true"
            >
            <?php echo forward_static_call_array(array('ETL_WP_Gallery', 'get_slides'), array($id)); ?>

        </div>
        <div class="cmd">
            <p id="prev-next-<?php echo $id ?>" class="prev-next">
                <a href="#" class="cycle-prev"><span class="icon"></span></a>
                <a href="#" class="cycle-next"><span class="icon"></span></a>
            </p>
        </div>
    </div>
    <?php endif; ?>

</div><!-- etl slider wrapper -->

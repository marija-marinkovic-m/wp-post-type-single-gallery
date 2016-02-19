jQuery(document).ready(function ($) {

    // set size of a main container
    // ratio 1:1.6
    var mapEls = $('.main-holder.cycle-slideshow');

    function mainSlideContainer (elCollection) {
        elCollection.each(function () {
            var calcHeight = $(this).width() * 0.625;
            $(this).find('a.panel').css('height', calcHeight);
        });
    }
    mainSlideContainer(mapEls);
    $(window).resize(function() {
        mainSlideContainer(mapEls);
    });

    if (typeof lightcase == "object") {
        $('a[data-rel^=lightcase]').lightcase();
    }
});

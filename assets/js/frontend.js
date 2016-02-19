var $ = jQuery.noConflict();
var minCaroSlides = 15; // 3 * 5
$(document).ready(function () {

    var slideshows = $('.cycle-slideshow').on('cycle-next cycle-prev', function(e, opts) {
        // advance the other slideshow
        var currID = $(this).data('slider');
        $('.cycle-slideshow-' + currID).not(this).cycle('goto', opts.currSlide);
    });
    
    $('body').on("click", ".caro-pager .cycle-slide", function() {
        var strIndex = $(this).data('hardIndex'), 
            currId = $(this).parents('.cycle-slideshow').data('slider');
        $(".cycle-slideshow-" + currId).not(this).cycle('goto', strIndex);
    });

    $('[id^="caro-pager-"] .cycle-slide').on('click', function () {
        var currID = $(this).data('slider-id'),
            index = $('#caro-pager-' + currID).find('.cycle-slideshow').data('cycle.API').getSlideIndex(this);
        $('.cycle-slideshow-' + currID).cycle('goto', index);
    });

    $('.caro-pager .cycle-slideshow').each(function () {
        var totalSlides = $(this).find('.panel').length,
            currId = $(this).data('slider'),
            currPager = '#template-pager-' + currId, 
            prevNext = '#prev-next-' + currId;

        if (totalSlides <= minCaroSlides) {
            // removes carousel pager and prev next ctrls
            $(this).parent().remove();
            $(prevNext).remove();

            $(window).load(function() {
                var currCycle = $('#slideshow-' + currId).find('.cycle-slideshow'),
                    cHeight = currCycle.height();

                // builds alt pager
                currCycle.cycle('destroy');
                currCycle.cycle({
                    fx: 'scrollHorz',
                    timeout: 0,
                    pager: currPager,
                    pagerTemplate: ""
                });

                currCycle.height(cHeight);

                $('[data-background]').each(function() {
                    var image = $(this).data('background');
                    $(this).css( {'background-image': 'url(' + image + ')'} );
                });
            });

        } else {
            // removes alt pager
            $(currPager).remove();
        }
    });

});

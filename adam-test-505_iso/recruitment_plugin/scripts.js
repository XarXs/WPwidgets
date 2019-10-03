(function($) {

    $( window ).bind("load", function() {
        $('.js-grid').isotope({
            // options
            itemSelector: '.grid-item',
            layoutMode: 'masonry',
            masonry: {
                columnWidth: 50,
                horizontalOrder: true
            }
        });
    });

    $(document).ready(function() {
        $('.js-member').magnificPopup({
            type: 'image',
            gallery:{
                enabled:true
            }
        });
    });
})(jQuery);
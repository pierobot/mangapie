/*
$swipe_prev_direction = 'left';
$swipe_next_direction = 'right';

$(function () {
    new Hammer($(".swipe")[0], {
        domEvents: true
    });

    $('.swipe').on('swipeleft', function(e) {
        if ($swipe_prev_direction === 'left') {
            // swipe left for previous image
            $prev_url = $('.swipe').parent().attr('prev_url');
                    
            if ($prev_url != null) {
                // change the href for desktops
                $('.swipe').parent().attr({ href : $prev_url });
                // change the location for mobile devices
                window.location = $prev_url;
            }
        }
    });

    $('.swipe').on('swiperight', function(e) {
        if ($swipe_next_direction === 'right') {
            // swipe right for next image
            $next_url = $('.swipe').parent().attr('href');

            if ($next_url != null) {
                $('.swipe').parent().attr({ href : $next_url });
                window.location = $next_url;
            }
        }
    });

});
*/
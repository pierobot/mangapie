$(function () {

    // set up handler for key events
    $(document).on('keyup', function (e) {
        if (e.keyCode == 37 || e.keyCode == 65) {
            // left arrow or a
            window.location = $('#next-image').attr('href');

        } else if (e.keyCode == 39 || e.keyCode == 68) {
            // right arrow or d
            window.location = $('#prev-image').attr('href');
        }
    });

    // set up handler for preloading images
    // this method will allow the first image to be loaded faster as it
    // will not share bandwidth with the preloaded images
    $(document).ready(function (e) {
        // go through each img in div#preload and load it
        $('#preload > img').each(function () {
            $(this).attr('src', $(this).attr('data-src'));
        })
    });
});

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
jQuery(document).ready(function($) {

    $('.container-slider').css('visibility', 'visible');

    var sliderTimeout = parseInt(sliderSettings.timeout);
    var sliderSpeed = parseInt(sliderSettings.speed);

    var adjustShowcase = function() {
        var maxShowcaseWidth = 1920;
        var thresholdShowcaseWidth = 1600; //1408 1595 1200
        var minShowcaseWidth = 980;

        var windowWidth = $(window).width();

        if (windowWidth <= minShowcaseWidth)
            windowWidth = minShowcaseWidth;
        else if (windowWidth >= maxShowcaseWidth) {
            windowWidth = maxShowcaseWidth;
        }

        var imgCenteredH = 0;

        if (windowWidth <= thresholdShowcaseWidth) {
            imgCenteredH = (windowWidth / 2) - (thresholdShowcaseWidth / 2);
            windowWidth = thresholdShowcaseWidth;
            $('#showcase').find('img').css('top', 0);
        }
        $('#showcase').find('img').css('width', windowWidth + 'px');
        $('#showcase').find('img').css('margin-left', imgCenteredH + 'px');

        if (windowWidth > minShowcaseWidth) {
            var showcaseHeight = $('#showcase').height();
            var showcaseImgHeight = $('#showcase img:visible').height();
            var liTop = showcaseHeight - showcaseImgHeight;

            $('.rslides li').css('top', liTop + "px");
        }
    };

    $('.rslides').responsiveSlides({
        auto: false, // Boolean: Animate automatically, true or false
        speed: sliderSpeed, // Integer: Speed of the transition, in milliseconds
        timeout: sliderTimeout, // Integer: Time between slide transitions, in milliseconds
        pager: false, // Boolean: Show pager, true or false
        nav: true, // Boolean: Show navigation, true or false
        random: false, // Boolean: Randomize the order of the slides, true or false
        pause: false, // Boolean: Pause on hover, true or false
        pauseControls: true // Boolean: Pause when hovering controls, true or false
    });


    adjustShowcase();
    jQuery(window).resize(adjustShowcase);
});
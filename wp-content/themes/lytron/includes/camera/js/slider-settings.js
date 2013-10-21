jQuery(document).ready(function($) {

    var timeout = sliderSettings.timeout;
    var speed = sliderSettings.speed;
    var effect = sliderSettings.effect;
    var autoplay = (sliderSettings.autoplay === 'true') ? true : false;

    jQuery('.camera_wrap').camera({
        height: '600px', //px or % for responsive design
        //minHeight: '300px',  //set this if height is in percent
        pagination: true,
        thumbnails: false,
        navigation: true,
        easing: false,
        fx: effect,
        time: parseInt(timeout),
        transPeriod: parseInt(speed),
        autoAdvance: autoplay,
        alignment: 'bottomCenter'   //topLeft, topCenter, topRight, centerLeft, center, centerRight, bottomLeft, bottomCenter, bottomRight
    });

});

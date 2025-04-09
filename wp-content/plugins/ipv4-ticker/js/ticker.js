jQuery(document).ready(function() {
    var tickerWrapper = jQuery(".tickerwrapper");
    var list = tickerWrapper.find("ul.prior-list");
    var clonedList = list.clone();
    var listWidth = 10;
 
    list.find("li").each(function(i) {
        listWidth += jQuery(this, i).outerWidth(true);
    });

    var endPos = tickerWrapper.width() - listWidth;

    list.add(clonedList).css({
        "width": listWidth + "px"
    });

    clonedList.addClass("cloned").appendTo(tickerWrapper);


    //TimelineMax



    //var time = 60;

    loadSlider();

    function loadSlider() {
        var infinite = new TimelineMax({repeat: -1, paused: true});
        var time = jQuery("#slider").val();

        infinite
            .fromTo(list, time, {rotation: 0.01, x: 0}, {force3D: true, x: -listWidth, ease: Linear.easeNone}, 0)
            .fromTo(clonedList, time, {rotation: 0.01, x: listWidth}, {
                force3D: true,
                x: 0,
                ease: Linear.easeNone
            }, 0)
            .set(list, {force3D: true, rotation: 0.01, x: listWidth})
            .to(clonedList, time, {force3D: true, rotation: 0.01, x: -listWidth, ease: Linear.easeNone}, time)
            .to(list, time, {force3D: true, rotation: 0.01, x: 0, ease: Linear.easeNone}, time)
            .progress(1).progress(0)
            .play();

//Pause/Play
        tickerWrapper.on("mouseenter", function() {
            infinite.pause();
        }).on("mouseleave", function() {
            infinite.play();
        });
    }

    jQuery("#slider").on('change', function() {

        time = jQuery("#slider").val();
        //console.log(time);
        loadSlider();
        //jQuery(".body").off(loadSlider())


        //return loadSlider();

    });


    jQuery( ".the-items-new2" ).each(function( index ) {
        if(jQuery(this).text() === '/17 '){
            jQuery(this).parent().css('order','1');
        }
        if(jQuery(this).text() === '/18 '){
            jQuery(this).parent().css('order','2');
        }
        if(jQuery(this).text() === '/19 '){
            jQuery(this).parent().css('order','3');
        }
        if(jQuery(this).text() === '/20 '){
            jQuery(this).parent().css('order','4');
        }
        if(jQuery(this).text() === '/21 '){
            jQuery(this).parent().css('order','5');
        }
        if(jQuery(this).text() === '/22 '){
            jQuery(this).parent().css('order','6');
        }
        if(jQuery(this).text() === '/23 '){
            jQuery(this).parent().css('order','7');
        }
        if(jQuery(this).text() === '/24 '){
            jQuery(this).parent().css('order','8');
            console.log(index);
        }
    });



});

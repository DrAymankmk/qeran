/*
| ----------------------------------------------------------------------------------
| TABLE OF CONTENT
| ----------------------------------------------------------------------------------
-SETTING
-Preloader
-Scroll Animation
-Parallax(Stellar)
-Chars Start
-Loader blocks
-Accordion
-Tooltip
-Zoom Images
-Isotope filter
-Select customization
-Main slider
-Bxslider
-OWL Sliders
*/



$(document).ready(function() {

    "use strict";


/////////////////////////////////////////////////////////////////
// SETTING
/////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////
// Preloader
/////////////////////////////////////////////////////////////////


    var $preloader = $('#page-preloader'),
    $spinner   = $preloader.find('.spinner-loader');
    $spinner.fadeOut();
    $preloader.delay(50).fadeOut('slow');


/////////////////////////////////////
//  Scroll Animation
/////////////////////////////////////


if ($('.scrollreveal').length > 0) {
    window.sr = ScrollReveal({
        reset:true,
        duration: 1000,
        delay: 200
    });

    sr.reveal('.scrollreveal');
  }



//////////////////////////////
// Parallax(Stellar)
//////////////////////////////

if ($('.stellar').length > 0) {
    $.stellar({
        responsive: true
    });
}


/////////////////////////////////////
//  Chars Start
/////////////////////////////////////


if ($('body').length) {
    $(window).on('scroll', function() {
        var winH = $(window).scrollTop();

        $('.b-progress-list').waypoint(function() {
            $('.js-chart').each(function() {
                CharsStart();
            });
        }, {
            offset: '80%'
        });
    });
}


function CharsStart() {

    $('.js-chart').easyPieChart({
        barColor: false,
        trackColor: false,
        scaleColor: false,
        scaleLength: false,
        lineCap: false,
        lineWidth: false,
        size: false,
        animate: 5000,

        onStep: function(from, to, percent) {
            $(this.el).find('.js-percent').text(Math.round(percent));
        }
    });
}



/////////////////////////////////////
//  Loader blocks
/////////////////////////////////////


    $( ".js-scroll-next" ).on( "click", function() {

        var hiddenContent =  $( ".js-scroll-next + .js-scroll-content") ;

        $(".js-scroll-next").hide() ;
        hiddenContent.show() ;
        hiddenContent.addClass("animated");
        hiddenContent.addClass("animation-done");
        hiddenContent.addClass("bounceInUp");

    });



/////////////////////////////////////////////////////////////////
// Accordion
/////////////////////////////////////////////////////////////////

    $(".btn-collapse").on('click', function () {
            $(this).parents('.panel-group').children('.panel').removeClass('panel-default');
            $(this).parents('.panel').addClass('panel-default');
            if ($(this).is(".collapsed")) {
                $('.panel-title').removeClass('panel-passive');
            }
            else {$(this).next().toggleClass('panel-passive');
        };
    });




/////////////////////////////////////
//  Tooltip
/////////////////////////////////////


    $('.link-tooltip-1').tooltip({
    template: '<div class="tooltip tooltip-1" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
  });
    $('.link-tooltip-2').tooltip({
    template: '<div class="tooltip tooltip-2" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
  });





/////////////////////////////////////
//  Zoom Images
/////////////////////////////////////



  if ($('.js-zoom-gallery').length > 0) {
      $('.js-zoom-gallery').each(function() { // the containers for all your galleries
          $(this).magnificPopup({
              delegate: '.js-zoom-gallery__item', // the selector for gallery item
              type: 'image',
              gallery: {
                enabled:true
              },
        mainClass: 'mfp-with-zoom', // this class is for CSS animation below

        zoom: {
          enabled: true, // By default it's false, so don't forget to enable it

          duration: 300, // duration of the effect, in milliseconds
          easing: 'ease-in-out', // CSS transition easing function

          // The "opener" function should return the element from which popup will be zoomed in
          // and to which popup will be scaled down
          // By defailt it looks for an image tag:
          opener: function(openerElement) {
            // openerElement is the element on which popup was initialized, in this case its <a> tag
            // you don't need to add "opener" option if this code matches your needs, it's defailt one.
            return openerElement.is('img') ? openerElement : openerElement.find('img');
          }
        }
          });
      });
    }


  if ($('.js-zoom-images').length > 0) {
      $('.js-zoom-images').magnificPopup({
        type: 'image',
        mainClass: 'mfp-with-zoom', // this class is for CSS animation below

        zoom: {
          enabled: true, // By default it's false, so don't forget to enable it

          duration: 300, // duration of the effect, in milliseconds
          easing: 'ease-in-out', // CSS transition easing function

          // The "opener" function should return the element from which popup will be zoomed in
          // and to which popup will be scaled down
          // By defailt it looks for an image tag:
          opener: function(openerElement) {
            // openerElement is the element on which popup was initialized, in this case its <a> tag
            // you don't need to add "opener" option if this code matches your needs, it's defailt one.
            return openerElement.is('img') ? openerElement : openerElement.find('img');
          }
        }
      });

    }



////////////////////////////////////////////
// ISOTOPE FILTER
///////////////////////////////////////////


  if ($('.b-isotope').length > 0) {

    var $container = $('.b-isotope-grid');

    // init Isotope
    var $grid = $('.grid').isotope({
      itemSelector: '.grid-item',
      percentPosition: true,
      masonry: {
        columnWidth: '.grid-sizer'
      }
    });
    // layout Isotope after each image loads
    $grid.imagesLoaded().progress( function() {
      $grid.isotope('layout');
    });

    // filter items when filter link is clicked
    $('.b-isotope-filter a').on( 'click', function() {
        var selector = $(this).attr('data-filter');
        $container.isotope({
            filter: selector
        });
        return false;
    });

    $('.b-isotope-filter a').on( 'click', function() {
          $('.b-isotope-filter').find('.current').removeClass('current');
          $( this ).addClass('current');
        });
  }




/////////////////////////////////////
// Select customization
/////////////////////////////////////

if ($('.selectpicker').length > 0) {

  $('.selectpicker').selectpicker({
    style: 'ui-select'
  });
}



////////////////////////////////////////////
// Main slider
///////////////////////////////////////////

    if ($('#main-slider').length > 0) {

        var sliderWidth = $("#main-slider").data("slider-width");
        var sliderHeigth = $("#main-slider").data("slider-height");
        var sliderArrows = $("#main-slider").data("slider-arrows");
        var sliderButtons = $("#main-slider").data("slider-buttons");

        // Detect mobile device
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 767;
        
        // Calculate responsive height
        function getResponsiveHeight() {
            var width = $(window).width();
            if (width <= 480) {
                return '300px';
            } else if (width <= 767) {
                return '400px';
            } else if (width <= 991) {
                return '600px';
            } else {
                return sliderHeigth || '950px';
            }
        }

        // Initialize slider with responsive settings
        var sliderOptions = {
            width: sliderWidth || '100%',
            height: getResponsiveHeight(),
            arrows: isMobile ? false : sliderArrows,
            buttons: sliderButtons,
            fade: true,
            fullScreen: !isMobile, // Disable fullScreen on mobile
            touchSwipe: isMobile, // Enable touch swipe on mobile
            autoplay: true,
            autoplayDelay: 5000,
            loop: true,
            orientation: 'horizontal', // Explicitly set orientation
            breakpoints: {
                480: {
                    height: '300px',
                    arrows: false,
                    fullScreen: false,
                    touchSwipe: true
                },
                767: {
                    height: '400px',
                    arrows: false,
                    fullScreen: false,
                    touchSwipe: true
                },
                991: {
                    height: '600px',
                    arrows: sliderArrows,
                    fullScreen: false,
                    touchSwipe: true
                }
            }
        };

        // Initialize slider
        var sliderInstance = $( '#main-slider' ).sliderPro(sliderOptions);
        
        // Fix touch event handling to prevent console errors on mobile
        if (isMobile && sliderInstance.length > 0) {
            try {
                var slider = sliderInstance.data('sliderPro');
                if (slider && slider.$slidesMask && slider.$slidesMask.length > 0) {
                    // Store original _onTouchMove method
                    var originalOnTouchMove = slider._onTouchMove;
                    
                    // Override _onTouchMove to check if event is cancelable
                    slider._onTouchMove = function(event) {
                        // Check if event is cancelable before calling preventDefault
                        var eventObject = typeof event.originalEvent.touches !== 'undefined' ? event.originalEvent.touches[0] : event.originalEvent;
                        
                        // Indicate that the move event is being fired
                        this.isTouchMoving = true;
                        
                        // Get the current position of the mouse pointer
                        this.touchEndPoint.x = eventObject.pageX || eventObject.clientX;
                        this.touchEndPoint.y = eventObject.pageY || eventObject.clientY;
                        
                        // Calculate the distance of the movement on both axis
                        this.touchDistance.x = this.touchEndPoint.x - this.touchStartPoint.x;
                        this.touchDistance.y = this.touchEndPoint.y - this.touchStartPoint.y;
                        
                        // Calculate the distance of the swipe
                        var distance = this.settings.orientation === 'horizontal' ? this.touchDistance.x : this.touchDistance.y,
                            oppositeDistance = this.settings.orientation === 'horizontal' ? this.touchDistance.y : this.touchDistance.x;
                        
                        // If the movement is in the same direction as the orientation of the slides, the swipe is valid
                        if (Math.abs(distance) > Math.abs(oppositeDistance)) {
                            // Only prevent default if the event is cancelable
                            if (event.cancelable !== false && event.originalEvent && event.originalEvent.cancelable !== false) {
                                try {
                                    event.preventDefault();
                                } catch(e) {
                                    // Silently ignore if preventDefault fails
                                }
                            }
                            
                            // Continue with the original logic
                            if (this.settings.loop === false) {
                                // Make the slides move slower if they're dragged outside its bounds
                                if ((this.slidesPosition > this.touchStartPosition && this.selectedSlideIndex === 0) ||
                                    (this.slidesPosition < this.touchStartPosition && this.selectedSlideIndex === this.getTotalSlides() - 1)) {
                                    distance = distance * 0.2;
                                }
                            }
                            
                            this._moveTo(this.touchStartPosition + distance, true);
                        }
                    };
                }
            } catch(e) {
                // If patching fails, suppress the console warning instead
                var originalWarn = console.warn;
                console.warn = function() {
                    var message = arguments[0] ? arguments[0].toString() : '';
                    if (message.indexOf('Ignored attempt to cancel a touchmove event') === -1 &&
                        message.indexOf('cancelable=false') === -1) {
                        originalWarn.apply(console, arguments);
                    }
                };
            }
        }

        // Update slider height on window resize
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if ($('#main-slider').length > 0 && typeof $('#main-slider').data('sliderPro') !== 'undefined') {
                    var newHeight = getResponsiveHeight();
                    $('#main-slider').data('sliderPro').update({
                        height: newHeight
                    });
                }
            }, 250);
        });
    }



/////////////////////////////////////////////////////////////////
// Bxslider
/////////////////////////////////////////////////////////////////

    if ($('.bxslider').length > 0) {

        $('.bxslider').bxSlider({
          mode: 'vertical',
          minSlides: 3,
          controls: false,
          autoHover: true,
          auto: false
        });
    }


/////////////////////////////////////////////////////////////////
// OWL Sliders
/////////////////////////////////////////////////////////////////

    var Core = {

        initialized: false,

        initialize: function() {

                if (this.initialized) return;
                this.initialized = true;

                this.build();

        },

        build: function() {

        // Owl Carousel

            this.initOwlCarousel();
        },
        initOwlCarousel: function(options) {

            $(".enable-owl-carousel").each(function(i) {
                var $owl = $(this);

                var itemsData = $owl.data('items');
                var navigationData = $owl.data('navigation');
                var paginationData = $owl.data('pagination');
                var singleItemData = $owl.data('single-item');
                var autoPlayData = $owl.data('auto-play');
                var transitionStyleData = $owl.data('transition-style');
                var mainSliderData = $owl.data('main-text-animation');
                var afterInitDelay = $owl.data('after-init-delay');
                var stopOnHoverData = $owl.data('stop-on-hover');
                var min480 = $owl.data('min480');
                var min768 = $owl.data('min768');
                var min992 = $owl.data('min992');
                var min1200 = $owl.data('min1200');

                $owl.owlCarousel({
                    navigation : navigationData,
                    pagination: paginationData,
                    singleItem : singleItemData,
                    autoPlay : autoPlayData,
                    transitionStyle : transitionStyleData,
                    stopOnHover: stopOnHoverData,
                    navigationText : ["<i></i>","<i></i>"],
                    items: itemsData,
                    itemsCustom:[
                                    [0, 1],
                                    [465, min480],
                                    [750, min768],
                                    [975, min992],
                                    [1185, min1200]
                    ],
                    afterInit: function(elem){
                                if(mainSliderData){
                                        setTimeout(function(){
                                                $('.main-slider_zoomIn').css('visibility','visible').removeClass('zoomIn').addClass('zoomIn');
                                                $('.main-slider_fadeInLeft').css('visibility','visible').removeClass('fadeInLeft').addClass('fadeInLeft');
                                                $('.main-slider_fadeInLeftBig').css('visibility','visible').removeClass('fadeInLeftBig').addClass('fadeInLeftBig');
                                                $('.main-slider_fadeInRightBig').css('visibility','visible').removeClass('fadeInRightBig').addClass('fadeInRightBig');
                                        }, afterInitDelay);
                                    }
                    },
                    beforeMove: function(elem){
                        if(mainSliderData){
                                $('.main-slider_zoomIn').css('visibility','hidden').removeClass('zoomIn');
                                $('.main-slider_slideInUp').css('visibility','hidden').removeClass('slideInUp');
                                $('.main-slider_fadeInLeft').css('visibility','hidden').removeClass('fadeInLeft');
                                $('.main-slider_fadeInRight').css('visibility','hidden').removeClass('fadeInRight');
                                $('.main-slider_fadeInLeftBig').css('visibility','hidden').removeClass('fadeInLeftBig');
                                $('.main-slider_fadeInRightBig').css('visibility','hidden').removeClass('fadeInRightBig');
                        }
                    },
                    afterMove: sliderContentAnimate,
                    afterUpdate: sliderContentAnimate,
                });
            });

            function sliderContentAnimate(elem){
                var $elem = elem;
                var afterMoveDelay = $elem.data('after-move-delay');
                var mainSliderData = $elem.data('main-text-animation');
                if(mainSliderData){
                    setTimeout(function(){
                        $('.main-slider_zoomIn').css('visibility','visible').addClass('zoomIn');
                        $('.main-slider_slideInUp').css('visibility','visible').addClass('slideInUp');
                        $('.main-slider_fadeInLeft').css('visibility','visible').addClass('fadeInLeft');
                        $('.main-slider_fadeInRight').css('visibility','visible').addClass('fadeInRight');
                        $('.main-slider_fadeInLeftBig').css('visibility','visible').addClass('fadeInLeftBig');
                        $('.main-slider_fadeInRightBig').css('visibility','visible').addClass('fadeInRightBig');
                    }, afterMoveDelay);
                }
            }
        },

    };

    Core.initialize();

});


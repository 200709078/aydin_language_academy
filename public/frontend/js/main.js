(function ($) {
    "use strict";

    // Masaustunde tel: baglantilarini etkisizlestir (mobilde arama uygulamasi acilir)
    var isTouchDevice = window.matchMedia('(pointer: coarse)').matches || navigator.maxTouchPoints > 0;
    if (!isTouchDevice) {
        document.querySelectorAll('a[href^="tel:"]').forEach(function (link) {
            link.removeAttribute('href');
        });
    }

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Facts counter (eklenti yalnız sayaç kullanılan sayfalarda yüklenir).
    if ($.fn.counterUp) {
        $('[data-toggle="counter-up"]').counterUp({
            delay: 10,
            time: 2000
        });
    }


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        animateOut: 'fadeOutLeft',
        items: 1,
        dots: true,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });


    // Sticky desktop navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.frontend-header__desktop .navbar.sticky-top').addClass('shadow-sm');
        } else {
            $('.frontend-header__desktop .navbar.sticky-top').removeClass('shadow-sm');
        }
    });
    // Reviews carousel
    $(".review-carousel").owlCarousel({
        autoplay: false,
        smartSpeed: 700,
        loop: true,
        margin: 24,
        dots: false,
        nav: false,
        slideBy: 1,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });

    $(".review-prev").on("click", function () {
        $(".review-carousel").trigger("prev.owl.carousel");
    });

    $(".review-next").on("click", function () {
        $(".review-carousel").trigger("next.owl.carousel");
    });

    // News carousel: cards loop continuously on the homepage.
    var $newsCarousels = $(".news-carousel");

    if ($newsCarousels.length > 0 && $.fn.owlCarousel) {
        $newsCarousels.each(function () {
            var $carousel = $(this);
            var count = $carousel.children().length;
            var hasMultipleNewsItems = count > 1;
            var shouldLoop = count > 3;
            var $dotsContainer = $carousel.closest(".ala-news-section").find(".news-carousel-dots").first();

            $carousel.owlCarousel({
                autoplay: hasMultipleNewsItems,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                smartSpeed: 700,
                loop: shouldLoop,
                rewind: !shouldLoop,
                margin: 24,
                dots: hasMultipleNewsItems,
                dotsContainer: hasMultipleNewsItems ? $dotsContainer : false,
                nav: false,
                slideBy: 1,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    992: {
                        items: 3
                    }
                }
            });
        });

        $(".news-prev").on("click", function () {
            $(this).closest(".ala-news-section").find(".news-carousel").first().trigger("prev.owl.carousel");
        });

        $(".news-next").on("click", function () {
            $(this).closest(".ala-news-section").find(".news-carousel").first().trigger("next.owl.carousel");
        });
    }

})(jQuery);


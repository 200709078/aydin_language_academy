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


    // Sticky desktop navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.frontend-header__desktop .navbar.sticky-top').addClass('shadow-sm');
        } else {
            $('.frontend-header__desktop .navbar.sticky-top').removeClass('shadow-sm');
        }
    });
    
    
    // Facts counter
    if (typeof $.fn.counterUp === 'function') {
        $('[data-toggle="counter-up"]').counterUp({
            delay: 10,
            time: 2000
        });
    }


    // Date and time picker
    $('.date').datetimepicker({
        format: 'L'
    });
    $('.time').datetimepicker({
        format: 'LT'
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: false,
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

    
})(jQuery);


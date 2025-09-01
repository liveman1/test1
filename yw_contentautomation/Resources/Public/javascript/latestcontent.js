document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.latest-content-swiper', {
        loop: true,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        }
    });
});

$('.single-item,.slider2-item, .slider2-item-mob, .single-item-mob').slick({
    prevArrow: '<button type="button" class="slick-prev"><img src="/catalog/view/theme/default/assets/images/arrow-left.svg"></button>',
    nextArrow: '<button type="button" class="slick-next"><img src="/catalog/view/theme/default/assets/images/arrow-right.svg"></button>',
});

$('.collapsed-mobile').click(function(e){
    e.preventDefault();
    $(this).toggleClass('active');
});

$('.mobile-menu-toggle').click(function(e){
    e.preventDefault();
    $('.mobile-menu').toggleClass('open');
});
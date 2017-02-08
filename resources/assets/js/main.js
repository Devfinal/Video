// ------------ Dependencies ------------ //
require('./bootstrap');
require('./vendor/owl');
require('./vendor/lazyload');
window.Masonry = require('./vendor/masonry');
window.PhotoSwipe = require('./vendor/photoswipe');
window.PhotoSwipeUI_Default = require('./vendor/photoswipe-ui-default');

jQuery(document).ready($ => {

    // ------------ Cached References ------------ //
    const $window = $(window);
    const $document = $(document);
    const $body = $('body');
    const $lazyImages = $('figure.lazyload');
    const $gridList = $body.find('.grid-list');
    const $gallery = $body.find('.gallery:not(.gallery--botm):not(.gallery--thumbnails)');
    const $gallerybotm = $body.find('.gallery.gallery--botm:not(.gallery--thumbnails)');
    const $galleryThumbnails = $body.find('.gallery.gallery--thumbnails');
    const $galleryElement = $body.find('.pswp');
    var gallery;
    var galleryItems = $gallery.find('figure').map((i, figure) => {
        const $figure = $(figure);

        return {
            src: $figure.data('src-original'),
            w: $figure.data('width-original'),
            h: $figure.data('height-original')
        };
    });
    const $galleryLinks = $gallery.find('.item a');
    const $photoswipeLinks = $body.find('.photoswipe-link');
    const photoswipeItems = $photoswipeLinks.find('img').map((i, img) => {
        $img = $(img);

        return {
            src: $img.data('src-original'),
            w: $img.data('width-original'),
            h: $img.data('height-original')
        };
    });

    // detect touch
    if (!!('ontouchstart' in window)) {
        $body.addClass('touch');
    }

    // ------------ Images ------------ //
    // const lazyload = new LazyLoad({
    //     inView: true,
    //     offset: 200
    // });
    // lazyload.registerElements($lazyImages.get());


    // ------------ Galleries ------------ //
    $gallery.owlCarousel({
        navigation: true,
        navigationText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
        pagination: false,
        paginationNumbers: true,
        singleItem: true,
        autoHeight: false,
        theme: 'bfh-owl-theme',
        afterAction: function() {
            $galleryThumbnails.find(".owl-item")
                .removeClass("synced")
                .eq(this.currentItem)
                .addClass("synced");

            if ($galleryThumbnails.data("owlCarousel") !== void 0) {
                let visibleItems = $galleryThumbnails.data("owlCarousel").owl.visibleItems;
                let found = false;

                for (var i in visibleItems) {
                    if (this.currentItem === visibleItems[i]) {
                        found = true;
                    }
                }

                if (found === false) {
                    if (this.currentItem > visibleItems[visibleItems.length - 1]) {
                        $galleryThumbnails.trigger("owl.goTo", this.currentItem - visibleItems.length + 2);
                    } else {
                        if (this.currentItem - 1 === -1) {
                            this.currentItem = 0;
                        }

                        $galleryThumbnails.trigger("owl.goTo", this.currentItem);
                    }
                } else if (this.currentItem === visibleItems[visibleItems.length - 1]) {
                    $galleryThumbnails.trigger("owl.goTo", visibleItems[1]);
                } else if (this.currentItem === visibleItems[0]) {
                    $galleryThumbnails.trigger("owl.goTo", this.currentItem - 1);
                }
            }
        }
    });

    $gallerybotm.owlCarousel({
        navigation: true,
        navigationText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
        pagination: false,
        singleItem: true,
        autoHeight: false,
        autoPlay: true,
        loop: true,
        theme: 'bfh-owl-theme'
    });

    $galleryThumbnails.owlCarousel({
        items: 15,
        itemsDesktop: [1199, 10],
        itemsDesktopSmall: [979, 10],
        itemsTablet: [768, 8],
        itemsMobile: [479, 4],
        afterInit: $element => {
            $element.find(".owl-item").eq(0).addClass("synced");
        }
    });

    $galleryLinks.on('click', event => {
        if (event.currentTarget.getAttribute('href') !== '#') {
            return true;
        }

        event.preventDefault();

        if (!~event.target.className.indexOf('image')) {
            return false;
        }

        event.stopPropagation();

        const $link = $(event.currentTarget);

        const gallery = new PhotoSwipe($galleryElement.get(0), PhotoSwipeUI_Default, galleryItems, {
            index: $galleryLinks.index(event.currentTarget),
            bgOpacity: 0.9,
            showHideOpacity: true,
            shareEl: false,
        });

        gallery.init();

        return false;
    });

    // gallery video
    $galleryLinks.find('video').one('playing', event => {
        $(event.currentTarget).closest('a').find('.play-icon').remove();
    });

    $galleryLinks.find('.play-icon').on('click', event => {
        $(event.currentTarget).closest('a').find('video').get(0).play();
    });

    $galleryThumbnails.on('click', '.owl-item', function (event) {
        event.preventDefault();

        $gallery.trigger('owl.goTo', $(this).data('owlItem'));
    });

    $photoswipeLinks.on('click', event => {
        if (event.currentTarget.getAttribute('href') !== '#') {
            return true;
        }

        event.preventDefault();

        if (!~event.target.className.indexOf('image')) {
            return false;
        }

        event.stopPropagation();

        const $link = $(event.currentTarget);

        const gallery = new PhotoSwipe($galleryElement.get(0), PhotoSwipeUI_Default, photoswipeItems, {
            index: $photoswipeLinks.index(event.currentTarget),
            bgOpacity: 0.9,
            showHideOpacity: true,
            shareEl: false,
        });

        gallery.init();

        return false;
    });


    // smartresize plugin from Paul Irish
    (function ($, sr) {

        // debouncing function from John Hann
        // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
        var debounce = function (func, threshold, execAsap) {
            var timeout;

            return function debounced() {
                var obj = this, args = arguments;

                function delayed() {
                    if (!execAsap)
                        func.apply(obj, args);
                    timeout = null;
                };

                if (timeout)
                    clearTimeout(timeout);
                else if (execAsap)
                    func.apply(obj, args);

                timeout = setTimeout(delayed, threshold || 100);
            };
        }
        // smartresize
        jQuery.fn[sr] = function (fn) {
            return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
        };

    })(jQuery, 'smartresize');

    const masonrySettings = {
        initLayout: true,
        itemSelector: '.grid-item',
        stamp: '.stamp',
        stagger: 45,
        percentPosition: true
    };

    if ($window.innerWidth() > 544) {
        $gridList.masonry(masonrySettings);

        $window.on('load', () => {
            $gridList.masonry('layout');
        });
    }

    $window.smartresize(event => {
        if ($window.innerWidth() > 544) {
            if (!$gridList.data('masonry')) {
                $gridList.masonry(masonrySettings);
            }
        } else {
            if ($gridList.data('masonry')) {
                $gridList.masonry('destroy');
            }
        }
    });


    // video
    plyr.setup('video', {
        controls: [
            'play',
            'progress',
            'volume',
            'fullscreen'
        ]
    });





    // AJAX likes
    $document.on('submit', '.ajax-like', event => {
        const xhr = new XMLHttpRequest;
        const $form = $(event.currentTarget);

        event.preventDefault();

        xhr.addEventListener('readystatechange', event => {
            if (event.currentTarget.readyState === 4) {
                if (event.currentTarget.status === 200) {
                    let $likeWrapper = $(event.currentTarget.responseText).find($form.data('like-wrapper'));
                    $($form.data('like-wrapper')).html($likeWrapper.html());
                }
            }
        });

        xhr.open(event.currentTarget.getAttribute('method'), event.currentTarget.getAttribute('action'), true);
        xhr.send(new FormData(event.currentTarget));
    });

});

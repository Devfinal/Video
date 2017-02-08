/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Masonry = require('./vendor/masonry');
window.Sortable = require('sortablejs');

jQuery(document).ready($ => {
    require('./media-preview');
    require('./media-upload');

    $('.navbar-horizontal .navbar-toggler').on('click', event => {
        $('body').toggleClass('sidebar-open');
    });

    $('.sidebar-collapse-button').on('click', () => {
        $('body').toggleClass('sidebar-collapsed');

        Cookies.set('sidebar-collapsed', $('body').hasClass('sidebar-collapsed') ? 1 : 0);
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
                }
                ;

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

    const $window = $(window);
    const $gridList = $('.grid-list');
    const $sortable = $('.sortable');

    window.masonrySettings = {
        initLayout: true,
        itemSelector: '.grid-item',
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


    // form actions
    $('.delete-media-button').on('click', function (event) {
        $('#delete-media-form')
            .attr('action', '/admin/posts/' + $(event.currentTarget).data('post-id') + '/media/' + $(event.currentTarget).data('media-id'))
            .submit();
    });

    $('.delete-post-button').on('click', function (event) {
        $('#delete-post').submit();
    });


    // media order
    $sortable.each((i, element) => new Sortable(element, {
            draggable: '.draggable',
            delay: $window.innerWidth() < 544 ? 200 : false,
            scroll: true,
            onUpdate(event) {
                const orderedIds = $('.draggable', event.to).get().map(element => {
                    return element.getAttribute('data-media-id');
                });

                $.ajax({
                    method: 'PUT',
                    url: `/admin/posts/${event.to.getAttribute('data-post-id')}/media/sort`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        order: orderedIds
                    }
                });
            }
        }));


    // video
    plyr.setup(document.querySelectorAll('video'), {
        controls: [
            'play',
            'progress',
            'volume',
            'fullscreen'
        ]
    });


    const tinyMcePlugins = ~Laravel.userRoles.indexOf('admin') ?
        'table image textcolor code' :
        ' ';
    const tinyMceToolbar = ~Laravel.userRoles.indexOf('admin') ?
        'undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | code' :
        ' ';

    // tinyMCE
    tinymce.init({
        selector: '.tinymce',
        menubar: false,
        height: 360,
        plugins: tinyMcePlugins,
        toolbar: tinyMceToolbar,
        content_css: '/css/tinymce.css',
        skin_url: '/tinymce/skins/bfh',
        relative_urls: false,
        forced_root_block: false,
        force_br_newlines: true,
        force_p_newlines: true,
        image_list: success => {
            success([]);
        }
    });
});

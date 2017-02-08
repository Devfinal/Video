;(function(LazyLoad) {
    if (typeof defined === 'function' && define.amd) {
        /** register as an AMD module */
        define(LazyLoad);
    } else {
        /** register on window */
        window.LazyLoad = LazyLoad();
    }
})(function() {
    'use strict';

    var docElem = document.documentElement || document.body;

    function LazyLoad(args) {
        this.options = {
            inView: args.inView || false,
            offset: args.offset || 0,
        };
        this.elements = {};
        this.init = false;
    }

    LazyLoad.prototype.registerElements = function(elements) {
        elements.forEach(function(element) {
            var offset = getElementOffset(element);
            if (!(offset.top in this.elements)) {
                this.elements[offset.top] = [];
            }
            this.elements[offset.top].push(element);
        }.bind(this));

        if (!this.init) {
            this.listenOnScroll();
            window.addEventListener('scroll', this.listenOnScroll.bind(this), false);
            this.init = true;
        }
    };

    LazyLoad.prototype.listenOnScroll = function() {

        if (this.elements) {

            var scrollPosition = getScrollPosition();

            for (var key in this.elements) {

                if (this.elements.hasOwnProperty(key)) {

                    if ((scrollPosition.top + docElem.clientHeight + this.options.offset) >= key) {
                        this.elements[key].forEach(function(element) {
                            loadImage(element);
                        });
                        delete this.elements[key];
                    }
                } else {
                    this.elements = false;
                }
            }
        }
    };

    function getScrollPosition() {

        return {
            top: (window.pageYOffset || docElem.scrollTop) - (docElem.clientTop || 0),
            left: (window.pageXOffset || docElem.scrollLeft) - (docElem.clientLeft || 0)
        };
    }

    function getElementOffset(element) {

        var box = {top: 0, left: 0};

        if (typeof element.getBoundingClientRect !== 'undefined') {
            box = element.getBoundingClientRect();
        }

        return {
            top: Math.round(box.top + (window.pageYOffset || docElem.scrollTop) - (docElem.clientTop || 0)),
            left: Math.round(box.left + (window.pageXOffset || docElem.scrollLeft) - (docElem.clientLeft || 0))
        };
    }

    function loadImage(element) {

        if (!hasClass(element, 'loaded')) {
            var noscript = element.children[0];

            var img = (typeof Image === 'function') ? new Image() : document.createElement('img');
            img.src = noscript.getAttribute('data-src');
            img.className = 'image';
            img.alt = noscript.getAttribute('data-alt');
            img.title = noscript.getAttribute('data-title');

            if (true) { // maby check conditions?
                var div = document.createElement('div');
                div.className = 'image';
                div.style.backgroundImage = "url('" + img.src + "')";
                element.appendChild(div);
            } else {
                element.appendChild(img);
            }

            noscript.parentNode.removeChild(noscript);
            element.className = element.className + ' loaded';
        }
    }

    function hasClass(element, cls) {
        return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
    }

    return LazyLoad;
});

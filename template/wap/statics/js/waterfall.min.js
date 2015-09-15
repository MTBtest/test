/*!
 * waterfall
 * http://wlog.cn/waterfall/
 *
 * Copyright (c) 2013 bingdian
 * Licensed under the MIT license.
 */
/*global Handlebars: false, console: false */
;(function( $, window, document, undefined ) {

    'use strict';

    /*
     * defaults
     */
    var $window = $(window),
        pluginName = 'waterfall',
        defaults = {
            itemCls: 'waterfall-item',  // the brick element class
            prefix: 'waterfall', // the waterfall elements prefix
            fitWidth: true, // fit the parent element width
            colWidth: 240,  // column width
            gutterWidth: 10, // the brick element horizontal gutter
            gutterHeight: 10, // the brick element vertical gutter
            align: 'center', // the brick align，'align', 'left', 'right'
            minCol: 1,  // min columns
            maxCol: undefined, // max columns, if undefined,max columns is infinite
            maxPage: undefined, // max page, if undefined,max page is infinite
            bufferPixel: -50, // decrease this number if you want scroll to fire quicker
            containerStyle: { // the waterfall container style
                position: 'relative'
            },
            resizable: true, // triggers layout when browser window is resized
            isFadeIn: false, // fadein effect on loading
            isAnimated: false, // triggers animate when browser window is resized
            animationOptions: { // animation options
            },
            isAutoPrefill: true,  // When the document is smaller than the window, load data until the document is larger
            path: undefined, // Either parts of a URL as an array (e.g. ["/popular/page/", "/"] => "/popular/page/1/" or a function that takes in the page number and returns a URL(e.g. function(page) { return '/populr/page/' + page; } => "/popular/page/1/")
            dataType: 'json', // json, jsonp, html
            params: {}, // params,{type: "popular", tags: "travel", format: "json"} => "type=popular&tags=travel&format=json"
            headers: {}, // headers variable that gets passed to jQuery.ajax()

            loadingMsg: '<div style="text-align:center;padding:10px 0; color:#999;">loading...</div>', // loading html

            state: {
                isDuringAjax: false,
                isProcessingData: false,
                isResizing: false,
                isPause: false,
                curPage: 1 // cur page
            },

            // callbacks
            callbacks: {
                /*
                 * loading start
                 * @param {Object} loading $('#waterfall-loading')
                 */
                loadingStart: function($loading) {
                    $loading.show();
                    //console.log('loading', 'start');
                },
                loadingFristEmpay:function($loading, isBeyondMaxPage) {
                    $("#waterfall-content").hide();
                    $("#waterfall-empty").show();
                },
                /*
                 * loading finished
                 * @param {Object} loading $('#waterfall-loading')
                 * @param {Boolean} isBeyondMaxPage
                 */
                loadingFinished: function($loading, isBeyondMaxPage, data) {
                    if ( !isBeyondMaxPage ) {
                        $loading.fadeOut();
                        this._debug('loading finished');
                    } else {
                        //console.log('loading isBeyondMaxPage');
                        $loading.remove();
                    }
                },

                /*
                 * loading error
                 * @param {String} xhr , "end" "error"
                 */
                loadingError: function($message, xhr) {
                    $message.html('数据获取失败，请重试。');
                }
            },

            debug: false // enable debug
        };

    /*
     * Waterfall constructor
     */
    function Waterfall(element, options) {
        this.$element = $(element);
        this.options = $.extend(true, {}, defaults, options);
        this.colHeightArray = []; // columns height array
        this.styleQueue = [];

        this._init();
    }


    Waterfall.prototype = {
        constructor: Waterfall,

        // Console log wrapper
        _debug: function () {
            if ( true !== this.options.debug ) {
                return;
            }

            if (typeof console !== 'undefined' && typeof console.log === 'function') {
                // Modern browsers
                // Single argument, which is a string
                if ((Array.prototype.slice.call(arguments)).length === 1 && typeof Array.prototype.slice.call(arguments)[0] === 'string') {
                    console.log( (Array.prototype.slice.call(arguments)).toString() );
                } else {
                    console.log( Array.prototype.slice.call(arguments) );
                }
            } else if (!Function.prototype.bind && typeof console !== 'undefined' && typeof console.log === 'object') {
                // IE8
                Function.prototype.call.call(console.log, console, Array.prototype.slice.call(arguments));
            }
        },


        /*
         * _init
         * @callback {Object Function } and when instance is triggered again -> $element.waterfall()
         */
        _init: function( callback ) {
            var options = this.options,
                path = options.path;
        
            this._initContainer();
            //this.reLayout( callback );

            if ( !path ) {
                this._debug('Invalid path');
                return;
            }

            // auto prefill
            if ( options.isAutoPrefill ) {
                this._prefill();
            }

            // bind resize
            if ( options.resizable ) {
                //this._doResize();
            }

            // bind scroll
            this._doScroll();
        },

        /*
         * init waterfall container
         */
        _initContainer: function() {
            var options = this.options,
                prefix = options.prefix;

            // fix fixMarginLeft bug
//            $('body').css({
//                overflow: 'scroll'
//            });


            this.$element.css(this.options.containerStyle).addClass(prefix + '-container');
            this.$element.after('<div id="' + prefix + '-loading">' +options.loadingMsg+ '</div><div id="' + prefix + '-message" style="text-align:center;color:#999;"></div>');

            this.$loading = $('#' + prefix + '-loading');
            this.$message = $('#' + prefix + '-message');
        },
        /*
         * append
         * @param {Object} $content
         * @param {Function} callback
         */
        append: function($content, callback) {
            this.$element.append($content);
            if ( typeof callback === 'function' ) {
                callback();
            }
        },

        /*
         * opts
         * @param {Object} opts
         * @param {Function} callback
         */
        option: function( opts, callback ){
            if ( $.isPlainObject( opts ) ){
                this.options = $.extend(true, this.options, opts);
                if ( typeof callback === 'function' ) {
                    callback();
                }

                // re init
                this._init();
            }
        },

        /*
         * prevent ajax request
         */
        pause: function(callback) {
            this.options.state.isPause = true;
            if ( typeof callback === 'function' ) {
                callback();
            }
        },


        /*
         * resume ajax request
         */
        resume: function(callback) {
            this.options.state.isPause = false;

            if ( typeof callback === 'function' ) {
                callback();
            }
        },

        /**
         * request data
         */
        _requestData: function(callback) {
            var self = this,
                options = this.options,
                maxPage = options.maxPage,
                curPage = options.state.curPage++, // cur page
                path = options.path,
                dataType = options.dataType,
                params = options.params,
                headers = options.headers,
                pageurl;

            if ( (maxPage !== undefined && curPage > maxPage) || options.isPause){
                options.state.isBeyondMaxPage = true;
                options.callbacks.loadingFinished(this.$loading, options.state.isBeyondMaxPage);
                return;
            }
            
            this._debug('loading...');


            // get ajax url
            pageurl = (typeof path === 'function') ? path(curPage) : path;
            params.page = curPage;
            this._debug('heading into ajax', pageurl+$.param(params));

            // loading start
            options.callbacks.loadingStart(this.$loading);

            // update state status
            options.state.isDuringAjax = true;
            options.state.isProcessingData = true;
            // ajax
            $.ajax({
                url: pageurl,
                data: params,
                headers: headers,
                dataType: dataType,
                success: function(data) {                   
                    self.options.state.isProcessingData = false;
                    self.options.state.isDuringAjax = false;
                    self.options.state.isInvalidPage = false;                    
                    if(data.status == 'error') {
                        self.pause();
                        self.options.state.isBeyondMaxPage = true;
                    } else {
                        self.resume();
                        //self.options.state.isPause = false;
                    }
                    self._handleResponse(data, callback);
                },
                error: function(jqXHR) {
                    self._responeseError('error');
                }
            });
        },


        /**
         * handle response
         * @param {Object} data
         * @param {Function} callback
         */
        _handleResponse: function(data, callback) {
            var self = this,
                options = this.options;
            self.options.callbacks.loadingFinished(self.$loading, self.options.state.isBeyondMaxPage, data);
        },

        /*
         * reponse error
         */
        _responeseError: function(xhr) {

            this.$loading.hide();
            this.options.callbacks.loadingError(this.$message, xhr);

            if ( xhr !== 'end' && xhr !== 'error' ) {
                xhr = 'unknown';
            }

            this._debug('Error', xhr);
        },


        _nearbottom: function() {
            var options = this.options,
                distanceFromWindowBottomToMinColBottom = $window.scrollTop() + $window.height() - this.$element.height();
            this._debug('math:', distanceFromWindowBottomToMinColBottom);
            return ( distanceFromWindowBottomToMinColBottom > options.bufferPixel );
        },

        /*
         * prefill
         */
        _prefill: function() {
            if ( this.$element.height() <= $window.height() ) {
                this._scroll();
            }
        },

        /*
         * _scroll
         */
        _scroll: function() {
            var options = this.options,
                state = options.state,
                self = this;

            if ( state.isProcessingData || state.isDuringAjax || state.isInvalidPage || state.isPause ) {
                return;
            }

            if ( !this._nearbottom() ) {
                return;
            }
            this._requestData();
        },
        /*
         * do scroll
         */
        _doScroll: function() {
            var self = this,
                scrollTimer;

            $window.bind('scroll', function() {
                if ( scrollTimer ) {
                    clearTimeout(scrollTimer);
                }
                scrollTimer = setTimeout(function() {
                    self._debug('event', 'scrolling ...');
                    self._scroll();
                }, 100);
            });
        }
    };


    $.fn[pluginName] = function(options) {
        if ( typeof options === 'string' ) { // plugin method
            var args = Array.prototype.slice.call( arguments, 1 );

            this.each(function() {
                var instance = $.data( this, 'plugin_' + pluginName );

                if ( !instance ) {
                    instance._debug('instance is not initialization');
                    return;
                }

                if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) { //
                    instance._debug( 'no such method "' + options + '"' );
                    return;
                }

                //  apply method
                instance[options].apply( instance, args );
            });
        } else { // new plugin
            this.each(function() {
                if ( !$.data(this, 'plugin_' + pluginName) ) {
                    $.data(this, 'plugin_' + pluginName, new Waterfall(this, options));
                }
            });
        }

        return this;
    };

}( jQuery, window, document ));


/*!
 * jQuery imagesLoaded plugin v2.1.2
 * http://github.com/desandro/imagesloaded
 *
 * MIT License. by Paul Irish et al.
 */

;(function($, undefined) {
'use strict';

// blank image data-uri bypasses webkit log warning (thx doug jones)
var BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

$.fn.imagesLoaded = function( callback ) {
    var $this = this,
        deferred = $.isFunction($.Deferred) ? $.Deferred() : 0,
        hasNotify = $.isFunction(deferred.notify),
        $images = $this.find('img').add( $this.filter('img') ),
        loaded = [],
        proper = [],
        broken = [];

    // Register deferred callbacks
    if ($.isPlainObject(callback)) {
        $.each(callback, function (key, value) {
            if (key === 'callback') {
                callback = value;
            } else if (deferred) {
                deferred[key](value);
            }
        });
    }

    function doneLoading() {
        var $proper = $(proper),
            $broken = $(broken);

        if ( deferred ) {
            if ( broken.length ) {
                deferred.reject( $images, $proper, $broken );
            } else {
                deferred.resolve( $images );
            }
        }

        if ( $.isFunction( callback ) ) {
            callback.call( $this, $images, $proper, $broken );
        }
    }

    function imgLoadedHandler( event ) {
        imgLoaded( event.target, event.type === 'error' );
    }

    function imgLoaded( img, isBroken ) {
        // don't proceed if BLANK image, or image is already loaded
        if ( img.src === BLANK || $.inArray( img, loaded ) !== -1 ) {
            return;
        }

        // store element in loaded images array
        loaded.push( img );

        // keep track of broken and properly loaded images
        if ( isBroken ) {
            broken.push( img );
        } else {
            proper.push( img );
        }

        // cache image and its state for future calls
        $.data( img, 'imagesLoaded', { isBroken: isBroken, src: img.src } );

        // trigger deferred progress method if present
        if ( hasNotify ) {
            deferred.notifyWith( $(img), [ isBroken, $images, $(proper), $(broken) ] );
        }

        // call doneLoading and clean listeners if all images are loaded
        if ( $images.length === loaded.length ) {
            setTimeout( doneLoading );
            $images.unbind( '.imagesLoaded', imgLoadedHandler );
        }
    }

    // if no images, trigger immediately
    if ( !$images.length ) {
        doneLoading();
    } else {
        $images.bind( 'load.imagesLoaded error.imagesLoaded', imgLoadedHandler )
        .each( function( i, el ) {
            var src = el.src,

            // find out if this image has been already checked for status
            // if it was, and src has not changed, call imgLoaded on it
            cached = $.data( el, 'imagesLoaded' );
            if ( cached && cached.src === src ) {
                imgLoaded( el, cached.isBroken );
                return;
            }

            // if complete is true and browser supports natural sizes, try
            // to check for image status manually
            if ( el.complete && el.naturalWidth !== undefined ) {
                imgLoaded( el, el.naturalWidth === 0 || el.naturalHeight === 0 );
                return;
            }

            // cached images don't fire load sometimes, so we reset src, but only when
            // dealing with IE, or image is complete (loaded) and failed manual check
            // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
            if ( el.readyState || el.complete ) {
                el.src = BLANK;
                el.src = src;
            }
        });
    }

    return deferred ? deferred.promise( $this ) : $this;
};

})(jQuery);

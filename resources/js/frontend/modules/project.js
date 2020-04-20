function debounce(a,b,c){var d;return function(){var e=this,f=arguments;clearTimeout(d),d=setTimeout(function(){d=null,c||a.apply(e,f)},b),c&&!d&&a.apply(e,f)}}

var Projects = (function() {

    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
	
	// selectors
	var selectors = {
        html:      'html',
        body:      'body',
        header:    '.js-header',
        footer:    '.js-footer',
        menu:      '.js-menu',
        relations: '.js-project-relations',
	};

    // media queries
    var mq = {
        sm: window.matchMedia("(min-width: 720px)"),
        md: window.matchMedia("(min-width: 1024px)"),
        lg: window.matchMedia("(min-width: 1168px)")
    };

    var winHeight = $(window).height();
   

    /* --------------------------------------------------------------
     * METHODS
     * ------------------------------------------------------------ */
    
    // Init
	var _initialize = function() {
        _bind();
    };

    // Bind events
    var _bind = function() {

        // Check for hash and scroll to it
        var hash = document.location.hash;
        if (hash.length) {
            _scrollTo(hash);
        }

        // Resize height on load
        if (mq.sm.matches) {
            if ($(selectors.relations).length) {
                _resize(true);
            }
        }

        // Observe height to adjust project info box
        $(window).resize(function(event){
            if (mq.sm.matches && $(selectors.relations).length) {
                _resize(false);
            }
        });
    };

    /**
     * Scrolls to a certain project based on the given hash
     * @param string hash 
     */

    var _scrollTo = function(hash){
        var project = hash.substr(1, hash.length), 
            header = $(selectors.header),
            target = $('[data-scroll="' + project + '"]');

        if (target.length)
        {
            var offsetTop = target.offset().top - header.height() - 80;
            if (mq.sm.matches) {
                offsetTop = target.offset().top - header.height() - 24;
            }
            $.scrollTo(offsetTop, 400);
        }
    };

    /**
     * Handle position of project info box on resize
     */

    var _resize = debounce(function(init){

        // get all heights
        var heights = {
            window: $(window).height(),
            header: $(selectors.header).height(),
            footer: $(selectors.footer).outerHeight(),
            menu:   $(selectors.menu).outerHeight(),
            box:    $(selectors.relations).outerHeight(),
        };

        var margin = 48,
            minHeight = heights.window - (heights.menu + margin),
            offset = heights.menu + heights.header + margin,
            treshold = heights.menu + heights.header + 120;

        if (init) {
            $(selectors.relations).css('top', offset);
            $(selectors.relations).show();

            if (treshold >= minHeight) {
                $(selectors.relations).find('figure').hide();
            }
        }
        else {
            if (heights.window > winHeight) {
                if (treshold < minHeight) {
                    $(selectors.relations).find('figure').show();
                }
            }
            else {
                if (treshold >= minHeight) {
                    $(selectors.relations).find('figure').hide();
                }
            }
        }
        winHeight = heights.window;

    }, 10);


    /* --------------------------------------------------------------
     * RETURN PUBLIC METHODS
     * ------------------------------------------------------------ */

    return {
        init:  _initialize,
	};
	
})();

// Initialize
$(function() {
    Projects.init();
});


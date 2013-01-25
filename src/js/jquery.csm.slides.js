/**
 * Plugin with a timer to switch between groups of elements.
 *
 * Usage:
 * <div data-plugin="slides">
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2013 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorSlides = {

    node : null,
    slides : null,

    timer : null,

    options : {
      interval : 30,
      slides : '',
      filter : '* > *'
    },

    /**
     * Bind event handler, and store orignal text content of the element.
     *
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.options = $.extend(this.options, options, node.data());
      this.node = node;
      /* if a slide selector is specified use it, use children() otherwise */
      this.slides = this.options.slides ? node.find(this.options.slides) : node.children();
      /* hide all expect the first slide */
      this.slides.not(this.slides.get(0)).hide();
      this.timer = window.setInterval(
        $.proxy(this.nextSlide, this), 1000 * this.options.interval
      );
    },

    /**
     * Show the next slide, if it was the last, show the first slide again.
     */
    nextSlide : function() {
      var current = this.slides.filter(':visible');
      var currentIndex = this.slides.index(current);
      var next = null;

      for (var i = currentIndex + 1; i < this.slides.length; i++) {
        if (this.slides.eq(i).has(this.options.filter).length > 0) {
          next = this.slides.eq(i);
          break;
        }
      }
      if (!next) {
        next = this.slides.eq(0);
      }
      if (next && current.get(0) != next.get(0)) {
        if (current) {
          $(current).fadeOut(
            'slow',
            function () {
              next.fadeIn('slow');
            }
          );
        } else {
          next.fadeIn('slow');
        }
      }
    }
  };

  /**
   * Activate the slides plugin for an element
   */
  $.fn.CaricaStatusMonitorSlides = function(options) {
    return this.each(
      function() {
        var instance = $.extend(true, {}, CaricaStatusMonitorSlides);
        instance.setUp($(this), options);
      }
    );
  };

})(jQuery);
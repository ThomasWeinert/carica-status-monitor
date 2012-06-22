/**
 * Plugin that updates a clock
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var Clock = {

    node : null,

    options : {
    },

    /**
     * Bind event handler, activate the update interval.
     *
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.node = node;
      this.options = $.extend(this.options, options);
      this.create(node);
      this.update();
      window.setInterval(
        $.proxy(this.update, this), 5000
      );
    },

    create : function(parent) {
      parent.append('<span class="digit hour">00</span>');
      parent.append('<span class="digit minutes">00</span>');
    },

    formatDigit : function(value) {
      if (value < 10) {
        return '0' + value;
      } else {
        return value;
      }
    },

    /**
     * Update clock data
     */
    update : function() {
      var now = new Date();
      this.node.find('.hour').text(this.formatDigit(now.getHours()));
      this.node.find('.minutes').text(this.formatDigit(now.getMinutes()));
    }
  };

  /**
   * Activate the hash replacement for an element
   */
  $.fn.Clock = function(options) {
    return this.each(
      function() {
        var instance = $.extend(true, {}, Clock);
        instance.setUp($(this), options);
      }
    );
  };

})(jQuery);
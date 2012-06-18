/**
 * Plugin to watch the url fragment/hash and update an dom element with it.
 *
 * A callback that is executed if the hash changes can be defined in the
 * options argument.
 *
 * Usage:
 * <h1 data-plugin="hash">Default Text</h1>
 *
 * jQuery('[data-plugin="hash"]').HashReplace(
 *   {
 *     onUpdate : function() {
 *       ...
 *     }
 *   }
 * );
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var HashReplace = {

    node : null,
    defaultText : '',

    options : {
      onUpdate : null
    },

    /**
     * Bind event handler, and store orignal text content of the element.
     *
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.node = node;
      this.defaultText = node.text();
      this.options = $.extend(this.options, options);
      this.replace();
      $(window).bind(
        'hashchange',
        $.proxy(this.update, this)
      );
    },

    /**
     * Read the hash, insert it into the text content. If it is empty insert
     * the stored default text.
     */
    replace : function () {
      var hash = window.location.hash ? window.location.hash.substring(1) : '';
      this.node.text(
        (hash != '') ? hash : this.defaultText
      );
    },

    /**
     * Execute the callback if defined.
     */
    update : function() {
      this.replace();
      if (this.options.onUpdate) {
        this.options.onUpdate();
      }
    }
  };

  /**
   * Activate the hash replacement for an element
   */
  $.fn.HashReplace = function(options) {
    return this.each(
      function() {
        var replace = $.extend(true, {}, HashReplace);
        replace.setUp($(this), options);
      }
    );
  };

})(jQuery);
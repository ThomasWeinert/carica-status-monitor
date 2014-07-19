/**
 * Plugin to load and display a image.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2014 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorImage = {

    node : null,
    image : null,

    options : {
      url : '',
      interval : 0,
      refresh : 'all',
      highlight : 'no',
      animations : 'no',
      basePath : '',
      showStatus : 'yes'
    },

    template : '<div class="image"><img src="about:blank" alt=""/></div>',

    setUp : function(node, options) {
      this.node = node;
      this.node.data('Widget', this);
      this.options = $.extend(this.options, options, node.data());
      options = this.options;
      var header = this.node.find('h2');
      if (this.template) {
        this.node.append(this.template);
      }
      this.image = this.node.find('img');
      this.update();
      window.setInterval(
        $.proxy(this.update, this),
        this.options.interval * 1000
      );
    },

    update : function() {
      this.image.attr('src', this.options.url + '?t=' + (new Date).getTime());
    }

  };

  /**
   * jQuery selector handling to attach StatusChart to dom elements
   *
   * @param options
   */
  $.fn.CaricaStatusMonitorImage = function(options) {
    return this.each(
      function() {
        var widget = $.extend(
          true, CaricaStatusMonitorImage
        );
        widget.setUp($(this), options);
      }
    );
  };

})(jQuery);
/**
 * Plugin to load an xml including xcal:vevents and display all events the 
 * events as countdowns.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorCountdown = {

    options : {
      url : '',
      interval : 0
    },

    template : '<ul class="countdown"/>',

    /**
     * Read the feed data and update the countdown items.
     *
     * @param data
     */
    update: function(xml) {
      
    }
  };

  /**
   * jQuery selector handling to attach the countdown widget to an element
   *
   * @param options
   */
  $.fn.CaricaStatusMonitorCountdown = function(options) {
    return this.each(
      function() {
        var widget = $.extend(
          true, $.CaricaStatusMonitorWidget(), CaricaStatusMonitorCountdown
        );
        widget.setUp($(this), options);
      }
    );
  };

})(jQuery);
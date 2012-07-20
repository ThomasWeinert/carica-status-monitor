/**
 * Plugin to load an xml including xcal:vevents and display all events the 
 * events as countdowns.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){
  
  var CaricaStatusMonitorCountdownEntry = $.extend(
    true,
    $.CaricaStatusMonitorWidget.Entry(),
    {
      template :
        '<li class="item">' +
          '<div class="numberIcon countdownFirst">' +
            '<div class="number">00</div>' +
            '<div class="title"> </div>' +
          '</div>' +
          '<div class="numberIcon countdownSecond">' +
            '<div class="number">00</div>' +
            '<div class="title"> </div>' +
          '</div>' +
          '<div class="numberIcon countdownThird">' +
            '<div class="number">00</div>' +
            '<div class="title"> </div>' +
          '</div>' +
          '<span class="spacer"></span>' +
        '</li>',
    }
  );

  var CaricaStatusMonitorCountdown = {
      
    entries : null,

    options : {
      url : '',
      interval : 0,
      max : 5
    },

    template : '<ul class="countdown"/>',
    

    /**
     * Prapare the object before fetching the data
     */
    prepare : function() {
      this.entries = $.CaricaStatusMonitorWidget.Entries();
      this.entries.setUp(
        this, this.node.find('ul')
      );
    },

    /**
     * Read the feed data and update the countdown items.
     *
     * @param data
     */
    update: function(xml) {
      var entry, data, prototype;
      var entries = xml.find('atom|entry');
      var max = this.options.max;
      if (this.options.refresh == 'all') {
        this.entries.clear();
      }
      for (var i = max; i > 0; i--) {
        entry = entries.eq(i - 1);
        if (entry.length > 0) {
          data = new Object();
          data.id = entry.find('atom|id').text();
          data.updated = entry.find('atom|updated').text();
          if (entry.find('xcal|vevent').length > 0) {
            prototype = CaricaStatusMonitorCountdownEntry;
            this.entries.get(data.id, prototype).update(data, entry);
          }
        }
      }
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
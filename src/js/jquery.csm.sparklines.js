/**
 * Plugin to load an atom entries with data series and display the
 * latest number and a small chart to show the trend
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorSparklinesEntry = $.extend(
    true,
    $.CaricaStatusMonitorWidget.Entry(),
    {
      template :
        '<li class="item">' +
          '<div class="number"/>' +
          '<div class="teaser">' +
            '<h3/>' +
            '<div class="sparkline" style="height: 30px;"/>' +
          '</div>' +
          '<span class="spacer"></span>' +
        '</li>',

      plotTimer : null,

      readDatapoints : function(xml) {
        result = [];
        xml.filter(':has(csm|data-point)').each(
          function(entryIndex) {
            var row = $(this).find('csm|data-series').first();
            row.find('csm|data-point').each(
              function(pointIndex) {
                result[pointIndex] = [
                  this.getAttribute('x'), this.getAttribute('y')
                ];
              }
            );
          }
        );
        return result;
      },

      updateData : function(data, xml) {
        var data = this.readDatapoints(xml);
        this.node.find('h3').text(xml.find('atom|title').text());
        this.node.find('.number').text(
          $.CaricaStatusMonitor.Number.roundWithUnit(
            data[data.length - 1][1]
          )
        );
        this.plot(
          this.node,
          [ data ],
          {
            xaxis : {
              show : false
            },
            yaxis : {
              show : false
            },
            grid: {
              borderWidth : 0
            }
          }
        );
      },

      /**
       * The control need to be visislbe and contain a width.
       *
       * If this is not the case, postphone the plotting for some time
       *
       * @param container
       * @param data
       * @param options
       */
      plot : function(container, data, options) {
        if (this.plotTimer) {
          window.clearTimeout(this.plotTimer);
        }
        if (container.width() > 0 && container.height() > 0) {
          container.find('.sparkline').width(
            container.width() - 160
          );
          $.plot(container.find('.sparkline'), data, options);
        } else {
          var that = this;
          this.plotTimer = window.setTimeout(
            function() {
              that.plot(container, data, options)
            },
            1000
          );
        }
      }
    }
  );

  var CaricaStatusMonitorSparklines = {

    entries : null,

    options : {
      url : '',
      interval : 0,
      max : 5
    },

    template : '<ul class="sparklines"/>',


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
     * Read the feed data and update the Sparklines items.
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
          if (entry.find('csm|data-series').length > 0) {
            prototype = CaricaStatusMonitorSparklinesEntry;
            this.entries.get(data.id, prototype).update(data, entry);
          }
        }
      }
    }
  };

  /**
   * jQuery selector handling to attach the Sparklines widget to an element
   *
   * @param options
   */
  $.fn.CaricaStatusMonitorSparklines = function(options) {
    return this.each(
      function() {
        var widget = $.extend(
          true, $.CaricaStatusMonitorWidget(), CaricaStatusMonitorSparklines
        );
        widget.setUp($(this), options);
      }
    );
  };

})(jQuery);
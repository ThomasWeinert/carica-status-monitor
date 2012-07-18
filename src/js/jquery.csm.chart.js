/**
 * Plugin to load data series inside an atom feed and display a chart.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorChartSeries = {

    data : [],

    /**
     * Read the data series from the xml into an array
     *
     * @param xml
     */
    read : function(xml) {
      series = this;
      xml.each(
       function(entryIndex) {
         var row = $(this).find('csm|data-series').first();
         series.data[entryIndex] = {
           label : $(this).find('atom|title').text(),
           data : []
         };
         row.find('csm|data-point').each(
           function(pointIndex) {
             series.data[entryIndex].data[pointIndex] = [
               this.getAttribute('x'), this.getAttribute('y')
             ];
           }
         );
       }
      );
    }
  };

  var CaricaStatusMonitorChart = {

    options : {
      url : '',
      interval : 0,
      height: '200px'
    },

    template : '<div class="chart"><div class="container"/></div>',

    /**
     * Read the feed data and update the chart.
     *
     * @param data
     */
    update: function(xml) {
      var container = this.node.find('.chart .container');
      var entries = xml.find('atom|entry');
      var series = $.extend(true, {}, CaricaStatusMonitorChartSeries);
      series.read(entries);
      container.css('height', this.options.height);

      var options = {
        series: {
          lines: { show: true },
          points: { show: false }
        },
        xaxis: this.getAxisOptions(xml.find('csm|chart-options csm|axis-x')),
        yaxis: this.getAxisOptions(xml.find('csm|chart-options csm|axis-y'))
      };
      $.plot(container, series.data, options);
    },

    /**
     * Read the axis options from the xml node
     *
     * @param xml
     */
    getAxisOptions : function(xml) {
      var options = {
        mode : xml.attr('mode') != '' ? xml.attr('mode') : null,
        timeformat : xml.attr('timeformat') != '' ? xml.attr('timeformat') : null,
        min : xml.attr('min') > 0 ? xml.attr('min') : null,
        max : xml.attr('max') != '' ? xml.attr('max') : null
      };
      return options;
    }
  };

  /**
   * jQuery selector handling to attach StatusChart to list elements
   *
   * @param options
   */
  $.fn.CaricaStatusMonitorChart = function(options) {
    return this.each(
      function() {
        var widget = $.extend(
          true, $.CaricaStatusMonitorWidget(), CaricaStatusMonitorChart
        );
        widget.setUp($(this), options);
      }
    );
  };

})(jQuery);
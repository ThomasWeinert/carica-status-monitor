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
         series.data[entryIndex] = [];
         row.find('csm|data-point').each(
           function(pointIndex) {
             series.data[entryIndex][pointIndex] = [
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
      interval : 0
    },
    
    template : '<div class="chart"><div class="container"/></div>',
    
    /**
     * Read the feed data and update the chart.
     * 
     * @param data
     */
    update: function(data) {
      var container = this.node.find('.chart .container');
      var entries = data.find('atom|entry');
      var series = $.extend(true, {}, CaricaStatusMonitorChartSeries);
      series.read(entries);
      container.css('height', '200px');
      $.plot(container, series.data);
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
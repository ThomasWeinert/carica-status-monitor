/**
 * Plugin to load data series inside an atom feed and display a chart.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorChartSeries = {

    widget : null,

    data : [],

    /**
     * Read the data series from the xml into an array
     *
     * @param xml
     */
    read : function(xml) {
      series = this;
      xml.filter(':has(csm|data-point)').each(
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

  /**
   * A tooltip handler
   */
  var CaricaStatusMonitorChartTooltip = {

    widget : null,

    /**
     * Chart hover event
     *
     * @param event
     * @param pos
     * @param item
     */
    onHover : function(event, pos, item) {
      if (item) {
        var x = this.format(item.datapoint[0], item.series.xaxis.options.mode);
        var y = this.format(item.datapoint[1], item.series.yaxis.options.mode);
        var label = item.series.label + ": " + x + " = " + y;
        this.show(item.pageX, item.pageY, label);
      } else {
        this.hide();
      }
    },

    /**
     * Format the value depending on the axis mode (time)
     *
     * @param value
     * @param mode
     * @returns string
     */
    format : function(value, mode) {
      switch (mode) {
      case 'time' :
        return Globalize.format(new Date(value), "f");
      case 'milliseconds' :
        return this.widget.formatPeriod(value);
      case 'integer' :
        return Globalize.format(value, "n0");
      default :
        return Globalize.format(value, "n");
      }
    },

    /**
     * show the tooltip, if the dom element does not exists it will be created.
     *
     * @param x
     * @param y
     * @param label
     */
    show : function(x, y, label) {
      var node = $('#chartTooltip');
      if (node.length == 0) {
        node = this.create();
      }
      node.css({left: x + 5, top: y + 5});
      node.text(label);
      node.show();
    },

    /**
     * hide the tooltip (if it exists)
     */
    hide : function() {
      $('#chartTooltip').hide();
    },

    /**
     * Create the tooltop dom element
     * @returns HTMLNode
     */
    create : function() {
      return $('<div id="chartTooltip"/>').appendTo('body');
    }

  };

  var CaricaStatusMonitorChart = {

    options : {
      url : '',
      interval : 0,
      height : '200px',
      chart : 'lines',
      hover : 'yes'
    },

    template : '<div class="chart"><div class="container"/></div>',

    tooltip : null,

    plotTimer : null,

    /**
     * create a tooltip for the chart widget instance
     */
    prepare : function() {
      this.tooltip = $.extend(true, {}, CaricaStatusMonitorChartTooltip);
      this.tooltip.widget = this;
    },

    /**
     * Read the feed data and update the chart.
     *
     * @param data
     */
    update: function(xml) {
      var container = this.node.find('.chart .container');
      var entries = xml.find('atom|entry');
      var series = $.extend(true, {}, CaricaStatusMonitorChartSeries);
      series.widget = this;
      series.read(entries);
      container.css('height', this.options.height);

      var options = {
        legend: {
          show: true,
          position: 'nw',
          backgroundOpacity: 0.6
        },
        grid: {},
        series: {},
        xaxis: this.getAxisOptions(xml.find('csm|chart-options csm|axis-x')),
        yaxis: this.getAxisOptions(xml.find('csm|chart-options csm|axis-y'))
      };
      switch (this.options.chart) {
      case 'lines' :
        options.series.lines = { show: true };
        break;
      case 'points' :
        options.series.points = { show: true };
        break;
      case 'bars' :
        options.series.bars = { show: true };
        break;
      }
      if (this.options.hover == 'yes') {
        options.grid.hoverable = true;
        container.unbind().bind(
          'plothover',
          $.proxy(this.tooltip.onHover, this.tooltip)
        );
      }
      this.plot(container, series.data, options);
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
      if (container.width() > 0) {
        $.plot(container, data, options);
      } else {
        var that = this;
        this.plotTimer = window.setTimeout(
          function() {
            that.plot(container, data, options)
          },
          100
        );
      }
    },

    /**
     * Read the axis options from the xml node
     *
     * @param xml
     */
    getAxisOptions : function(xml) {
      var options = {
        min : xml.attr('min') > 0 ? xml.attr('min') : null,
        max : xml.attr('max') != '' ? xml.attr('max') : null
      };
      switch (xml.attr('mode')) {
      case 'time' :
        options.mode = 'time',
        options.timeformat = xml.attr('timeformat') != '' ? xml.attr('timeformat') : null;
        break;
      case 'milliseconds' :
        options.mode = 'milliseconds';
        options.tickFormatter = $.proxy(this.formatPeriod, this);
        break;
      case 'integer' :
        options.mode = 'integer';
        options.tickFormatter = $.proxy(this.formatNumeric, this);
        break;
      }
      return options;
    },

    /**
     * Calulate the different parts of a time period and build a label from it.
     *
     * Like: 42min 21s 345ms
     *
     * @param value
     * @param axis
     * @returns
     */
    formatPeriod : function(value, axis) {
      var labels = {
        y  : 'y',
        m : 'm',
        w : 'w',
        d : 'd',
        h : 'h',
        i : 'min',
        s : 's',
        ms : 'ms'
      };
      var numbers = {};
      var numberIndex = 0;
      var result = '';
      if (value == 0) {
        return 0;
      }
      numbers = $.CaricaStatusMonitor.Date.parsePeriod(value);
      for (var i in numbers) {
        if (numbers[i] > 0 || numberIndex > 0 || i == 'ms') {
          numberIndex++;
          if (numbers[i] > 0 || numberIndex == 1) {
            result += ' ' + numbers[i] + labels[i];
          }
        }
        if (numberIndex > 2) {
          break;
        }
      }
      return result.substr(1);
    },

    /**
     *
     * @param value
     * @param axis
     */
    formatNumeric : function(value, axis) {
      return $.CaricaStatusMonitor.Number.roundWithUnit(value);
    }
  };

  /**
   * jQuery selector handling to attach StatusChart to dom elements
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
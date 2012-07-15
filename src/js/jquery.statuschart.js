/**
 * Plugin to load data series inside an atom feed and display a chart.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){
  
  var StatusChartSeries = {
      
    data : [],
    
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

  var StatusChart = {

    node : null,

    options : {
      url : '',
      interval : 0
    },

    /**
     * Define namespaces for the css selectors
     */
    namespaces : {
      'atom' : 'http://www.w3.org/2005/Atom',
      'csm' : 'http://thomas.weinert.info/carica/ns/status-monitor'
    },

    /**
     * Store options and trigger first update
     *
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.node = node;
      this.options = $.extend(this.options, options, node.data());
      var header = this.node.find('h2');
      if (header.length > 0) {
        header.prepend('<span class="status"/>');
        header.after('<div class="message"/>');
      } else {
        this.node.append(
          '<div class="status"><span class="message">&nbsp;</span></div>'
        );
      }
      this.node.append('<div class="chart"><div class="container"/></div>');
      this.fetch();
    },

    /**
     * schedule a ajax refresh in n seconds, the currently scheduled refresh is 
     * stopped and removed.
     */
    schedule : function() {
      if (this.options.interval > 0) {
        if (this.timer) {
          window.clearTimeout(this.timer);
        }
        this.timer = window.setTimeout(
          $.proxy(this.fetch, this), 1000 * this.options.interval
        );
      }
    },

    /**
     * Fetch data
     */
    fetch : function() {
      if (this.options.url != '') {
        var url = this.options.url;
        $.get(url)
          .success($.proxy(this.ajaxSuccess, this));
      }
    },
    
    /**
     * Read dom returned from the Ajax request. Update the found items.
     *
     * @param data
     */
    read : function(data) {
      var chart = this;
      $(data).xmlns(
        chart.namespaces,
        function () {
          var entries = this.find('atom|entry');
          var series = $.extend(true, {}, StatusChartSeries);
          series.read(entries);
          chart.update(series.data);
        }
      );
    },
    
    update: function(series) {
      var container = this.node.find('.chart .container');
      container.css('height', '200px');
      $.plot(container, series);
    },
    
    /**
     * Update the status and message elements, if the are not showing something
     * they are hidden.
     */
    updateStatus : function(status, message) {
      var statusNode = this.node.find('.status');
      var messageNode = this.node.find('.message');
      statusNode.attr('class', 'status');
      messageNode.attr('class', 'message');
      if (status != 'none' && status != '') {
        statusNode.addClass(status).show();
        messageNode.addClass(status);
      } else {
        statusNode.hide();
      }
      if (message != '') {
        messageNode.text(message).show();
      } else {
        messageNode.html('&nbsp;').filter('div').hide();
      }
    },

    /**
     * Ajax request successful callback. If the response contains a string
     * try to convert it into a dom.
     *
     * @param data
     */
    ajaxSuccess : function(data) {
      if (typeof data == 'string') {
        data = new DOMParser().parseFromString(data, 'text/xml');
      }
      this.read(data);
      this.updateStatus('none', '');
      this.schedule();
    },

    /**
     * Ajax request error callback. Display the error in the status element.
     *
     * @param data
     */
    ajaxError : function(data) {
      this.updateStatus('error', data.status + ' ' + data.statusText);
      this.schedule();
    }
  };

  /**
   * Activate the hash replacement for an element
   */
  $.fn.StatusChart = function(options) {
    return this.each(
      function() {
        var replace = $.extend(true, {}, StatusChart);
        replace.setUp($(this), options);
      }
    );
  };

})(jQuery);
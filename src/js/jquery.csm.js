/**
 * The Status Monitor loader
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitor = {

    requires : [
      'js/external/jquery.xmlns.js',
      'js/external/globalize.js',
      'js/external/cultures/globalize.cultures.js',
      'js/external/jquery.flot.js',
      'js/jquery.csm.widget.js'
    ],

    plugins : {
      feed : {
        file : 'js/jquery.csm.atomreader.js',
        object : 'CaricaStatusMonitorAtomReader'
      },
      chart : {
        file : 'js/jquery.csm.chart.js',
        object : 'CaricaStatusMonitorChart'
      },
      clock : {
        file : 'js/jquery.csm.weatherclock.js',
        object : 'CaricaStatusMonitorWeatherClock'
      },
      countdown : {
        file : 'js/jquery.csm.countdown.js',
        object : 'CaricaStatusMonitorCountdown'
      },
      hash : {
        file : 'js/jquery.csm.hashreplace.js',
        object : 'CaricaStatusMonitorHashReplace'
      }
    },

    requireCounter : 0,

    options : {
      /* dynamic loading (or expect the files loaded by html) */
      dynamicLoading : true,
      /* cache script files */
      cache : false,
      /* locale for date time formatting */
      locale : 'en'
    },

    pluginOptions : {
      hash : {
        onUpdate : function() {
          jQuery('[data-plugin~=feed]').each(
            function () {
              $(this).data('Widget').fetch();
            }
          );
        }
      }
    },

    /**
     * Store options and trigger requires loading
     * @param object options
     */
    setUp : function(options, pluginOptions) {
      this.options = $.extend(this.options, options);
      this.pluginOptions = $.extend(this.pluginOptions, pluginOptions);
      if (this.options.dynamicLoading) {
        this.loadRequires();
      } else {
        this.applyPlugins();
      }
    },

    /**
     * Load all requires
     */
    loadRequires : function() {
      for (var i in this.requires) {
        $.ajax(
          {
            url : this.requires[i],
            cache: this.options.cache,
            dataType: "script"
          }
        ).done(
          $.proxy(this.onRequireCompleted, this)
        );
      }
    },

    /**
     * Count the finished requires. If all are done trigger the plugin loading.
     */
    onRequireCompleted : function() {
      this.requireCounter++;
      if (this.requireCounter >= this.requires.length) {
        Globalize.culture(this.options.locale);
        this.loadPlugins();
      }
    },

    /**
     * Directly apply the plugins to the dom, expects loading already done
     */
    applyPlugins : function() {
      var nodes, options, plugin;
      for (var name in this.plugins) {
        nodes = $('[data-plugin~='+name+']');
        if (nodes.length > 0) {
          plugin = this.plugins[name];
          if (nodes[plugin.object]) {
            options = $.extend({}, this.pluginOptions[name]);
            nodes[plugin.object](options);
          } else {
            console.error('Plugin "' + plugin.object + '" not found.');
          }
        }
      }
    },

    /**
     * Call loadPlugin for each plugin
     */
    loadPlugins : function() {
      for (plugin in this.plugins) {
        this.loadPlugin(plugin, this.plugins[plugin]);
      }
    },

    /**
     * Select dom nodes using a plugin. Is one ore more are found
     * load the plugin and applyaw it.
     *
     * @param string name
     * @param object plugin
     */
    loadPlugin : function(name, plugin) {
      var nodes = $('[data-plugin~='+name+']');
      if (nodes.length > 0) {
        var options = $.extend({}, this.pluginOptions[name]);
        $.ajax(
          {
            url : plugin.file,
            cache: this.options.cache,
            dataType: "script"
          }
        ).success(
          function (plugin, options, nodes) {
            return function() {
              $(nodes)[plugin.object](options);
            };
          }(plugin, options, nodes)
        );
      }
    }
  };

  /**
   * Activate the status monitor, load required scripts and used plugins
   */
  $.CaricaStatusMonitor = function(options, pluginOptions) {
    CaricaStatusMonitor.setUp(options, pluginOptions);
    return this;
  };

  /**
   * Convert the iCalendar date format into one parseable by
   * the Date() object and use Globalize to format the date
   *
   * @param node
   * @returns string
   */
  $.CaricaStatusMonitor.Xcalendar = {
    parseDate : function(xcalDate, format) {
      var dateString =
        xcalDate.substr(0, 4) + '-' +
        xcalDate.substr(4, 2) + '-' +
        xcalDate.substr(6, 2);
      if (format == 'DATE-TIME') {
        dateString +=
          xcalDate.substr(8, 3) + ':' +
          xcalDate.substr(11, 2) + ':' +
          xcalDate.substr(13);
      }
      return date = new Date(dateString);
    }
  };

  $.CaricaStatusMonitor.Date = {

    periods : {
      y  : 31556926000,
      m : 2629743830,
      w : 604800000,
      d : 86400000,
      h : 3600000,
      i : 60000,
      s : 1000
    },

    parsePeriod : function(milliseconds) {
      var period = {};
      period.sign = (milliseconds > 0) ? '+' : '-';
      milliseconds = Math.abs(milliseconds);
      for (var i in this.periods) {
        period[i] = Math.floor(milliseconds / this.periods[i]);
        milliseconds -= period[i] * this.periods[i];
      }
      period.ms = milliseconds;
      return period;
    }
  };

})(jQuery);
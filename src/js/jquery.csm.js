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
      if (this.options.loading) {
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
      var nodes, options;
      for (plugin in this.plugins) {
        nodes = $('[data-plugin~='+plugin+']');
        if (nodes.length > 0) {
          options = $.extend({}, this.pluginOptions[plugin]);
          nodes[this.plugins[plugin].object](options);
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

})(jQuery);
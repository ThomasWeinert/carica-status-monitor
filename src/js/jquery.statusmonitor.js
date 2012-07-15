/**
 * The Status Monitor loader
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var StatusMonitor = {
    
    requires : [
      'js/external/jquery.xmlns.js',
      'js/external/globalize.js',
      'js/external/cultures/globalize.cultures.js',
      'js/external/jquery.flot.js',
      'js/jquery.statuswidget.js'
    ],
      
    plugins : {
      feed : {
        file : 'js/jquery.atomreader.js',
        object : 'AtomReader'
      },
      chart : {
        file : 'js/jquery.statuschart.js',
        object : 'StatusChart'
      },
      clock : {
        file : 'js/jquery.weatherclock.js',
        object : 'WeatherClock'
      },
      hash : {
        file : 'js/jquery.hashreplace.js',
        object : 'HashReplace'
      }
    },
    
    requireCounter : 0,
    
    options : {
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
      this.loadRequires();
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
      var options = $.extend({}, this.pluginOptions[name]);
      if (nodes.length > 0) {
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
  $.StatusMonitor = function(options, pluginOptions) {
    StatusMonitor.setUp(options, pluginOptions);
    return this;
  };

})(jQuery);
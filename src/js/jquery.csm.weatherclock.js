/**
 * Plugin that updates a clock
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorWeatherClock = {

    node : null,

    template :
      '<div class="digits">' +
        '<span class="digit hours">00</span>' +
        '<span class="digit minutes">00</span>' +
      '</div>' +
      '<div class="details">' +
        '<span class="date"> </span>' +
        '<span class="location"> </span>' +
        '<span class="text"> </span>' +
        '<span class="temperature"> </span>' +
      '</div>',

    /**
     * Define namespaces for the css selectors
     */
    namespaces : {
      'atom' : 'http://www.w3.org/2005/Atom',
      'yweather' : 'http://xml.weather.yahoo.com/ns/rss/1.0'
    },

    options : {
      url : 'feeds/yweather.php?location=667931',
      weather : 'yes',
      interval : 600,
      basePath : ''
    },

    /**
     * Bind event handler, activate the update interval.
     *
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.node = node;
      this.options = $.extend(this.options, options, node.data());
      this.create(node);
      this.update();
      window.setInterval(
        $.proxy(this.update, this), 5000
      );
      if (this.options.weather != 'no') {
        this.fetchWeather();
        if (this.options.interval > 0) {
          window.setInterval(
            $.proxy(this.fetchWeather, this), this.options.interval * 1000
          );
        }
      }
    },

    create : function(parent) {
      parent.append(this.template);
    },

    formatDigit : function(value) {
      if (value < 10) {
        return '0' + value;
      } else {
        return value;
      }
    },

    /**
     * Update clock data
     */
    update : function() {
      var now = new Date();
      this.node.find('.hours').text(this.formatDigit(now.getHours()));
      this.node.find('.minutes').text(this.formatDigit(now.getMinutes()));
      this.node.find('.date').text(Globalize.format(now, "d"));
    },

    /**
     * Featch weather data feed
     */
    fetchWeather : function() {
      if (this.options.url != '') {
        var url = this.options.basePath + this.options.url;
        $.get(url)
          .success($.proxy(this.ajaxSuccess, this));
      }
    },

    /**
     * read the weather data from the xml and update the dom elements
     */
    readWeather : function(data) {
      var clock = this;
      $(data).xmlns(
        clock.namespaces,
        function () {
          $(data).xpath().evaluate('//yweather:condition[0]').each(
            function() {
              var xpath = $(this).xpath();
              clock.node.find('.text').text(xpath.evaluate('string(@text)'));
              clock.node.find('.location').text(
                xpath.evaluate('string(yweather:location/@city)')
              );
              clock.node.find('.temperature').text(
                xpath.evaluate('string(@temp)') + '°' +
                xpath.evaluate('string(yweather:units/@temperature)')
              );
              clock.node.css(
                'background-image',
                'url('+$(data).xpath().evaluate('string(atom:link[rel=image]/@href)')+')'
              );
            }
          );
        }
      );
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
      this.readWeather($(data));
    }
  };

  /**
   * Activate the Weather clock inside the given container
   */
  $.fn.CaricaStatusMonitorWeatherClock = function(options) {
    return this.each(
      function() {
        var instance = $.extend(true, {}, CaricaStatusMonitorWeatherClock);
        instance.setUp($(this), options);
      }
    );
  };

})(jQuery);
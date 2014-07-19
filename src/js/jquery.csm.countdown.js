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
      targetTime : 0,

      periodTitles : {
        y  : 'years',
        m : 'months',
        w : 'weeks',
        d : 'days',
        h : 'hours',
        i : 'minutes',
        s : 'seconds'
      },

      template :
        '<li class="item">' +
          '<div class="timer">' +
            '<div class="numberIcon countdownNumber1">' +
              '<div class="number">00</div>' +
              '<div class="title"> </div>' +
            '</div>' +
            '<div class="numberIcon countdownNumber2">' +
              '<div class="number">00</div>' +
              '<div class="title"> </div>' +
            '</div>' +
            '<div class="numberIcon countdownNumber3">' +
              '<div class="number">00</div>' +
              '<div class="title"> </div>' +
            '</div>' +
          '</div>' +
          '<div class="teaser">' +
            '<h3/>' +
            '<div class="summary"/>' +
          '</div>' +
          '<span class="spacer"></span>' +
        '</li>',

      updateData : function(data, xml) {
        this.node.find('h3').text(xml.evaluate('string(atom:title)'));
        this.targetTime = $.CaricaStatusMonitor.Xcalendar.parseDate(
          xml.evaluate('string(.//xcal:dtstart)'),
          xml.evaluate('string(.//xcal:dtstart/@tzoffset)')
        );
        this.node.find('.summary').text(xml.evaluate('atom:summary'));
        this.refresh();
      },

      refresh : function() {
        var now = new Date();
        var difference = this.targetTime.getTime() - now.getTime();
        if (difference <= 0) {
          this.node.addClass('fatal').removeClass('labelApproaching');
        } else if (difference < this.entries.widget.options.approachingLimitMS) {
          this.node.addClass('labelApproaching').removeClass('fatal');
        } else {
          this.node.removeClass('fatal');
        }
        var numbers = $.CaricaStatusMonitor.Date.parsePeriod(difference);
        var numberIndex = 0;
        var numberIcon = null;
        for (var i in numbers) {
          if (numbers[i] > 0 || numberIndex > 0 || i == 'h') {
            numberIndex++;
            numberIcon = this.node.find('.countdownNumber' + numberIndex);
            numberIcon.find('.number').text(numbers[i]);
            numberIcon.find('.title').text(this.periodTitles[i]);
          }
          if (numberIndex > 2) {
            break;
          }
        }
      }
    }
  );

  var CaricaStatusMonitorCountdown = {

    entries : null,

    options : {
      url : '',
      interval : 0,
      max : 5,
      approachingLimit : 86400 // seconds
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
      this.options.approachingLimitMS = this.options.approachingLimit * 1000;
      setInterval($.proxy(this.refreshEntries, this), 1000);
    },

    /**
     * update countdown entries
     */
    refreshEntries : function() {
      this.entries.refresh();
    },

    /**
     * Read the feed data and update the countdown items.
     *
     * @param data
     */
    update: function(xml) {
      var entry, data, prototype;
      var entries = xml.xpath().evaluate('//atom:entry').toArray();
      var max = this.options.max;
      if (entries.length < max) {
        max = entries.length;
      }
      this.entries.clear();
      for (var i = max; i > 0; i--) {
        entry = $(entries[i - 1]).xpath();
        data = new Object();
        data.id = entry.evaluate('string(atom:id)');
        data.updated = entry.evaluate('string(atom:updated)');
        if (entry.evaluate('count(xcal:vevent) > 0')) {
          prototype = CaricaStatusMonitorCountdownEntry;
          this.entries.get(data.id, prototype).update(data, entry);
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
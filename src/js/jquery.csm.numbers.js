/**
 * Plugin to load an xml including xcal:vevents and display all events the
 * events as countdowns.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorNumbersEntry = $.extend(
    true,
    $.CaricaStatusMonitorWidget.Entry(),
    {
      template :
        '<li class="item">' +
          '<div class="spriteIcon icon">' +
            '<div class="sprite"> </div>' +
            '<div class="title"></div>' +
          '</div>' +
          '<h3/>' +
          '<div class="numbers">' +
          '</div>' +
          '<span class="updated"></span>' +
          '<span class="spacer"></span>' +
        '</li>',

      numberTemplate :
        '<div class="numberIcon">' +
          '<div class="number">00</div>' +
          '<div class="title background-highlight"> </div>' +
        '</div>',

      updateData : function(data, entry) {
        var numbersNode, numberNode, number;
        this.node.find('h3').text(entry.evaluate('string(atom:title)'));
        numbersNode = this.node.find('div.numbers');
        numbersNode.children().remove();
        var numbers = entry.evaluate('.//csm:number').toArray();
        for (var i = 0; i < numbers.length; i++) {
          number = $(numbers[i]);
          numberNode = $($(this.numberTemplate).appendTo(numbersNode)[0]);
          numberNode.find('.number').text(
            $.CaricaStatusMonitor.Number.roundWithUnit(
              parseInt(number.text())
            )
          );
          numberNode.find('.title').text(number.attr('title'));
          numberNode.find('.title').css('background', number.attr('color') || 'black');
        }
        var iconNode = this.node.find('.icon');
        var icon = entry.evaluate('string(csm:icon/@src)');
        if (!icon || icon == '') {
          icon = entry.evaluate('string(atom:link[@rel="image"]/@href)');
        }
        if (icon || this.defaultIcon) {
          iconNode.css(
            'background-image', 'url(' + (icon ? icon : this.defaultIcon) + ')'
          );
          iconNode.addClass('hasImage');
        } else {
          iconNode.css(
            'background-image', 'none'
          );
          iconNode.removeClass('hasImage');
        }
        var iconText = entry.evaluate('string(csm:icon/@text)');
        if (iconText) {
          iconNode.find('.sprite').text(iconText);
          iconNode.addClass('hasText');
        } else {
          iconNode.find('.sprite').text(' ');
          iconNode.removeClass('hasText');
        }
        this.node.find('.updated').text(Globalize.format(this.updated, "f"));
      }
    }
  );

  var CaricaStatusMonitorNumbers = {

    entries : null,

    options : {
      url : '',
      interval : 0,
      max : 5
    },

    template : '<ul class="numbers"/>',


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
        if (entry.evaluate('count(.//csm:number) > 0')) {
          prototype = CaricaStatusMonitorNumbersEntry;
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
  $.fn.CaricaStatusMonitorNumbers = function(options) {
    return this.each(
      function() {
        var widget = $.extend(
          true, $.CaricaStatusMonitorWidget(), CaricaStatusMonitorNumbers
        );
        widget.setUp($(this), options);
      }
    );
  };

})(jQuery);
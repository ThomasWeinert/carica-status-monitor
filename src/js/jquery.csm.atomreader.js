/**
* A jQuery based Atom reader. It adds an <ul> to the element and
* <li> for the items.
**
* Use data-Attributes to define options.
*
* Usage:
*
* <div data-plugin="feed" data-url="..." data-interval="120" data-max="10"/>
*
* jQuery('ul[data-plugin="feed"]').CaricaStatusMonitorAtomReader();
*
* Options:
*
* url: the Atom feed url, required
* interval: refresh time in seconds, 0 = no refresh, default = 0
* max: maximum items default = 5
* highlight: higlight new/changed items, default = yes
*
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/
(function($){

  var CaricaStatusMonitorAtomReaderEntry = $.extend(
    true,
    $.CaricaStatusMonitorWidget.Entry(),
    {

      defaultIcon : '',
      link : null,

      template :
        '<li class="item">' +
          '<div class="spriteIcon icon">' +
            '<div class="sprite"> </div>' +
            '<div class="title"> </div>' +
          '</div>' +
          '<div class="teaser">' +
            '<h3/>' +
            '<div class="summary"/>' +
          '</div>' +
          '<span class="updated"></span>' +
          '<span class="spacer"></span>' +
        '</li>',

      /**
       * Handle a click on the element
       *
       * @param event
       */
      onClick : function(event) {
        event.preventDefault();
        if (this.link && this.link != '') {
          window.open(this.link, '_blank');
        }
      },

      /**
       * Update data of the entry
       *
       * @param data
       * @param entry
       */
      updateData : function(data, entry) {
        this.updateNode(data, entry);
        this.updateLink(data, entry);
        if (this.entries.widget.options.highlight == 'yes') {
          this.node.addClass('changed');
        }
      },

      /**
       * Read values from entry xml and update the dom element
       *
       * @param data
       * @param entry
       */
      updateNode : function(data, entry) {
        var status = entry.find('csm|status').text();
        this.node.attr('class', 'item').addClass(
          (status != '') ? status : 'information'
        );
        var iconNode = this.node.find('.icon');
        var icon = entry.find('csm|icon').attr('src');
        if (!icon || icon == '') {
          icon = entry.find('atom|link[rel=image]').attr('href');
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
        iconNode.removeClass('rotate');
        switch (entry.find('csm|icon').attr('animation')) {
          case 'rotate' :
            iconNode.addClass('rotate');
            break;
          case 'bounce' :
            iconNode.addClass('bounce');
            break;
        }
        var iconText = entry.find('csm|icon').attr('text');
        if (iconText) {
          iconNode.find('.sprite').text(iconText);
          iconNode.addClass('hasText');
        } else {
          iconNode.find('.sprite').text(' ');
          iconNode.removeClass('hasText');
        }
        var iconTitle = entry.find('csm|icon').attr('title');
        if (iconTitle) {
          iconNode.find('.title').text(iconTitle).show();
          iconNode.addClass('hasTitle');
        } else {
          iconNode.find('.title').text(' ').hide();
          iconNode.removeClass('hasTitle');
        }
        this.updateTeaser(data, entry);
        this.node.find('.updated').text(Globalize.format(this.updated, "f"));
      },

      /**
       * Read values from entry xml and update the teaser summary text
       *
       * @param data
       * @param entry
       */
      updateTeaser : function(data, entry) {
        this.node.find('h3').text(entry.find('atom|title').text());
        var type = entry.find('atom|summary').attr('type');
        var summary = this.node.find('.summary');
        var teaser;
        if (type == 'html') {
          teaser = $($.parseHTML(entry.find('atom|summary').text()));
        } else if (type == 'xhtml') {
          teaser = entry.find('atom|summary').children();
        } else {
          summary.text(entry.find('atom|summary').text());
          return;
        }
        if (this.entries.widget.options.allowHtml != 'yes') {
          summary.text(teaser.text());
          return;
        }
        summary.empty();
        summary.append(teaser.clone());
        this.expandHrefs(summary.find('a[href],img[src]'), entry.find('atom|link').attr('href'));
      },

      /**
       * Read values from entry xml and update the click action data
       *
       * @param data
       * @param entry
       */
      updateLink : function(data, entry) {
        this.link = entry
          .find('atom|link[type="text/html"],atom|link[rel=alternate]')
          .first()
          .attr('href');
        if (this.link && this.link != '') {
          this.node.addClass('clickable');
        } else {
          this.node.removeClass('clickable');
        }
      },

      /**
       * Makes href/src attributes of the matches elements absolute
       *
       * @param nodes
       * @param baseHref
       */
      expandHrefs : function(nodes, baseHref) {
        if (baseHref) {
          if (baseHref.lastIndexOf('#') > 0) {
            baseHref = baseHref.substr(0, baseHref.lastIndexOf('#'));
          }
          if (baseHref.lastIndexOf('?') > 0) {
            baseHref = baseHref.substr(0, baseHref.lastIndexOf('?'));
          }
          if (baseHref.lastIndexOf('/') > 0) {
            baseHref = baseHref.substr(0, baseHref.lastIndexOf('/') + 1);
          }
          nodes.each(
            function () {
              if (this.getAttribute('href')) {
                var href = this.getAttribute('href');
                if (!href.match(/^\w+:/)) {
                  this.setAttribute('href', baseHref + href);
                }
                $(this).click(
                  function(href) {
                    return function(event) {
                      event.preventDefault();
                      window.open(href, '_blank');
                    };
                  }(this.getAttribute('href'))
                );
              }
              if (this.getAttribute('src')) {
                var href = this.getAttribute('src');
                if (!href.match(/^\w+:/)) {
                  this.setAttribute('src', baseHref + href);
                }
              }
            }
          );
        }
      }
    }
  );

  var CaricaStatusMonitorAtomReaderEntryXCal = $.extend(
    true,
    {},
    CaricaStatusMonitorAtomReaderEntry,
    {
      template :
        '<li class="item">' +
          '<div class="numberIcon">'+
            '<span class="number"/>'+
            '<span class="title"/>'+
          '</div>' +
          '<div class="teaser">' +
            '<h3/>' +
            '<div class="summary"/>' +
          '</div>' +
          '<span class="updated"></span>' +
          '<span class="spacer"></span>' +
         '</li>',

      /**
       * Read values from entry xml and update the dom element
       *
       * @param data
       * @param entry
       */
      updateNode : function(data, entry) {
        var status = entry.find('csm|status').text();
        this.node.attr('class', 'item').addClass(
          (status != '') ? status : 'information'
        );
        var startDate = this.parseEventDate(entry.find('xcal|dtstart'));
        var endDate = this.parseEventDate(entry.find('xcal|dtend'));
        var startDateFormat = entry.find('xcal|dtstart').attr('value');
        this.node.find('.numberIcon .title').text(Globalize.format(startDate, "MMM"));
        this.node.find('.numberIcon .number').text(Globalize.format(startDate, " d"));
        if (Globalize.format(startDate, "d") == Globalize.format(new Date(), "d")) {
          this.node.find('.numberIcon').removeClass('labelAllDay').addClass('labelToday');
        } else if (startDateFormat != 'DATE-TIME') {
          this.node.find('.numberIcon').removeClass('labelToday').addClass('labelAllDay');
        } else {
          this.node.find('.numberIcon').removeClass('labelToday').removeClass('labelAllDay');
        }
        this.updateTeaser(data, entry);
        if ((startDateFormat == 'DATE-TIME') ||
            (startDateFormat != 'DATE' && endDate > startDate)) {
          this.node.find('h3').prepend(
            document.createTextNode(
              Globalize.format(startDate, "t") +
              ' - ' +
              Globalize.format(endDate, "t") +
              ' '
            )
          );
        }
        this.node.find('.updated').text(Globalize.format(this.updated, "f"));
      },

      parseEventDate : function(node) {
        return $.CaricaStatusMonitor.Xcalendar.parseDate(node.text(), node.attr('tzoffset'));
      }
    }
  );

  var CaricaStatusMonitorAtomReader = {

    entries : null,
    timer : null,

    options : {
      url : '',
      highlight : 'yes',
      refresh : 'updated',
      max : 5,
      interval : 0,
      allowHtml : 'no'
    },

    /**
     * Define namespaces for the css selectors
     */
    namespaces : {
      'atom' : 'http://www.w3.org/2005/Atom',
      'xcal' : 'urn:ietf:params:xml:ns:xcal',
      'csm' : 'http://thomas.weinert.info/carica/ns/status-monitor'
    },

    template : '<ul class="feed"/>',

    /**
     * Prapare the object before fetching the data
     */
    prepare : function() {
      this.entries = $.CaricaStatusMonitorWidget.Entries();
      this.entries.setUp(this, this.node.find('ul'));
      if (this.options.refresh == 'all') {
        this.options.highlight = 'no';
      }
    },

    /**
     * Read dom returned from the Ajax request. Update the found items.
     *
     * @param xml
     */
    update : function(xml) {
      var entry, data, prototype;
      var entries = xml.find('atom|entry');
      var max = this.options.max;
      if (this.options.refresh == 'all') {
        this.entries.clear();
      }
      for (var i = max; i > 0; i--) {
        entry = entries.eq(i - 1);
        if (entry.length > 0) {
          data = new Object();
          data.id = entry.find('atom|id').text();
          data.updated = entry.find('atom|updated').text();
          if (entry.find('xcal|vevent').length > 0) {
            prototype = CaricaStatusMonitorAtomReaderEntryXCal;
          } else {
            prototype = CaricaStatusMonitorAtomReaderEntry;
          }
          this.entries.get(data.id, prototype).update(data, entry);
        }
      }
    }
  };

  /**
   * jQuery selector handling to attach Atom reader to list elements
   *
   * @param options
   */
  $.fn.CaricaStatusMonitorAtomReader = function(options) {
    return this.each(
      function() {
        var widget = $.extend(
          true, $.CaricaStatusMonitorWidget(), CaricaStatusMonitorAtomReader
        );
        widget.setUp($(this), options);
      }
    );
  };

})(jQuery);
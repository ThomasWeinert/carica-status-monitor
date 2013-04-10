/**
 * Superclass for widget plugins. It handles stuff like loading and
 * display errors. The "update" method needs to be defined to contains the
 * widget logic.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */
(function($){

  var CaricaStatusMonitorWidgetEntry = {

    entries : null,
    node : null,

    id : null,
    updated : null,
    link : null,

    template : '',

    /**
     * Update an entry if new data is available. This contains
     * an implicit create for the dom elements
     *
     * @param data
     * @param entry
     */
    update : function(data, entry) {
      var updated = new Date(data.updated);
      if (this.node) {
        if (updated <= this.updated) {
          return;
        }
        this.hide();
      } else {
        this.create();
      }
      this.id = data.id;
      this.updated = updated;
      this.updateData(data, entry);
      this.show();
    },

    updateData : function(data, entry) {
    },

    /**
     * refresh display with current data
     */
    refresh : function() {
    },

    /**
     * Handle a click on the element
     *
     * @param event
     */
    onClick : function(event) {
    },

    /**
     * Move an item to the top of the list and show it.
     */
    show : function() {
      this.entries.widget.node.find('ul').prepend(this.node);
      if (this.entries.widget.options.refresh == 'updated') {
        this.node.fadeIn(3000);
      } else {
        this.node.show();
      }
    },

    /**
     * Hide the dom element if here is one.
     */
    hide : function() {
      if (this.node) {
        this.node.hide();
      }
    },

    /**
     * Remove the dom elements and the item.
     */
    remove : function() {
      this.hide();
      if (this.node) {
        this.node.remove();
        if (this.entries[this.id]) {
          delete(this.entries[this.id]);
        }
      }
    },

    /**
     * Create the dom elements for the item
     */
    create : function() {
      var nodes = this.entries.widget.node.find('li');
      for (var i = nodes.length; i > 1, i >=  this.entries.widget.options.max; i--) {
        nodes.eq(i - 1).data('widgetEntry').remove();
      }
      this.node = $(this.template).clone();
      this.node.data('widgetEntry', this);
      this.node.click($.proxy(this.onClick, this));
    }
  };

  /** a list of items for an widget */
  var CaricaStatusMonitorWidgetEntries = {

    widget : null,
    entries : {},
    node : null,

    /**
     * Setup object for for the widget
     *
     * @param widget
     * @param node
     * @param entryPrototype
     */
    setUp : function (widget, node) {
      this.widget = widget;
      this.node = node;
    },

    /**
     * Get an entry item for the provided id, creates the item
     * if it is not found.
     *
     * @param string id
     * @returns
     */
    get : function(id, prototype) {
      if (!this.entries[id]) {
        this.entries[id] = $.extend(
          true, {}, prototype
        );
        this.entries[id].entries = this;
      }
      return this.entries[id];
    },

    /**
     * Remove an item from the list, calls remove on the item, too.
     * @param id
     */
    remove : function(id) {
      if (this.entries[id]) {
        var entry = this.entries[id];
        delete(this.entries[id]);
        entry.remove();
      }
    },

    /**
     * Remove all items and their dom elements
     */
    clear : function() {
      this.node.empty();
      this.entries = new Object();
    },

    refresh : function() {
      for (var i in this.entries) {
        this.entries[i].refresh();
      }
    }
  };

  var CaricaStatusMonitorWidget = {

    node : null,

    options : {
      url : '',
      interval : 0,
      refresh : 'all',
      highlight : 'no',
      basePath : '',
      showStatus : 'yes'
    },

    /**
     * Define namespaces for the css selectors
     */
    namespaces : {
      'atom' : 'http://www.w3.org/2005/Atom',
      'xcal' : 'urn:ietf:params:xml:ns:xcal',
      'csm' : 'http://thomas.weinert.info/carica/ns/status-monitor'
    },

    template : null,

    /**
     * Store options and trigger first update
     *
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.node = node;
      this.node.data('Widget', this);
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
      if (this.template) {
        this.node.append(this.template);
      }
      this.prepare();
      this.fetch();
    },

    /**
     * Prepare the widget instance. This method can be redefined by
     * the actual widget objects
     *
     * @param data
     */
    prepare : function() {
    },

    /**
     * Update widget with data from feed. This method need to be redefined by
     * the actual widget objects
     *
     * @param data
     */
    update : function(data) {
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
     * Fetch the feed from the url. The url will be modified if it contains
     * the joker {hash}. It will be replaced with the current fragment/hash.
     */
    fetch : function() {
      if (this.options.url != '') {
        var href = window.location.href;
        var hash = (href.lastIndexOf('#') > 0)
         ? href.substr(href.lastIndexOf('#') + 1)
         : '';
        var url = this.options.url.replace(/\{hash\}/, hash);
        this.updateStatus('loading', '');
        $.get(url)
          .success($.proxy(this.ajaxSuccess, this))
          .error($.proxy(this.ajaxError, this));
      }
    },

    /**
     * Read dom returned from the Ajax request. Update the found items.
     *
     * @param data
     */
    read : function(data) {
      var widget = this;
      $(data).xmlns(
        widget.namespaces,
        function () {
          widget.update($(data));
        }
      );
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
      if (status != 'none' && status != '' && this.options.showStatus != 'no') {
        statusNode.addClass(status).show();
        messageNode.addClass(status);
      } else {
        statusNode.hide();
      }
      if (message != '' && this.options.showStatus != 'no') {
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
   * Get the a clone of the StatusWidget object
   */
  $.CaricaStatusMonitorWidget = function() {
    return $.extend(true, {}, CaricaStatusMonitorWidget);
  };

  $.CaricaStatusMonitorWidget.Entries = function() {
    return $.extend(true, {}, CaricaStatusMonitorWidgetEntries);
  };

  $.CaricaStatusMonitorWidget.Entry = function() {
    return $.extend(true, {}, CaricaStatusMonitorWidgetEntry);
  };

})(jQuery);
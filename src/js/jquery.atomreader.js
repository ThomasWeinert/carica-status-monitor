/**
* A jQuery based Atom reader. It adds li elements to the given list
* element depending on the fetched feed.
**
* Use data-Attributes in the list element to define options.
*
* Usage:
*
* <ul data-plugin="feed" data-url="..." data-interval="120" data-max="10">
*
* jQuery('ul[data-plugin="feed"]').AtomReader();
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

  var AtomReaderEntry = {

    entries : null,
    node : null,

    defaultIcon : null,

    id : null,
    updated : null,
    
    template : 
      '<li class="entry">' +
        '<img src="img/dialog-information.png" class="icon"/>' +
        '<h3>Title</h3>' +
        '<p>Summary</p>' +
        '<span class="updated"></span>' +
        '<span class="spacer"></span>' +
      '</li>',

    /**
     * Update an entry if new data is available. This contains
     * an implicit create for the dom elements
     *
     * @param object data
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
      this.updateNode(data, entry);
      if (this.entries.reader.options.highlight == 'yes') {
        this.node.addClass('changed');
      }
      this.show();
    },
    
    /**
     * Read values from entry xml and update the dom element
     * 
     * @param data
     * @param entry
     */
    updateNode : function(data, entry) {
      var status = entry.find('csm|status').text();
      var icon = entry.find('csm|icon').attr('src');
      if (!icon || icon == '') {
        icon = entry.find('atom|link[rel=image]').attr('href');
      }
      this.node.attr('class', 'entry').addClass(
        (status != '') ? status : 'information'
      );
      this.node.find('img').attr(
        'src', (icon != '') ? icon : this.defaultIcon
      );
      this.node.find('h3').text(entry.find('atom|title').text());
      this.node.find('p').text(entry.find('atom|summary').text());
      this.node.find('.updated').text(Globalize.format(this.updated, "f"));
    },

    /**
     * Move an item to the top of the list and show it.
     */
    show : function() {
      this.entries.reader.node.find('li.message, li.status, li.title').last().after(this.node);
      if (this.entries.reader.options.refresh == 'updated') {
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
    create : function () {
      var nodes = this.entries.reader.node.find('li.entry');
      for (var i = nodes.length; i > 1, i >=  this.entries.reader.options.max; i--) {
        nodes.eq(i - 1).data('atomReaderEntry').remove();
      }
      this.node = $(this.template).clone();
      this.node.data('atomReaderEntry', this);
      this.defaultIcon = this.node.find('img').attr('src');
    }
  };

  var AtomReaderEntries = {

    reader : null,
    entries : {},

    /**
     * Get an entry item for the provided id, creates the item
     * if it is not found.
     *
     * @param string id
     * @returns
     */
    get : function(id) {
      if (!this.entries[id]) {
        this.entries[id] = $.extend(
          true, {}, AtomReaderEntry
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
      this.reader.node.find('.entry').remove();
      this.entries = new Object();
    }
  };

  var AtomReader = {

    node : null,
    entries : null,

    options : {
      url : '',
      highlight : 'yes',
      refresh : 'updated',
      max : 5,
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
     * Set up a new Atom reader instance
     * @param node
     * @param options
     */
    setUp : function(node, options) {
      this.node = node;
      this.node.data('AtomReader', this);
      this.entries = $.extend(true, {}, AtomReaderEntries);
      this.entries.reader = this;
      this.options = $.extend($.extend(this.options, options), node.data());
      var header = this.node.find('.header');
      if (header.length > 0) {
        header.prepend('<span class="status"/>');
        header.after('<li class="message"/>');
      } else {
        this.node.append('<li class="status"><span class="message"/></li>');
      }
      if (this.options.refresh == 'all') {
        this.options.highlight = 'no';
      }
      this.fetch();
    },

    schedule : function() {
      if (this.options.interval > 0) {
        window.setTimeout(
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
        var hash = window.location.hash ? window.location.hash : '';
        var url = this.options.url.replace(/\{hash\}/, escape(hash));
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
      var reader = this;
      $(data).xmlns(
        reader.namespaces,
        function () {
          var entries = this.find('atom|entry');
          var max = reader.options.max;
          if (reader.options.refresh == 'all') {
            reader.entries.clear();
          }
          for (var i = max; i > 0; i--) {
            var entry = entries.eq(i - 1);
            if (entry.length > 0) {
              var data = new Object();
              data.id = entry.find('atom|id').text();
              data.updated = entry.find('atom|updated').text();
              reader.entries.get(data.id).update(data, entry);
            }
          }
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
      if (status != 'none' && status != '') {
        statusNode.addClass(status).show();
        messageNode.addClass(status);
      } else {
        statusNode.hide();
      }
      if (message != '') {
        messageNode.text(message).show();
      } else {
        messageNode.text('').hide();
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
   * jQuery selector handling to attach Atom reader to list elements
   */
  $.fn.AtomReader = function(options) {
    return this.each(
      function() {
        var feed = $.extend(true, {}, AtomReader);
        feed.setUp($(this), options);
      }
    );
  };

})(jQuery);
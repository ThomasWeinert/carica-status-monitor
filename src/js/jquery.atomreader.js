(function($){

  var AtomReaderEntry = {

    entries : null,
    node : null,

    id : null,
    updated : null,

    update : function(data) {
      var updated = new Date(data.updated);
      if (this.node) {
        if (updated > this.updated) {
          return;
        }
        this.hide();
      } else {
        this.create();
      }
      this.id = data.id;
      this.updated = updated;
      this.node.atomReaderEntry = this;
      this.node.find('h3').text(data.title);
      this.node.find('p').text(data.summary);
      this.node.find('.updated').text(updated.toLocaleString());
      this.show();
    },

    show : function() {
      this.entries.reader.node.find('li.status, li.title').last().after(this.node);
      this.node.slideDown();
    },

    hide : function() {
      if (this.node) {
        this.node.slideUp();
      }
    },

    remove : function() {
      this.hide();
      if (this.node) {
        this.node.remove();
        if (this.entries[this.id]) {
          delete(this.entries[this.id]);
        }
      }
    },

    create : function () {
      this.node = $(
        '<li class="entry">' +
          //'<img src="img/dummy.png" class="icon"/>' +
          '<h3>Title</h3>' +
          '<p>Summary</p>' +
          '<span class="updated"></span>' +
        '</li>'
      );
    }
  };

  var AtomReaderEntries = {

    reader : null,
    entries : {},

    get : function(id) {
      if (!this.entries[id]) {
        this.entries[id] = $.extend(
          true, {}, AtomReaderEntry
        );
        this.entries[id].entries = this;
      }
      return this.entries[id];
    },

    remove : function(id) {
      if (this.entries[id]) {
        var entry = this.entries[id];
        delete(this.entries[id]);
        entry.remove();
      }
      return false;
    }
  };

  var AtomReader = {

    node : null,
    entries : null,

    options : {
      url : '',
      mode : 'all',
      max : 5,
      interval : 0
    },

    namespaces : {
      'atom' : 'http://www.w3.org/2005/Atom',
      'media' : 'http://search.yahoo.com/mrss/',
      'carica' : 'http://www.a-basketful-of-papays.net/ns/status-monitor'
    },

    setUp : function(node, options) {
      this.node = node;
      this.entries = $.extend(true, {}, AtomReaderEntries);
      this.entries.reader = this;
      this.options = $.extend($.extend(this.options, options), node.data());
      this.fetch();
      if (this.options.interval > 0) {
        window.setInterval(
          $.proxy(this.fetch, this), 1000 * this.options.interval
        );
      }
    },

    fetch : function() {
      if (this.options.url != '') {
        this.node.find('.status').attr('class', 'status').addClass('loading').slideDown();
        $.get(this.options.url)
          .success($.proxy(this.ajaxSuccess, this))
          .error($.proxy(this.ajaxError, this));
      }
    },

    read : function(data) {
      var reader = this;
      $(data).xmlns(
        reader.namespaces,
        function () {
          var entries = this.find('atom|entry');
          for (var i = reader.options.max; i > 0; i--) {
            var entry = entries.eq(i - 1);
            if (entry.length > 0) {
              var data = new Object();
              data.id = entry.find('atom|id').text();
              data.updated = entry.find('atom|updated').text();
              data.title = entry.find('atom|title').text();
              data.summary = entry.find('atom|summary').text();
              reader.entries.get(data.id).update(data);
            }
          }
        }
      );
    },

    ajaxSuccess : function(data) {
      if (typeof data == 'string') {
        data = new DOMParser().parseFromString(data, 'text/xml');
      }
      this.read(data);
      this.node.find('.status').slideUp();
    },

    ajaxError : function(data) {
      this.node.find('.status').attr('class', 'status').addClass('error').text(
        data.status + ' ' + data.statusText
      );
    }
  };

  $.fn.AtomReader = function(options) {
    return this.each(
      function() {
        var feed = $.extend(true, {}, AtomReader);
        feed.setUp($(this), options);
      }
    );
  };

})(jQuery);
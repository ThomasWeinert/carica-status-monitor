(function($){

  var AtomReaderItem = {

    node : null,
    id : null,
    modified : null,
    title : null,
    text : null,

    show : function(reader, data) {
      console.log.data();
    }

  };

  var AtomReader = {

    node : null,

    options : {
      url : '',
      mode : 'all',
      max : 5,
      interval : 60
    },

    namespaces : {
      'atom' : 'http://www.w3.org/2005/Atom',
      'media' : 'http://search.yahoo.com/mrss/',
      'carica' : 'http://www.a-basketful-of-papays.net/ns/status-monitor'
    },

    setUp : function(node, options) {
      this.node = node;
      this.options = $.extend($.extend(this.options, options), node.data());
      this.fetch();
      window.setInterval($.proxy(this.fetch, this), 1000 * this.options.interval);
    },

    fetch : function() {
      if (this.options.url != '') {
        this.node.find('.status').attr('class', 'status').addClass('loading').slideDown();
        $.get(this.options.url)
          .success($.proxy(this.ajaxSuccess, this))
          .error($.proxy(this.ajaxError, this));
      }
    },

    update : function(data) {
      var reader = this;
      $(data).xmlns(
        reader.namespaces,
        function () {
          console.log(this.find('atom|entry').text());
        }
      );
    },

    ajaxSuccess : function(data) {
      if (typeof data == 'string') {
        data = new DOMParser().parseFromString(data, 'text/xml');
      }
      this.update(data);
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
(function($){

  var AtomReader = {

    node : null,

    options : {
      url : '',
      mode : 'all',
      max : 5,
      interval : 60
    },

    xmlns : {
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
      console.log(this.options);
      if (this.options.url != '') {
        this.node.find('.status').attr('class', 'status').addClass('loading').slideDown();
        $.get(this.options.url)
          .success($.proxy(this.ajaxSuccess, this))
          .error($.proxy(this.ajaxError, this));
      }
    },

    ajaxSuccess : function(data) {
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
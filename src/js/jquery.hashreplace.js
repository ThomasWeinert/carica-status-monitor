(function($){

  var HashReplace = {

    node : null,
    defaultText : '',
    
    options : {
      onUpdate : null
    },

    setUp : function(node, options) {
      this.node = node;
      this.defaultText = node.text();
      this.options = $.extend(this.options, options);
      this.replace();
      $(window).bind(
        'hashchange',
        $.proxy(this.update, this)
      );
    },
    
    replace : function () {
      var hash = window.location.hash ? window.location.hash.substring(1) : '';
      this.node.text(
        (hash != '') ? hash : this.defaultText
      );
    },

    update : function() {
      this.replace();
      if (this.options.onUpdate) {
        this.options.onUpdate();
      }
    }
  };

  $.fn.HashReplace = function(options) {
    return this.each(
      function() {
        var replace = $.extend(true, {}, HashReplace);
        replace.setUp($(this), options);
      }
    );
  };

})(jQuery);
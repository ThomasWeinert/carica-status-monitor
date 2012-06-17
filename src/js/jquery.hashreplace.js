(function($){

  var HashReplace = {

    node : null,
    defaultText : '',

    setUp : function(node) {
      this.node = node;
      this.defaultText = node.text();
      this.update();
      $(window).bind(
        'hashchange',
        $.proxy(this.update, this)
      );
    },

    update : function() {
      var hash = window.location.hash ? window.location.hash.substring(1) : '';
      this.node.text(
        (hash != '') ? hash : this.defaultText
      );
    }
  };

  $.fn.HashReplace = function() {
    return this.each(
      function() {
        var replace = $.extend(true, {}, HashReplace);
        replace.setUp($(this));
      }
    );
  };

})(jQuery);
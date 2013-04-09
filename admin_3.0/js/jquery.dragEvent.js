;(function($) {

	 var default_opts = {
      docEnter: empty,
      docOver: empty,
      docLeave: empty,
	  docDrop: empty
      }
      ,doc_leave_timer, stop_loop = false
	  
    
  $.fn.dragEvent = function(options) {
	  if ( ! this.is(document)) alert('should applay to document');
    var opts = $.extend({}, default_opts, options);
	$(this).on('drop', docDrop).on('dragenter', docEnter).on('dragover', docOver).on('dragleave', docLeave);

    function docDrop(e) {
      e.preventDefault();
      opts.docLeave.call(this, e);
      return false;
    }

    function docEnter(e) {
      clearTimeout(doc_leave_timer);
      e.preventDefault();
      opts.docEnter.call(this, e);
      return false;
    }

    function docOver(e) {
      clearTimeout(doc_leave_timer);
      e.preventDefault();
      opts.docOver.call(this, e);
      return false;
    }

    function docLeave(e) {
      doc_leave_timer = setTimeout((function(_this) {
        return function() {
          opts.docLeave.call(_this, e);
        };
      })(this), 200);
    }

  }
    function empty() {}
})(jQuery);
(function(ui){
	
	var doc_leave_timer = false;
	
	function dragEnter(e) {       clearTimeout(doc_leave_timer);      e.preventDefault();      opts.dragEnter.call(this, e);    }
    function dragOver(e) {      clearTimeout(doc_leave_timer);      e.preventDefault();      opts.docOver.call(this, e);      opts.dragOver.call(this, e);    }
    function dragLeave(e) {      clearTimeout(doc_leave_timer);      opts.dragLeave.call(this, e);      e.stopPropagation();    }
    function docDrop(e) {      e.preventDefault();      _docLeave.call(this, e);      return false;    }
    function docEnter(e) {      clearTimeout(doc_leave_timer);      e.preventDefault();      opts.docEnter.call(this, e);      return false;    }
    function docOver(e) {      clearTimeout(doc_leave_timer);      e.preventDefault();      opts.docOver.call(this, e);      return false;    }
	
	function _docLeave(_o,e){}
	
    function docLeave(e) {
      doc_leave_timer = setTimeout((function(_this) {
        return function() {
          _docLeave.call(_this, e);
        };
      })(this), 200);
    }
	
	function empty() {} ;
	 
	drop = function(_o,e){
		/*If image the upload and insert
		
		*/
		console.log("Hi");
	} ;
	
	dragStart = empty ;
    dragEnter = empty ;
    dragOver = empty ;
    dragLeave = empty ;
    docEnter = empty ;
    docOver = empty ;
    docLeave = empty ;
	
	ui.formLoad(function (){	
		var iframedoc = $('.rte-zone iframe').contents()
		
		iframedoc.on('drop', drop).on('dragstart',dragStart).on('dragenter', dragEnter).on('dragover', dragOver).on('dragleave', dragLeave)	
		
		iframedoc.keypress(function(e){
				//if (e.which == 98 && e.ctrlKey){e.preventDefault(); formatText('bold' ) ;	 return false ;	}
				//if (e.which == 105 && e.ctrlKey){e.preventDefault(); formatText('italic' ) ; return false ;	}
		}).bind('paste',function(_e){ 
			//console.log(_e.originalEvent , _e.originalEvent.clipboardData.files) 
			e = _e.originalEvent ;		   
		})
			
	})
	
	
		
}(UI))
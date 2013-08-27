/*

html5number - a JS implementation of <input type=number> for Firefox 16 and up

BASED ON :

html5slider - a JS implementation of <input type=range> for Firefox 16 and up
https://github.com/fryn/html5slider


*/
//var c = console.log ;
(function() {
	var MutationObserver    = window.MutationObserver || window.WebKitMutationObserver;

	var test = document.createElement('input');
	try { test.type = 'number';  if (test.type == 'number') return;} catch (e) {  return;	}

	var forEach = Array.prototype.forEach;
	var onChange = document.createEvent('HTMLEvents');
	
	onChange.initEvent('change', true, false);
	
	if (document.readyState == 'loading')
	  document.addEventListener('DOMContentLoaded', initialize, true);
	else
	  initialize();
	
	function initialize() {
	  // create initial sliders
	  forEach.call(document.querySelectorAll('input[type=number]'), transform);
	  // create sliders on-the-fly
	  new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
		  if (mutation.addedNodes)
			forEach.call(mutation.addedNodes, function(node) {
			  check(node);
			  if (node.childElementCount)
				forEach.call(node.querySelectorAll('input'), check);
			});
		});
	  }).observe(document, { childList: true, subtree: true });
	}
	
	
	function check(input) {
	  if (input.localName == 'input' && input.type != 'number' &&  input.getAttribute('type') == 'number')
		transform(input);
	}
	
	function transform(slider) {
	  	if (!slider.value){ 	slider.value = 0 ; 	}
		if (!slider.step){ 	slider.step = 1 ; 	}
		if (!slider.min){ 	slider.min = -10000000 ; 	}
		if (!slider.max){ 	slider.max = 10000000 ; 	}
		
		slider.addEventListener('keydown', function(e){
			//console.log(new_value,this.value,this.step)  ;
			if (e.keyCode > 36 && e.keyCode < 41) { // 37-40: left, up, right, down
				new_value =	parseFloat(this.value) + (e.keyCode == 38 || e.keyCode == 39 ? parseFloat(this.step) : -parseFloat(this.step));
				if (new_value < parseFloat(this.min) ){
					new_value = this.min ;
				}
				if ( new_value > parseFloat(this.max)){
					new_value = this.max ;
				}
				this.value = new_value;
			}  
		}, true);
	
	
	}
})();
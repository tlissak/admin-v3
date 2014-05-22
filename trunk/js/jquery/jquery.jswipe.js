(function ($) {
    $.fn.swipe = function (options) {
        var defaults = {
            threshold: {                x: 500,                y: 500           },
            swipeLeft: function () {
                log('swiped left')
            },
            swipeRight: function () {
                log('swiped right')
            }
        };
		function log(s){
			//$('#out')	.append(s + ' , ')
		}
        var options = $.extend(defaults, options);
        if (!this) return false;
        return this.each(function () {
            var me = $(this)
            var originalCoord = {          x: 0,                y: 0            }
            var finalCoord = {               x: 0,                y: 0            }

                function touchMove(event) {
                    event.preventDefault();
                    finalCoord.x = event.targetTouches[0].pageX
                    finalCoord.y = event.targetTouches[0].pageY
                }

                function touchEnd(event) {
					log('TouchEnd')
                    var changeY = originalCoord.y - finalCoord.y
					
                    if (changeY < defaults.threshold.y && changeY > (defaults.threshold.y * -1)) {
                        changeX = originalCoord.x - finalCoord.x
                        if (changeX > defaults.threshold.x) {
                            defaults.swipeLeft()
                        }
                        if (changeX < (defaults.threshold.x * -1)) {
                            defaults.swipeRight()
                        }
                    }
                }
	
                function touchStart(event) {
					log('TouchStart')
                    originalCoord.x = event.targetTouches[0].pageX
                    originalCoord.y = event.targetTouches[0].pageY
                    finalCoord.x = originalCoord.x
                    finalCoord.y = originalCoord.y
                }
	
                function touchCancel(event) {}
				this.addEventListener("touchstart", touchStart, false);
				this.addEventListener("touchmove", touchMove, false);
				this.addEventListener("touchend", touchEnd, false);
				this.addEventListener("touchcancel", touchCancel, false);
        });
    };
})(jQuery);

/*<a href="#" onClick="$(this).next().html('');return false;">Cleanup</a>
<div id="out"></div>
*/
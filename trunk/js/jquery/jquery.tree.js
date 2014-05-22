//LATERAL SUB-MENU
$(document).ready(function(e) {
//    return;
	if ($(".lateralNavigation").length) {
		$(".lateralNavigation li ul").hide();
		$(".lateralNavigation li ul li ul").hide();
		$(".lateralNavigation li ul").parent().addClass("plus");
		$(".lateralNavigation li ul").parent().hoverIntent({
			sensitivity: 5,
			interval: ($.browser.msie)  ?  75 : 150, //tempo em ms para o ie e restantes browsers
			over: function(){
				$(this).removeClass("plus").addClass("minus");
				$(this).find("ul:eq(0)").slideDown();
			},
			timeout: 300, 
			out: function(){
				$(this).removeClass("minus").addClass("plus");
				$(this).find("ul:eq(0)").slideUp();
			}
		});
		/*
		//alternative keyboard methods
		$(".lateralNavigation li ul").parent().find("a:first").focus(function(){
			$(this).parent().removeClass("plus").addClass("minus");
			$(this).parent().find("ul:eq(0)").slideDown();
		});
		$(".lateralNavigation li ul").find("a:last").blur(function(){
			$(this).parent().parent("ul").parent().removeClass("minus").addClass("plus");
			$(this).parent().parent("ul").slideUp();
		});
		*/
	};
});
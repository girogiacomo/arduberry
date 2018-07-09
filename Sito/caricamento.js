$( document ).ready(function() {
    //Once the DOM is ready
	//console.log( "DOM ready!" );
	
	$(window).load(function() {
		//Once the entire page (images or iframes) is ready
		//console.log( "Page ready!" );
		$('.flexslider').flexslider();
		$(".loader").fadeOut("slow");
		});
		
});
$(function(){
	if(gsp_layout_array.gsp_layout == 'vertical') {
		var l = $(".entry-title").offset().left - parseInt($(".entry-title").css("padding-left")) - parseInt($(".entry-title").css("border-left"));
		$('.gabfire_sharebar_vertical').css({left: l - 120, top : $(".entry-title").position().top});
	}
	
	console.log($(".entry-title").position().left + '-----' + $(".gabfire_sharebar_vertical").position().left);
	
	$(window).scroll(function() {
	
		/* console.log($(".entry-title").offset().top + '-----' + $(".gabfire_sharebar_vertical").offset().top); */
		
		//Vertical
		if (gsp_layout_array.gsp_layout == 'vertical') {
			var vertical_top = $(".entry-title").offset().top;
		    if($(this).scrollTop() >= vertical_top + 15){
		        $('.gabfire_sharebar_vertical').addClass("gsp_stick");
		    }else{
			    $('.gabfire_sharebar_vertical').removeClass("gsp_stick");
		    }
		}
		
		//Horizontal
		if (gsp_layout_array.gsp_layout == 'horizontal') {
			var horizontal_top = $(".entry-title").offset().top;
		    if($(this).scrollTop() >= horizontal_top + 15){
		        $('.gabfire_sharebar_horizontal').addClass("gsp_stick");
		    }else{
			    $('.gabfire_sharebar_horizontal').removeClass('gsp_stick');
		    }
		}
	});
});
jQuery(document).ready(function($) {
	
	if(wpsite_sharebar_layout_array.wpsite_sharebar_layout == 'vertical') {
		var l = $(".entry-title").offset().left - parseInt($(".entry-title").css("padding-left")) - parseInt($(".entry-title").css("border-left"));
		$('.wpsite_sharebar_vertical').css({left: l - 120, top : $(".entry-title").position().top});
	}
	
	//console.log($(".entry-title").position().left + '-----' + $(".wpsite_sharebar_vertical").position().left);
	console.log($('header-main').attr('height'));
	$(window).scroll(function() {
	
		/* console.log($(".entry-title").offset().top + '-----' + $(".wpsite_sharebar_vertical").offset().top); */
		
		//Vertical
		if (wpsite_sharebar_layout_array.wpsite_sharebar_layout == 'vertical') {
			var vertical_top = $(".entry-title").offset().top;
		    if($(this).scrollTop() >= vertical_top + 15){
		        $('.wpsite_sharebar_vertical').addClass("wpsite_sharebar_stick");
		    }else{
			    $('.wpsite_sharebar_vertical').removeClass("wpsite_sharebar_stick");
		    }
		}
		
		//Horizontal
		if (wpsite_sharebar_layout_array.wpsite_sharebar_layout == 'horizontal') {
			var horizontal_top = $(".entry-title").offset().top;
		    if($(this).scrollTop() >= horizontal_top + 15){
		        $('.wpsite_sharebar_horizontal').addClass("wpsite_sharebar_stick");
		    }else{
			    $('.wpsite_sharebar_horizontal').removeClass('wpsite_sharebar_stick');
		    }
		}
	});
});
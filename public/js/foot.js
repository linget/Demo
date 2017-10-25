/**走马灯与广告 滑动**/
	function foot_auto(){
		footer_css();
		var num = 1,imgWidth = $(".custom ul li").eq(0).width();;//$(".custom ul li").eq(0).width();
		if($(".custom ul li").length>1)
		{
		$(".custom").find("ul").animate({
			marginLeft:-imgWidth
			},30000,function(){
				$(this).css("margin-left","0px").find("li:first").appendTo($(this)); 
			})
		}
	}

	function advert(){
		var num = 0,ad = $(".ad ul li");
		setInterval(function(){
			if(num>ad.length-1)
			{
				num = 0;
			}else{
				num++;
			}
			ad.eq(num).fadeIn(3000).siblings().fadeOut(3000);
		},8000);
	}

function footer_css(){
		var winHeight = screen.width;//走马灯动态宽度设置
		var math = $(".footer div.custom ul li").length;
		$(".footer div.custom ul").css('width',math*winHeight+'px');
		$(".footer div.custom ul li").css('width',winHeight+'px');
}
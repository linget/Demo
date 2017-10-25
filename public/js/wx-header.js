/* 微信头像-动态显示 */
var weixins = {
	loadhtml:function(data){
		var html = '<ul>';
		$.each(data,function(k,v){
			html +='<li class="'+v['class']+'">';
			html +='<img src="'+v['headimgurl']+'" />';
			html +='<input type="hidden" id="r'+k+'" value="'+v['number']+'" />';
			html +='<canvas id="c'+k+'"></canvas></li>';
		})
		html += '</ul>';
		return html;
	},
	wxsuccess:function(res){
		if(res == null||res == 'null'||res == undefined){return;}
		var info = JSON.parse(res);
		var html = weixins.loadhtml(info);
		document.getElementById('showwx').innerHTML = html;
	},
	Monitoring:function(url){
		$.ajax({
			data:{data:1},
			dataType:'json',
			url:url,
			type:'post',
			async:true,
			timeout:5000,
			success:function(res){
				weixins.wxsuccess(res);
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				console.log(XMLHttpRequest);console.log(textStatus);console.log(errorThrown);
			}
		});
	}
}

var monitor = function(){
    	var url = 'http://localhost/Demo/public/index.php/home/airs/monitor';
    	weixins.Monitoring(url);
}

//全向发布-走马灯语音-语音
var releasemsg = function(){
	$.ajax({
		url:'http://localhost/Demo/public/index.php/wap/release/findrelease',
		data:{releasemsg:1},
		dataType:'json',
		type:'POST',
		success:function(res){
			var result = JSON.parse(res);
			var cah = result['cache'];
			var voice = result['voiceurl'];
			var type = result['result'];
			if(type == 'ok'){
				var msg = "<li><p>"+decodeURI(cah)+"<audio src='"+voice+"' autoplay='autoplay'></audio></p></li>";
				$(".run1").children("li:first-child").prepend(msg);
			}else{
				return false;
			}
		}
	});
};
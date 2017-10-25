//页面时间
    function nowtime(){
    /*
     * ev:显示时间的元素
     * type:时间显示模式.若传入12则为12小时制,不传入则为24小时制
     */
    //年月日时分秒
    var H,I;
    //月日时分秒为单位时前面补零
    function fillZero(v){
        if(v<10){v='0'+v;}
        return v;
    }
    (function(){
        var d=new Date();
        H=fillZero(d.getHours());
        I=fillZero(d.getMinutes());

        document.getElementById('time').innerHTML=H+':'+I;
        //每秒更新时间
        setTimeout(arguments.callee,60000);
    })();
}

/* 翻页----判断出发到达，语言状态 */
function changes_type()
{
	var change_lanugages,change_from;
		/* 到发判断 */
		switch(c_isFromSha)
		{
			//到达
			case 0:
				if(isFromSha == 1)
				{
					isFromSha = '0';
					document.cookie="isFromSha"+name+"="+isFromSha; /*window.location.reload();*/
				}
				isFromSha = '0';
				document.cookie="isFromSha"+name+"="+isFromSha;
				change_from = 'isFromSha_end';

			break;

			//出发
			case 1:
				
				if(isFromSha != 1)
				{
					isFromSha = 1;
					document.cookie="isFromSha="+name+""+isFromSha; /*window.location.reload();*/
				}
				isFromSha = '1';
				document.cookie="isFromSha="+name+""+isFromSha;
				change_from = 'isFromSha_end';
			break;

			case 2:
				
				if(isFromSha == '1')
				{
					isFromSha = '0';
				}else{
					isFromSha = '1';
					change_from = 'isFromSha_end';//已运行完
				}

				document.cookie="isFromSha"+name+"="+isFromSha; /*window.location.reload();*/
			break;
		}

			/* 语言判断 */
		switch(c_language_type)
			{
				//中
				case 1:
					if(language != 'zh_cn')
					{
						language = 'zh_cn';
						document.cookie="language"+name+"="+language;
						 /*window.location.reload();*/
					}
					language = 'zh_cn';
					document.cookie="language"+name+"="+language;
					change_lanugages = "language_end";//已运行完一遍
				break;
				//英
				case 2:
					if(language != 'en')
					{
						language = 'en';
						document.cookie="language="+language; /**/
					}
					language = 'en';
					document.cookie="language"+name+"="+language;
					change_lanugages = "language_end";//已运行完一遍
				break;

				case 3:
					if( c_isFromSha == "2" && change_from != "isFromSha_end")
					{ 
						break;
					}
					if(language == 'zh_cn')
					{
						language = 'en';
						
					}else{
						language = 'zh_cn';
						
					}
					change_lanugages = "language_end";//已切换完语言
					document.cookie="language"+name+"="+language; //window.location.reload();
				break;
			}
			// console.log("c_isFromSha:"+c_isFromSha+"c_language_type:"+c_language_type);console.log("change_from:"+change_from+"change_lanugages:"+change_lanugages);
		//整个页面运行完成（出发，到达，中英文）跳转标记
		if (change_lanugages == "language_end" || change_from == 'isFromSha_end') 
		{
			console.log("language_end"+change_lanugages);
			//var html = '<p><img '+'src="/public/images/loading.gif" style="height:120px;margin:25% auto;" />正在加载......，请稍后</p>';
			//document.getElementsByTagName("body")[0].innerHTML=html;
			//var inputs = window.parent.document.getElementById('jumptype');
			//if(inputs){ 
				//inputs.value = 'yes';
			jump();

			//}
			return false;			
		};

}
//延迟函数
function sleep(d){
  for(var t = Date.now();Date.now() - t <= d;);
}

//出发到达头部动态设置
	function change_header(isFromSha,language,type)
	{
		var html = '';
		switch(language){
			case 'zh_cn':
			switch(type){
			case 'trains':
					if (isFromSha == 1) 
					{
						html += "<p><h3>虹桥火车站始发车次</h3><a>HongQiao Train Station Starting Trip</a></p>";
					}else{
						html += "<p><h3>虹桥火车站到达车次</h3><a>Hongqiao Train Station Arrival Trip</a></p>";
					}
			break;
			case 'airs':
					if (isFromSha == 1) 
					{
						html += "<p><h3>虹桥机场离港航班</h3><a>HongQiao Airport Departure Flight</a></p>";
					}else{
						html += "<p><h3>虹桥机场到港航班</h3><a>HongQiao Airport Arrival Flight</a></p>";
					}
			break;
		}
			break;
			case 'en':
			switch(type){
			case 'trains':
					if (isFromSha == 1) 
					{
						html += "<p><h3 class='hen'>HongQiao Train Station Starting Trip</h3><a class='aen'>虹桥火车站始发车次</a></p>";
					}else{
						html += "<p><h3 class='hen'>Hongqiao Train Station Arrival Trip</h3><a class='aen'>虹桥火车站到达车次</a></p>";
					}
			break;
			case 'airs':
					if (isFromSha == 1) 
					{
						html += "<p><h3 class='hen'>HongQiao Airport Departure Flight</h3><a class='aen'>虹桥机场离港航班</a></p>";
					}else{
						html += "<p><h3 class='hen'>HongQiao Airport Arrival Flight</h3><a class='aen'>虹桥机场到港航班</a></p>";
					}
			break;
		}
			break;
		}
		
		$(".center").html(html);
	}

//读取cookie
function getCookie(name)
{
	 var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
            if (arr != null) return unescape(arr[2]); return null;
            
	/*var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg))
	return unescape(arr[2]);
	else
	return null;*/
}

//实行跳转
function jump(){
		var len,iframe,this_iframe,then;
		iframe = window.parent.document.getElementById('frame');
		if (!iframe) { return};
		var tmp = parent.url;
		var url=[];
		for(var key in tmp){
		   //key是属性,object[key]是值
		   url.push(key);//往数组中放属性
		}
		len = url.length;
		this_iframe = iframe.src;

		for (var i = 1; i <= len;)
		{
			// console.log("this_iframe--"+this_iframe+"tmp[i]--"+tmp[i]);
			if(this_iframe.match(tmp[i]))
			{
				i=i+1;
				if(i>len)
				{
					i=1;
				}
				// console.log("jump："+tmp[i]);
				//then = tmp[i];
				//if(tmp[i] == 'images')
				//{
				//	window.parent.document.getElementById("main").innerHTML = '<iframe src="/Demo/public/index.php/home/others/index" id="frame" name="view_frame" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no"  width="100%;" height="100%"  ></iframe>'; 
					//(function(i){setTimeout(testj(),5000)})(i);
				//}else
				//{
					window.parent.document.getElementById("main").innerHTML = '<iframe  src="'+tmp[i]+'" id="frame" name="view_frame" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no"  width="100%;" height="100%" ></iframe>';
				//}
				break;
			}else
			{
				i++;
			}
			
		}
	
	}
/*
	function testj()
	{
		window.parent.document.getElementById("main").innerHTML = '<iframe  src="/Demo/public/index.php/home/airs/index"" id="frame" name="view_frame" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no"  width="100%;" height="100%" ></iframe>';
	}*/
var current_Page = 1;
var  data_error=function()
	{

		console.log("airlist die;")
		//window.location.href = '';
	}

var data_success = function(data){
	//判断是否需要跳转

	var stm,info,result,total,countPage;
	html = '';
	info = JSON.parse(data);
	result = info['info'];
	total = info['pages'];
	countPage = total['count_Page'];

	if(result ==''||result ==null){ return;}
	for (var i = 0; i < result.length; i++) {
		html +='<li class="variable"><label class="main"><div class="lunbo'+i+'"><ul  class="scroll">';

		$.each(result[i]['flight_info'],function(key,value){

			html +='<li><p id="airlogo"><img src="/Demo/public'+result[i]["flight_info"][0]["flight_logo"]+'" />&nbsp;';
			html +=result[i]["flight_info"][0]["flight_no"]+'</p></li>';
		}) 
		html +='</ul></div></label>';

		if (isFromSha == 1) 
		{
			stm = result[i]['E_STARTTIME'];

			html +='<label><div';
			if(result[i]['TOCITY'].length>16)
			{
				html +=' class="toolen" ';
			}
			html +='>'+result[i]['TOCITY']+'</div></label>';
			html +='<label><div>'+result[i]['STARTTIME']+'</div></label>';
		}else
		{

			stm = result[i]['E_ENDTIME'];
			html +='<label><div';
			if(result[i]['FROMCITY'].length>16)
			{
				html +=' class="toolen" ';
			}
			html +='>'+result[i]['FROMCITY']+'</div></label>';
			html +='<label><div>'+result[i]['ENDTIME']+'</div></label>';
		}

		if(stm == undefined||stm == result[i]['ENDTIME']||stm == result[i]['STARTTIME'])
		{
			stm = '';
		}

		if (result[i]['REMARK_XML'] == '正常'||result[i]['REMARK_XML'] == 'Normal') {
			html +='<label class="state'+i+' greens"><div>'+result[i]['REMARK_XML']+'</div></label></li>';

		}else if(result[i]['REMARK_XML'].match('延误')||result[i]['REMARK_XML'].match('Delayed'))
		{
			html +='<label class="state'+i+' yellows"><div>'+result[i]['REMARK_XML']+stm+'</div></label></li>';
		}else{
			html +='<label class="state'+i+' reds"><div>'+result[i]['REMARK_XML']+'</div></label></li>';
		}

		

	};

	$('div.content ul').html(html);
	/* 航班轮播 */
	//autorun();
	/* 状态颜色 */
	auto_head(isFromSha,language,"airs");

	change_header(isFromSha,language,"airs");
}
//列车
var train_success= function(data)
{
	var html,result,info,total,countPage;
	result = JSON.parse(data);
	info = result['info'];
	total= result['pages'];
	countPage = total['count_Page'];
	isFromSha = total['isFromSha'];

	html = '<ul class="test">';
	for (var i = 0; i < info.length; i++) 
	{
		html+= "<li class='variable'><label><div>"+info[i]['FLIGHT_NO']+"</div></label>";
		if (isFromSha == 1) {
			html+= "<label";
			if(info[i]['TOCITY'].length>=11)
			{
				html +=' class="toolen" ';
			}
			html+= ">"+info[i]['TOCITY']+"</label>";
			html+= "<label><div>"+info[i]['STARTTIME']+"</div></label>";
		}else{
			html+= "<label";
			if(info[i]['FROMCITY'].length>=11)
			{
				html +=' class="toolen" ';
			}
			html+= "><div>"+info[i]['FROMCITY']+"</div></label>";
			html+= "<label><div>"+info[i]['ENDTIME']+"</div></label>";
		}


		if(info[i]['REMARK'].match('晚点')|| info[i]['REMARK'].match('late'))
		{
			html +="<label class='state"+i+" yellows'><div>"+info[i]['REMARK'];
		}else{
			html+= "<label class='state"+i+" greens'><div>"+info[i]['REMARK'];
		}
		html +="</div></label></li>";
	}
		html+= '</ul>';
	$('div.content').html(html);
	/* 状态颜色 */

	auto_head(isFromSha,language,"trains");

	change_header(isFromSha,language,"trains");
}

/* 翻页 */
function autoLoad(){
	if (current_Page>=countPage||current_Page==null) 
	{
			current_Page = 0;
			if(language == undefined||language==null){  language == 'zh_cn';}
			var res = changes_type();//翻页语言等状态判断
			if(res == false){ return;}

	}
	
	if(MoreUrl != undefined && MoreUrl != null)
	{
		//列车翻页
		console.log(isFromSha);
		//ajaxs(ajaxParam(MoreUrl, {"current_Page":current_Page,"language":language,"isFromSha":isFromSha},train_success,data_error,true));
		ajax.post(MoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},train_success,data_error,true);
	}else{
		//航班翻页
		//ajaxs(ajaxParam(loadMoreUrl, {"current_Page":current_Page,"language":language,"isFromSha":isFromSha},data_success,data_error,true));
		ajax.post(loadMoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},data_success,data_error,true);
	}
	//ajaxs(ajaxParam(loadMoreUrl,{"current_Page":++current_Page,"language":language},data_success,data_error));
}

//态势页面头部
function auto_head(isFromSha,language,type)
{
		var html = '';

		switch(type){
			case 'airs':
					if (isFromSha == 1) 
					{
						if (language == 'zh_cn') {
							
							html = '<li><h3 class="first">航班</h3><h3>目的地</h3><h3>出发时间</h3><h3>状态</h3></li><li><h3 class="first">航班</h3><h3>目的地</h3><h3>出发时间</h3><h3>状态</h3></li><li><h3 class="first">航班</h3><h3>目的地</h3><h3>出发时间</h3><h3>状态</h3></li>';
						}else{
							html = '<li><h3 class="first">FLIGHT</h3><h3>TO</h3><h3>SCHED</h3><h3>STATUS</h3></li><li><h3 class="first">FLIGHT</h3><h3>TO</h3><h3>SCHED</h3><h3>STATUS</h3></li><li><h3 class="first">FLIGHT</h3><h3>TO</h3><h3>SCHED</h3><h3>STATUS</h3></li>';   
						};

					}else
					{
						if (language == 'zh_cn') {
							html = '<li><h3 class="first">航班</h3><h3>始发地</h3><h3>到达时间</h3><h3>状态</h3></li><li><h3 class="first">航班</h3><h3>始发地</h3><h3>到达时间</h3><h3>状态</h3></li><li><h3 class="first">航班</h3><h3>始发地</h3><h3>到达时间</h3><h3>状态</h3></li>';
						}else{
							html = '<li><h3 class="first">FLIGHT</h3><h3>FROM</h3><h3>ACTUAL</h3><h3>STATUS</h3></li><li><h3 class="first">FLIGHT</h3><h3>FROM</h3><h3>ACTUAL</h3><h3>STATUS</h3></li><li><h3 class="first">FLIGHT</h3><h3>FROM</h3><h3>ACTUAL</h3><h3>STATUS</h3></li>';   
						};
					}
			break;
			case 'trains':
					if (isFromSha == 1) 
					{
						if (language == 'zh_cn') {
							
							html = '<li><h3 class="first">车次</h3><h3>目的地</h3><h3>出发时间</h3><h3>状态</h3></li><li><h3 class="first">车次</h3><h3>目的地</h3><h3>出发时间</h3><h3>状态</h3></li><li><h3 class="first">车次</h3><h3>目的地</h3><h3>出发时间</h3><h3>状态</h3></li>';
						}else{
							html = '<li><h3 class="first">TRAIN</h3><h3>TO</h3><h3>SCHED</h3><h3>STATUS</h3></li><li><h3 class="first">TRAIN</h3><h3>TO</h3><h3>SCHED</h3><h3>STATUS</h3></li><li><h3 class="first">TRAIN</h3><h3>TO</h3><h3>SCHED</h3><h3>STATUS</h3></li>';   
						};

					}else
					{
						if (language == 'zh_cn') {
							
							html = '<li><h3 class="first">车次</h3><h3>始发地</h3><h3>到达时间</h3><h3>状态</h3></li><li><h3 class="first">车次</h3><h3>始发地</h3><h3>到达时间</h3><h3>状态</h3></li></li><li><h3 class="first">车次</h3><h3>始发地</h3><h3>到达时间</h3><h3>状态</h3></li>';
						}else{
							html = '<li><h3 class="first">TRAIN</h3><h3>FROM</h3><h3>ACTUAL</h3><h3>STATUS</h3></li><li><h3 class="first">TRAIN</h3><h3>FROM</h3><h3>ACTUAL</h3><h3>STATUS</h3></li><li><h3 class="first">TRAIN</h3><h3>FROM</h3><h3>ACTUAL</h3><h3>STATUS</h3></li>';   
						};
					}
			break;
		}
	$('.top ul').html(html);

}

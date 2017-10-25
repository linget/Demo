var current_Page = 1;
var  data_error=function(XMLHttpRequest, textStatus, errorThrown){
		
		//console.log(jqXHR);console.log(textStatus);console.log(errorThrown);
		//parent.location.reload();
		console.log("系统出现错误，请稍后再试！");console.log("getmore die;")
		//window.location.href = '';
	}

//航班
var data_success = function(data){
	//判断是否需要跳转
	//var is_end = change_url();

	//if (is_end == 1) { /*window.location.reload();*/return;/*终断程序运行*/};
	//if (is_end === 0) { var language = getCookie('language_index');all_city(language);/*切换坐标中英*/};
	
	var language = getCookie('language_index');
	var html = '';
	var info = JSON.parse(data);

	var result = info['info'];
	var total = info['pages'];
	var arr = info['arr'];
	var tm = '';
	countPage = total['count_Page'];
	if(!result){ return;}

	for (var i = 0; i < result.length; i++) 
	{
		html +='<tr><td><div class="lunbo'+i+'"><ul class="scroll">';
		//console.log(result[i]);
		$.each(result[i]['flight_info'],function(key,value)
		{
			html+='<li><p><img src="/Demo/public'+value.flight_logo+'" />&nbsp;';
			html+=value.flight_no+'</p></li>';
		});
		html +='</ul></div></td>';
		if(total.isFromSha == 1)
		{
			html +='<td>'+result[i]['STARTTIME']+'</td><td';
			if(result[i]['TOCITY'].length>=12)
			{
				html +=' class="toolen" ';
			}
			html +='>'+result[i]['TOCITY']+'</td>';
		}else{
			html +='<td>'+result[i]['ENDTIME']+'</td><td';
			if(result[i]['FROMCITY'].length>=12)
			{
			 html +=' class="toolen" ';
			}
			html +='>'+result[i]['FROMCITY']+'</td>';
		}

		tm = result[i]['E_STARTTIME'];//延误时间

		if(!tm||tm == undefined){ tm = '';}
		
		if(result[i]['REMARK_XML'].match('延误')||result[i]['REMARK_XML'].match('Delayed'))
		{
			html +='<td class="state'+i+'  yellows" >'+result[i]['REMARK_XML']+tm+'</td></tr>';

		}else if (result[i]['REMARK_XML'] == '正常'||result[i]['REMARK_XML'] == 'Normal') {

			html +='<td class="state'+i+'  greens">'+result[i]['REMARK_XML']+'</td></tr>';

		}else{

			html +='<td class="state'+i+'  reds">'+result[i]['REMARK_XML']+'</td></tr>';
		}
		

	};

	$('tbody.main').html(html);
		/* 态势图数据 ||total['current_Page'] == countPage*/
	if (total['current_Page'] == ''||total['current_Page'] == 1) { all_city(language);getEcharts(arr,null,null);};
			/* 颜色加载 */
	//autocolor();
		/* 航班上下滚动 */
	autorun();
	// 动态切花出发到达
	auto_head(isFromSha,language,"airs");
	change_header(isFromSha,language,"airs");

}

//列车
var train_success = function(data)
{
	//判断是否需要跳转
	//var is_end = change_url();
	//if (is_end == 1) { /*window.location.reload();*/return;/*终断程序运行*/};
	//if (is_end == 0) { train_city(language);/*切换坐标中英*/};
	 var language = getCookie('language_trainindex');
	var html,result,info,arr,total;
	result = JSON.parse(data);
	info = result['info'];
	arr = result['arr'];
	total = result['pages'];
	countPage = total['count_Page'];

	if(!info){ return;}
	// console.log(arr);
	html = '';
	for (var i = 0; i < info.length; i++) 
	{
		if(info[i]['TOCITY'] == undefined||info[i]['FROMCITY'] == undefined){continue;}
		html +='<tr><td class="train-first">'+info[i]['FLIGHT_NO']+'</td>';
		
		if (total.isFromSha == 1) {
			html +='<td>'+info[i]['STARTTIME']+'</td>';

			html +='<td ';
			if(info[i]['TOCITY'].length>13)
			{
				html +='class="toolen" ';
			}
			html +='>'+info[i]['TOCITY']+'</td>';
		}else{
			html +='<td>'+info[i]['ENDTIME']+'</td>';
			html +='<td ';
			
			if(info[i]['FROMCITY'].length>13)
			{
				html +='class="toolen" ';
			}
			html +='>'+info[i]['FROMCITY']+'</td>';
		}

		if(info[i]['REMARK'].match('晚点')|| info[i]['REMARK'].match('late'))
		{
			html +='<td class="state'+i+' yellows">';
		}else{
			html +='<td class="state'+i+' greens">';
		}
					
		html += info[i]['REMARK']+'</td></tr>';
	};
		$('tbody.main').html(html);
	
	/* 态势图数据 */
	if (total['current_Page'] == undefined||total['current_Page'] == 1) { train_city(language);getEcharts(arr);}
		
	/* 颜色加载 */
	auto_head(isFromSha,language,"trains");
	change_header(isFromSha,language,"trains");
}

/* 翻页 */
function autoLoad()
{

	if (current_Page>=countPage||current_Page==null) 
	{
		current_Page = 0;
		if(language == undefined||language==null){  language == 'zh_cn';}
		var res = changes_type();//翻页语言等状态判断
		if (res ==false) { return};
	};

	if(MoreUrl != undefined && MoreUrl != null)
	{
		//列车翻页
		ajax.post(MoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},train_success,data_error,true);
	}else{
		//航班翻页
		ajax.post(loadMoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},data_success,data_error,true);
	}
}

//态势页面头部
function auto_head(isFromSha,language,type)
{
		var title,html = '',head = '';
		if (language == 'zh_cn') { 
			title = '康得思酒店';
		}else{
			title = 'Cordis';
		}
		switch(type){
			case 'airs':
					if (isFromSha == 1) 
					{
						if (language == 'zh_cn') {
							
							head = '<tr><th class="first">航班</th><th>出发时间</th><th>目的地</th><th>状态</th></tr>';
						}else{
							head = '<tr><th class="first">FLIGHT</th><th>SCHED</th><th>TO</th><th>STATUS</th></tr>';   
						};

					}else{
						if (language == 'zh_cn') {
							
							head = '<tr><th class="first">航班</th><th>到达时间</th><th>始发地</th><th>状态</th></tr>';
						}else{
							head = '<tr><th class="first">FLIGHT</th><th>ACTUAL</th><th>FROM</th><th>STATUS</th></tr>';   
						};
					}
			break;
			case 'trains':
					if (isFromSha == 1) 
					{
						if (language == 'zh_cn') {
							
							head = '<tr><th class="trainno">车次</th><th>出发时间</th><th>目的地</th><th>状态</th></tr>';
						}else{
							head = '<tr><th class="trainno">TRAIN</th><th>SCHED</th><th>TO</th><th>STATUS</th></tr>';   
						};

					}else{
						if (language == 'zh_cn') {
							
							head = '<tr><th class="trainno">车次</th><th>到达时间</th><th>始发地</th><th>状态</th></tr>';
						}else{
							head = '<tr><th class="trainno">TRAIN</th><th>ACTUAL</th><th>FROM</th><th>STATUS</th></tr>';   
						};
					}
			break;
		}
	$('.theader').html(head);
	$('.logo h3').html(title);
}


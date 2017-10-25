var current_Page = 1;
var  data_error=function(XMLHttpRequest, textStatus, errorThrown){
		console.log(XMLHttpRequest);console.log(textStatus);console.log(errorThrown);
		//console.log(jqXHR);console.log(textStatus);console.log(errorThrown);
		//parent.location.reload();
		console.log("系统出现错误，请稍后再试！");console.log("getmore die;")
		//window.location.href = '';
	}

//航班
var data_success = function(data){
	//判断是否需要跳转
	var is_end = change_url();

	if (is_end == 1) { /*window.location.reload();*/return;/*终断程序运行*/};
	if (is_end == 0) { all_city(language);/*切换坐标中英*/};
	var html = '';var htm ='';
	var info = JSON.parse(data);

	var result = info['info'];
	var total = info['pages'];
	var arr = info['arr'];
	var tm = '';
	countPage = total['count_Page'];

		/* 态势图数据 */
	
	getEcharts(arr);

	// 动态切花出发到达
	auto_head(isFromSha,language,"airs");
	change_header(isFromSha,language,"airs");

}

//列车
var train_success = function(data)
{
	//判断是否需要跳转
	var is_end = change_url();
	if (is_end == 1) { /*window.location.reload();*/return;/*终断程序运行*/};
	if (is_end == 0) { train_city(language);/*切换坐标中英*/};
	var html,result,info,arr,total;
	result = JSON.parse(data);
	info = result['info'];
	arr = result['arr'];
	total = result['pages'];
	countPage = total['count_Page'];

	
	/* 态势图数据 */
	getEcharts(arr);


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
		changes_type();//翻页语言等状态判断
	};

	if(MoreUrl != undefined && MoreUrl != null)
	{
		//列车翻页
		//ajaxs(ajaxParam(MoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},train_success,data_error,true));
		ajax.post(MoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},train_success,data_error,true);
	}else{
		//航班翻页
		//ajaxs(ajaxParam(loadMoreUrl, {"current_Page":++current_Page,"language":language,"isFromSha":isFromSha},data_success,data_error,true));
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
	$('.logo h3').html(title);
}


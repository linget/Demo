var round_error=function()
	{
		console.log("more-round die;")
		//window.location.href = '';
	}

//航班走马灯
var round_success = function(data){
	
	var html = '',city;
	var info = JSON.parse(data);
	//console.log(info);
	if(!info)
	{
		return;
	}else
	{
		var timestamp=new Date().getTime();
		var result = info['result'];
		var total = info['total'];
		html ='<div class="now'+timestamp+'">';
		if (total.isFromSha == 1) {
			
			city = "FROMCITY";
		}else{
			city = "TOCITY";
		}
		if(total.language=='zh_cn')
		{
			$.each(result,function(key,value){

				if(value['REMARK_XML'] != '正常'||value['REMARK_XML'] != 'Normal')
				{
					html+='<li>前往'+value[city]+'的'+value['FLIGHT_NO']+'次航班由于航空公司计划的原因'+value['REMARK_XML']+',请广大旅客及时了解留意航班变动情况,以免影响正常出行。</li>';
				}else{
					html+='<li>前往'+value[city]+'的'+value['FLIGHT_NO']+'次航班将于'+value['STARTTIME']+'正常'+systemConfig[languages]['runtype'][isFromSha]+',请广大旅客及时了解留意航班变动情况,以免影响正常出行。</li>';
				}
			})
		}else{
			$.each(result,function(key,value){
				if(value['REMARK_XML'] != '正常'||value['REMARK_XML'] != 'Normal')
				{
					html+='<li>'+value['FLIGHT_NO']+' flight to '+value[city]+' due to '+value['REMARK_XML']+' due to company plan,Please timely attention to the vast number of passengers flying changes,So as not to affect the normal travel.</li>';
				}else{
					html+='<li>'+value['FLIGHT_NO']+' flight to '+value[city]+'will take off on'+value['STARTTIME']+','+systemConfig[languages]['runtype'][isFromSha]+'Please timely attention to the vast number of passengers flying changes,So as not to affect the normal travel.</li>';
				}

			})
		}
		html +='</div>';
	}
	$('div.custom ul').append(html);
	$('div.custom ul div.now'+timestamp).siblings('li').remove();

	// 清除div
	var mydate = new Date(); 
	var hour = mydate.getHours();
	if (hour == 0) {
		$('div.custom ul div.now'+timestamp).siblings().remove();

	};
	/*  走马灯轮播 */
	foot_auto();
}

//列车走马灯
var round_train = function(data)
{
	var info = JSON.parse(data);
	var timestamp = new Date().getTime();
	if(!info)
	{
		return;
	}else
	{

		var result = info['result'];
		var total = info['total'];
		var isFromSha = total['isFromSha'];
		html ='<div class="now'+timestamp+'">';
		if (isFromSha == 1) {
			
			city = "FROMCITY";
		}else{
			city = "TOCITY";
		}
		if(total.language=='zh_cn')
		{
			$.each(result,function(key,value){
				if(value['REMARK'] != '正点'||value['REMARK'] != 'on time')
				{
					html+='<li>前往'+value[city]+'的'+value['FLIGHT_NO']+'次航班由于航空公司计划的原因'+value['REMARK']+',请广大旅客及时了解留意航班变动情况,以免影响正常出行。</li>';
				}else{
					html+='<li>前往'+value[city]+'的'+value['FLIGHT_NO']+'次航班将于'+value['STARTTIME']+'正常'+systemConfig[languages]['runtype'][isFromSha]+',请广大旅客及时了解留意航班变动情况,以免影响正常出行。</li>';
				}
			})
		}else{
			$.each(result,function(key,value){
				if(value['REMARK'] != '正点'||value['REMARK'] != 'on time')
				{
					html+='<li>'+value['FLIGHT_NO']+' flight to '+value[city]+' due to '+value['REMARK']+' due to company plan,Please timely attention to the vast number of passengers flying changes,So as not to affect the normal travel.</li>';
				}else{
					html+='<li>'+value['FLIGHT_NO']+' flight to '+value[city]+'will take off on'+value['STARTTIME']+','+systemConfig[languages]['runtype'][isFromSha]+'Please timely attention to the vast number of passengers flying changes,So as not to affect the normal travel.</li>';
				}

			})
		}
		html +='</div>';
	}
	$('div.custom ul').append(html);
	$('div.custom ul div.now'+timestamp).siblings('li').remove();

	// 清除div
	var mydate = new Date(); 
	var hour = mydate.getHours();
	if (hour == 0) 
	{
		$('div.custom ul div.now'+timestamp).siblings().remove();

	};
	/*  走马灯轮播 */
	foot_auto();
}

/* 走马灯数据翻页 */
function autoround()
{
	
	if(round_Url != undefined && round_Url != null)
	{
		//列车走马灯
		//ajaxs(ajaxParam(round_Url, {"language":language,"isFromSha":isFromSha},round_train,round_error,true));
		ajax.post(round_Url, {"language":language,"isFromSha":isFromSha},round_train,round_error,true);
	}else{
		//航班走马灯
		ajax.post(roundUrl, {"language":language,"isFromSha":isFromSha},round_success,round_error,true);
		//ajaxs(ajaxParam(roundUrl, {"language":language,"isFromSha":isFromSha},round_success,round_error,true));
	}

	//ajaxs(ajaxParam(roundUrl,{"current_Page":++round_page,"language":language},round_success,round_error));
}
/**
 * 页面加载/数据存储/请求发送
 * @author zjl 2017-10-21
 */
var  send_error=function(XMLHttpRequest, textStatus, errorThrown){
		//console.log(XMLHttpRequest.status);console.log(XMLHttpRequest.readyState);
		console.log(textStatus);console.log(errorThrown);
	}

var loadhtml = {
	header1:function(data,isFromSha){
		var html = '',auth = $('#echarts-pie');
		var hgroup = document.getElementsByTagName('hgroup');

		if (!data||data==undefined) 
		{
			hgroup['0'].classList.add("current");
			hgroup['1'].classList.remove("current");
			return;
		};
		var datas = JSON.parse(data);



		$.each(datas['info'],function(k,v){
			html += "<dl><dt>"+v['name']+"</dt><dd>"+v['value']+"</dd></dl>";
		})

		//更新统计图表及数据
		getEcharts_pie(datas['json']);

		auth.siblings('div').html(html);
		
		hgroup['0'].classList.add("current");
		hgroup['1'].classList.remove("current");

	},
	header2:function(data,isFromSha){
		var html = '',auth = $('#echarts-rose');
		var hgroup = document.getElementsByTagName('hgroup');

		if (!data||data==undefined) 
		{
			var hgroup = document.getElementsByTagName('hgroup');
			hgroup['1'].classList.add("current");
			hgroup['0'].classList.remove("current");
			return;
		};
		var datas = JSON.parse(data);



		$.each(datas['info'],function(k,v){
			html += "<dl><dt>"+v['name']+"</dt><dd>"+v['value']+"</dd></dl>";
		})

		//更新统计图表及数据
		getEcharts_rose(datas['json']);

		auth.siblings('div').html(html);

		hgroup['1'].classList.add("current");
		hgroup['0'].classList.remove("current");
	},
	airmaplists:function(data,page,isFromSha){
		var html,auth = $('.lists ul'),title,list='',info={},city={};
		var datas = JSON.parse(data);

		list += "<li class='title'><span>班次</span> <span>地点</span> <span>状态</span></li>";
		if (isFromSha == "1") {
			title = "<h3>机场离港航班</h3>";
		}else{
			title = "<h3>机场到港航班</h3>";
		}

		if(!datas['info'][isFromSha]||datas['info'][isFromSha]==undefined){
			html = title+list;
			//更新地图数据
			auth.html(html);
			return false;
		}
//console.log('isFromSha: '+isFromSha+' type:air');
//console.log(' page:'+page+" length:"+datas['info'][isFromSha].length);

		var start = (page-1)*8;
		var end = page*8;





		
		
		$.each(datas['info'][isFromSha],function(k,v){
				
				if(parseInt(k)>=end||parseInt(k)<start){ return true;}

				list +="<li>";
					list += "<span><i><img src='/Demo/public"+v.ICON+"' height='30px' />&nbsp;</i>";
				if (v['FROMCITY'] =='上海虹桥') {
					list += v.FLIGHT_NO+"</span><span>"+v.TOCITY+"</span><span>"+v.REMARK_XML+"</span></li>";
				}else{
					list += v.FLIGHT_NO+"</span><span>"+v.FROMCITY+"</span><span>"+v.REMARK_XML+"</span></li>";
				}
		})
		html = title+list;

		//更新地图数据
		china_map(datas['topcity'][isFromSha]);
		auth.html(html);
	},
	trainmaplists:function(data,page,isFromSha){
		var html,auth = $('.lists ul'),title,list='',info={},city={};
		var datas = JSON.parse(data);

		list += "<li class='title'><span>车次</span> <span>地点</span> <span>状态</span></li>";
		if (isFromSha == "1") {
			title = "<h3>高铁出发班次</h3>";
		}else{
			title = "<h3>高铁到达班次</h3>";
		}
		if(!datas['info'][isFromSha]||datas['info'][isFromSha]==undefined){
			html = title+list;
			//更新地图数据
			auth.html(html);
			return false;
		}
//console.log('isFromSha: '+isFromSha+' type:train');
//console.log(' page:'+page+" length:"+datas['info'][isFromSha].length);

		var start = (page-1)*8;
		var end = page*8;



		
		$.each(datas['info'][isFromSha],function(k,v){
   				
   				if(parseInt(k)<start||parseInt(k)>=end){ return  true;}

   				
				list +="<li>";
				if (v['FROMCITY'] =='上海虹桥') {
					list +="<span><i><img src='/Demo/public"+v.ICON+"' height='30px' />&nbsp;</i>"+v.FLIGHT_NO+"</span><span>"+v.TOCITY+"</span><span>"+v.REMARK+"</span></li>";
				}else{
					list += "<span><i><img src='/Demo/public"+v.ICON+"' height='30px' />&nbsp;</i>"+v.FLIGHT_NO+"</span><span>"+v.FROMCITY+"</span><span>"+v.REMARK+"</span></li>";
				}
		})
		html = title+list;

		//更新地图数据
		china_map(datas['topcity'][isFromSha]);
		auth.html(html);
	}
}

//数据存储
var save_cache = {
	statis_air:function(data){
		localStorage.setItem('statis_air',data);
	},
	statis_train:function(data){
		localStorage.setItem('statis_train',data);
	},
	airmaplists:function(data){
		localStorage.setItem('airmaplists',data);
	},
	trainmaplists:function(data){
		localStorage.setItem('trainmaplists',data);
	}
}

//数据请求
var moredata = {
	statis_air:function(isFromSha){
		//航班统计
		var url = '/Demo/public/index.php/test/test/statis_air',data = {isFromSha:isFromSha};
		ajaxs(ajaxParam(url, data, save_cache.statis_air, send_error));
	},
	statis_train:function(isFromSha){
		//航班统计
		var url = '/Demo/public/index.php/test/test/statis_train',data = {isFromSha:isFromSha};
		ajaxs(ajaxParam(url, data, save_cache.statis_train, send_error));
	},
	airmaplists:function(isFromSha){
		var url = '/Demo/public/index.php/test/test/get_airInfo',data = {isFromSha:isFromSha};
		ajaxs(ajaxParam(url, data, save_cache.airmaplists, send_error));
	},
	trainmaplists:function(isFromSha){
		var url = '/Demo/public/index.php/test/test/get_trainInfo',data = {isFromSha:isFromSha};
		ajaxs(ajaxParam(url, data, save_cache.trainmaplists, send_error));
	}
}

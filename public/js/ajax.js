// 原生js
var ajax = {};
ajax.x = function () {
	if (typeof XMLHttpRequest !== 'undefined') {
		return new XMLHttpRequest();
	}
	var versions = [
	"MSXML2.XmlHttp.6.0",
	"MSXML2.XmlHttp.5.0",
	"MSXML2.XmlHttp.4.0",
	"MSXML2.XmlHttp.3.0",
	"MSXML2.XmlHttp.2.0",
	"Microsoft.XmlHttp"
	];

	var xhr;
	for (var i = 0; i < versions.length; i++) {
		try {
			xhr = new ActiveXObject(versions[i]);
			break;
		} catch (e) {
		}
	}
	return xhr;
};

ajax.send = function (url, method, data, success,fail,async) {
	if (async === undefined) {
		async = true;
	}
	var x = ajax.x();
	x.open(method, url, async);
	x.onreadystatechange = function () {
		if (x.readyState == 4) {
			var status = x.status;
			if (status >= 200 && status < 300) {
				success && success(x.responseText,x.responseXML)
			} else {
				fail && fail(status);
			}
			
		}
	};
	if (method == 'POST') {
		x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	}
	x.send(data)
};

ajax.get = function (url, data, callback, fail, async) {
	var query = [];
	for (var key in data) {
		query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
	}
	ajax.send(url + (query.length ? '?' + query.join('&') : ''), 'GET', '', callback, fail, async)
};

ajax.post = function (url, data, callback, fail, async) {
	var query = [];
	for (var key in data) {
		query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
	}	
	ajax.send(url,'POST', query.join('&'), callback, fail, async)
};
 
 /**异步传输操作 jquery**/
	function ajaxs(params) 
	{

		$.ajax({
			type : params.method,
			async : params.async,
			url : params.url,
			cache: false,
			data : params.date,
			dataType : "text",
			timeout : 5000,
			success : function(data)
			{
                if(data == "" || data == "undefined")
                {
                    params.error();
                }
                else
                {
                    params.success(data)
                }
			},
			error : function(XMLHttpRequest, textStatus, errorThrown)
			{
				params.error();
			}
		});
	}
	/**封装ajax参数**/
	function ajaxParam(url, data, success, error, async, method)
	{
		var param =
		{
			url : url,
			date : data,
			dataType : "text",
			success : success,
			error : error,
			async : "undefined" == (typeof async) ? true : async,
			method : "undefined" == (typeof method) ? "post" : method
		};
		return param;
	};
	 function settime()
	 {
	 	 var date = new Date();
		 var hours = date.getHours();
		 var minue = date.getMinutes();
		 if(hours<10){
		 	hours = "0"+hours;
		 }
		 if(minue<10){
		 	minue = "0"+minue;
		 }
		 var time = hours+':'+minue;
		 $('.weath').text(time);
	 }
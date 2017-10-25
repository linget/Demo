 /**异步传输操作**/
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
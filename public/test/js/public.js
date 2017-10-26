//读取cookie
function getCookie(name)
{
	 var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
            if (arr != null) return unescape(arr[2]); return null;
}
/* 数据更新判断 */
var judge = {
	main:function(type,isFromSha){
		var change_from,type,isFromSha;
		switch(isFromSha)
		{
			//到达
			case '0':
				isFromSha = '1';

				document.cookie="change_from=isFromSha_end";
				document.cookie="isFromSha=1";
			break;
			//出发
			case '1':
				isFromSha = '0';
				document.cookie="isFromSha=0";
			break;
		}

		var change = getCookie('change_from');
		if(change == 'isFromSha_end')
		{
			document.cookie="change_from=1";
			switch(type)
			{
				//到达
				case 'air':
					type = 'train';
					document.cookie="changetype="+type;
				break;
				//出发
				case 'train':
					type = 'road';
					document.cookie="changetype="+type;
				break;

				case 'road':
					type = 'air';
					document.cookie="changetype="+type;
				break;

			}

		}
		
	},
	refresh:function(){
		var data,nowdata={},type,countpage,isFromSha,statis_air,statis_train;

		type = getCookie('changetype');
		isFromSha = getCookie('isFromSha');
		statis_air = localStorage.getItem('statis_air');
		statis_train = localStorage.getItem('statis_train');
		countpage = 1;

		if (type == 'air') 
		{
			data = localStorage.getItem('airmaplists');
			
		}else if (type == 'train') 
		{
			data = localStorage.getItem('trainmaplists');
		}else{
			data = '';
		}

		if(data){ 
			nowdata = JSON.parse(data);
			if(nowdata['info'][isFromSha]!=undefined)
			{
				countpage = Math.ceil(nowdata['info'][isFromSha].length/8);
			}
		}

		
		if (page>countpage) 
		{
			judge.main(type,isFromSha);//状态判断与更新
			page = 1;return
		};
		
		isFromSha = getCookie('isFromSha');
		type = getCookie('changetype');

//console.log("countpage:"+countpage+"current_Page:"+page+'isFromSha: '+isFromSha+' type:'+type);
		if(type== 'air')
		{
			//更新页面
			loadhtml.airmaplists(data,page++,isFromSha);
			loadhtml.header1(statis_air,isFromSha);
		}else if(type == 'train'){

			loadhtml.trainmaplists(data,page++,isFromSha);
			loadhtml.header2(statis_train,isFromSha);
		}
	}
}



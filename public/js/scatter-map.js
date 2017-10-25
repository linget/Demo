function scatter(arr){
	
}
	
/* 国内所有机场数据 */
function all_city(language)
	{
		var url;
		if(language == 'zh_cn'){
			url = '/Demo/public/js/city.json';
		}else{
			url = '/Demo/public/js/encity.json';
		}


		$.get(url, function(geojson){

			$.each(geojson,function(ke,va){

				lng = va['c_lng']; 
				lat = va['c_lat'];
				city = va['c_city'];
				pla = [lng,lat];
				place[city] = pla;	
			})
			//writefile(place);
			//console.log(place);
		})
	}
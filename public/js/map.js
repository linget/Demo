function getEcharts(arr,citys_data,route_citys){

	var citys = [{name:'',value:'70'}],datas = [],effects = [];

	if(arr!= undefined){
		route_citys = arr.json_info;
		citys_data  = arr.citys;
		//citys_effect= arr.effect;
	}
/*  console.log(citys_effect);

	$.each(citys_effect,function(ka,va){
		effects[ka] = {'name':va['name'],'value':va['value']};
	})*/
	$.each(route_citys,function(ks,vs){
		datas[ks] = [{'name':vs['FROMCITY']},{'name':vs['TOCITY'],'value':vs['value']}];
	})
// console.log(datas);
	for (var i = 0; i < citys_data.length; i++) {
		 citys[i+1] = {name:citys_data[i],value:citys_data[i]}
	};
// console.log(citys);	
    // Step:3 conifg ECharts's path, link to echarts.js from current page.
    // Step:3 为模块加载器配置echarts的路径，从当前页面链接到echarts.js，定义所需图表路径
  
    require.config({
        paths: {
            echarts: '/Demo/public/js'
        }
    });
   
    // Step:4 require echarts and use it in the callback.
    // Step:4 动态加载echarts然后在回调函数中开始使用，注意保持按需加载结构定义图表路径
    require(
        [
            'echarts',
            'echarts/chart/map'
        ],
        function (ec) {
            // --- 地图 ---
            var myChart2 = ec.init(document.getElementById('mainMap'));
            
            myChart2.setOption({
				dataRange: {
					min : 0,
					max : 100,
					calculable : true,
					color: ['#ff3333', 'orange', 'yellow','lime','aqua'],/*色调*/
					textStyle:{
						color:'#fff'
					}
				},
				layoutCenter: ['50%', '50%'],
				series : [
					{
						name: '全国',
						type: 'map',
						roam: true,
						hoverable: false,/*触摸效果*/
						mapType: 'china',
						itemStyle:{
							normal:{
								borderColor:'rgba(100,149,237,1)',
								borderWidth:0.5,
								areaStyle:{
									color: '#1b1b1b'
								}
							}
						},
						data:[],
						markLine : {
							smooth:true,
							symbol: ['none', 'circle'],  
							symbolSize : 1,
							itemStyle : {
								normal: {
									color:'#fff',
									borderWidth:0,
									borderColor:'rgba(30,144,255,0.5)'
								}
							},
							data : []
						},
						geoCoord: place,
						/*城市点*/
						markPoint : {
							symbol:'emptyCircle',
							symbolSize : function (v){
								return 5 + v/10000
							},
							effect : {
								show: false,
								shadowBlur : 0
							},
							itemStyle:{
								normal:{
									label:{show:false}
								},
								emphasis: {
									label:{show:false,
										position:'left'}

								}
							},
							data :effects
							/*扩散*/
						}
					},
					{
						name: '上海 ',
						type: 'map',
						mapType: 'china',
						data:[],
						markLine : {
							smooth:true,/*弧度*/
							effect : {
								show: false,/*航线光点*/
								scaleSize: 1,/*航线光点大小*/
								period: 30,/*航线光点快慢*/
								color: '#fff',/*航线颜色*/
								shadowBlur: 100
							},
							itemStyle : {
								normal: {
									label:{show:false},
									borderWidth:1,/*航线*/
									lineStyle: {
										type: 'solid',
										shadowBlur: 10
									}
								}
							},

							 data :datas
						},/*航线*/
						markPoint : {
							symbol:'emptyCircle',/*实心空心*/
							symbolSize : function (v){
								return 2.8/*地点大小*/
							},
							effect : {
								show: false,
								shadowBlur : 0
							},
							itemStyle:{
								normal:{
									label:{show:true,
										  position:'bottom',
										  textStyle: {
													fontSize: '12'
												}
										  }
								},
								emphasis: {
									label:{show:true}
								}
							},
							data : citys
							/*地址*/
						}
					}
				]
        });
	});
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

	//写入文件
	var writefile = function(data){
		var fso, tf;
		fso = new ActiveXObject("Scripting.FileSystemObject");
		// 创建新文件
		tf = fso.CreateTextFile("D:/wamp/www/Demo/public/js/aircity.json", true);
		
		// 填写数据，并增加换行符
		//tf.WriteLine("Testing 1, 2, 3.") ;
		
		// 增加3个空行
		//tf.WriteBlankLines(3) ;
		
		// 填写一行，不带换行符
		tf.Write (data);
		
		// 关闭文件
		tf.Close();
	}


	/* 列车地址数据 */
	function train_city(language)
	{
		var url;
		// if(language == 'zh_cn'){
		 	url = '/Demo/public/js/traincity.json';
		// }else{
		// 	url = '/Demo/public/js/entraincity.json';
		// }
	
		
		$.get(url, function(geojson) {		
			$.each(geojson,function(ke,va){
				
				lng = va['c_lng']; 
				lat = va['c_lat'];
				city = va['c_city'];
				pla = [lng,lat];
				place[city] = pla;			
			})
		})
		
	}				
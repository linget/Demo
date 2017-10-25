/**
 * [map 态势地图]
 * @param  data 航班延误程度
 * @param  city 重大延误/取消城市
 * 中国地图
 */
var geoCoord = function(){
	if(localStorage.getItem('geoCoord')){ return;}//存在直接跳过本函数

	/* 国内所有机场数据 */
    var lng,lat,city,pla;
	var url,url2,place={},place2={},airgeo,arr;
		url = '/Demo/public/js/city.json';

    //坐标录入-air
	$.get(url, function(geojson){
		$.each(geojson,function(ke,va){

			lng = va['c_lng']; 
			lat = va['c_lat'];
			city = va['c_city'];
			pla = [lng,lat];
			place[city] = pla;	
		})
		 //console.log(place);
		//place = JSON.stringify(place);
		//localStorage.setItem('geoCoord',place);	
	})
    //console.log(place);
    //坐标录入-train
    url2 = '/Demo/public/js/traincity.json';
    $.get(url2, function(geojson){
        $.each(geojson,function(ke,va){

            lng = va['c_lng']; 
            lat = va['c_lat'];
            city = va['c_city'];
            pla = [lng,lat];
            place[city] = pla;  
        })
        place = JSON.stringify(place);
        localStorage.setItem('geoCoord',place);   
    })
    //console.log(place);
}

var china_map = function(city)
{
	var citys = [],citys_place = [{name:'上海虹桥',value:'上海虹桥'}];
	var geo  = localStorage.geoCoord;
	geo = JSON.parse(geo);
	//console.log(geo);

	if (city !=undefined) 
	{
		$.each(city,function(k,v){
			citys[k] = {name:v['name'],value:v['value']};
			citys_place[k+1] = {name:v['name'],value:v['name']};
		})
		
	};
 //console.log(citys_place);
 
	  require.config({  
        paths: {  
            echarts: '/Demo/public/test/js/echarts-2.2.7'  
        }  
    });
    require(  
        [  
            //这里的'echarts'相当于'./js'  
            'echarts',  
            'echarts/chart/map'  
        ],function(echarts){
             var myChart3 = echarts.init(document.getElementById('china-map'));
                    
              var option = {
    title : {
        text: '',
        subtext: '',
        sublink: 'http://www.pm25.in',
        x:'center'
    },
    tooltip : {
        trigger: 'item'
    },
    dataRange: {
        min : 0,
        max : 500,
        calculable : true,
        show:false,
        color: ['maroon','purple','red','orange','yellow','lightgreen']
    },

    series : [
        {
            name: '',
            type: 'map',
            mapType: 'china',
            hoverable: false,
            roam:true,
            data : [],
            markPoint : {
                symbolSize: 5,       // 标注大小，半宽（半径）参数，当图形为方向或菱形则总宽度为symbolSize * 2
                itemStyle: {
                    normal: {
                        borderColor: '#87cefa',
                        borderWidth: 1,            // 标注边线线宽，单位px，默认为1
                        label: {
                            show: false
                        }
                    },
                    effect : {
                    show: false,
                    shadowBlur : 0
                },
                    emphasis: {
                        borderColor: '#1e90ff',
                        borderWidth: 5,
                        label: {
                            show: false
                        }
                    }
                },
                data : citys
            },
            geoCoord: geo
        },
        {
            name: '',
            type: 'map',
            mapType: 'china',
            data:[],
            markPoint : {
                symbol:'emptyCircle',
                symbolSize : function (v){
                    return 10 + v/100
                },
                effect : {
                    show: true,
                    shadowBlur : 0
                },
                itemStyle:{
                    normal:{
                        label:{show:false}
                    }
                },
                data : citys
            }
        }
    ]
};

    myChart3.setOption(option);         
    })
}
<?php if (!defined('THINK_PATH')) exit(); /*a:13:{s:70:"D:\wamp\www\Demo\public/../application/test\view\index\infomation.html";i:1509328601;s:42:"../application/test/view/test/header1.html";i:1508736253;s:42:"../application/test/view/test/header2.html";i:1508735922;s:72:"D:\wamp\www\Demo\public/../application/test\view\.\public\road_info.html";i:1508839784;s:71:"D:\wamp\www\Demo\public/../application/test\view\.\public\road_map.html";i:1508295429;s:38:"../application/test/view/test/map.html";i:1508492177;s:74:"D:\wamp\www\Demo\public/../application/test\view\.\public\air_scoller.html";i:1508489216;s:75:"D:\wamp\www\Demo\public/../application/test\view\.\public\weather_time.html";i:1508926168;s:69:"D:\wamp\www\Demo\public/../application/test\view\.\public\market.html";i:1508326758;s:72:"D:\wamp\www\Demo\public/../application/test\view\.\public\road_list.html";i:1508899945;s:40:"../application/test/view/test/lists.html";i:1508909949;s:68:"D:\wamp\www\Demo\public/../application/test\view\.\public\video.html";i:1508307979;s:66:"D:\wamp\www\Demo\public/../application/test\view\.\public\img.html";i:1508391220;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <title>框架样式1</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="__PUBLIC__/css/style_info.css">
        <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
        <script src="http://webapi.amap.com/maps?v=1.3&key=b2ebe352bdba412ae771d02de2ca577f"></script>
        <script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
        <script src="__PUBLIC__/js/echarts.min.js" ></script>
         <script type="text/javascript" src="__PUBLIC__/js/jquery-1.8.0.js"></script>
         <script type="text/javascript" src="__PUBLIC__/js/keyScreen.js"></script> 
         <script type="text/javascript" src="__PUBLIC__/js/smallScreen.js"></script>
    </head>

<style type="text/css">
   hgroup{width: 22%;height: 100%;display: inline-block;float: left;background-color: #d7edb5;border-right: 1px solid white;text-align: center;box-sizing:border-box;}
    hgroup.current{background-color: #fffdef;}
    .lists ul h3{height:10%;line-height: 42px;}
    .title{height:10%;text-align: left;background-color: #97d334;color: #fff;}

</style>
    <body>
        <div class="sy_content" id="main">
        <div class="sy_left">
             <div class="sy_left_header">
                 <div class="sy_col_4 sy_full">
                     <!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>新界面</title>
	<script src="__PUBLIC__/js/jquery-1.8.0.js"></script>
</head>
<style type="text/css">
/* 	*{margin: 0;padding:0;font-family: 'microsoft Yahei';font-size: 18px;font-size: 1.2rem;}
html,body{width: 100%;height: 100%;display: inline-block;background-color: #fffdef;} */
	header{width: 100%;height: 100%;overflow: hidden;display: inline-block;}
	
	hgroup{width: 100%;height: 100%;display: inline-block;float: left;background-color: #d7edb5;border-right: 1px solid white;text-align: center;box-sizing:border-box;}
	hgroup.current{background-color: #fffdef;}
	
	hgroup h1{width: 100%;height:10%;display: inline-block;background-color: #97d334;color: #fff;letter-spacing: 0.2rem;font-size: 1rem;}

	hgroup dl{width: 100%;height: 8%;float: left;display: inline-block;font-size: 1rem;}
	hgroup dl dt{width: 40%;height: 100%;display: inline-block;float: left;text-align: left;padding-left: 10%;}

    #echarts-pie,#echarts-rose{width: 100%;height: 55%;}

</style>
<body>
<header>  
<hgroup class="current">
	<h1>航班</h1>
	<div id="echarts-pie"></div>
	
	<?php if(is_array($statis_air) || $statis_air instanceof \think\Collection): $ko = 0; $__LIST__ = $statis_air;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($ko % 2 );++$ko;?>
	<dl><dt><?php echo $vo['name']; ?></dt><dd><?php echo $vo['value']; ?></dd></dl>
	<?php endforeach; endif; else: echo "" ;endif; ?>

    </hgroup>
</header>
<script src="__PUBLIC__/test/js/echarts/echarts.js"></script>
<script src="__PUBLIC__/test/js/echarts/Statistics-pie.js"></script>
<script type="text/javascript">
	/* 参数初始化 */
	var statis_airjson= {};
    //统计数据
	statis_airjson = <?php echo $statis_airjson; ?>;


    var myChart1 = echarts.init(document.getElementById('echarts-pie'));
    myChart1.setOption(getEcharts_pie(statis_airjson));
</script>
</body>
</html>
                 </div>
                 <div class="sy_col_4 sy_full" style="background: #009cda;">
                     <!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>新界面</title>
	<script src="__PUBLIC__/js/jquery-1.8.0.js"></script>

</head>
<style type="text/css">
	*{margin: 0;padding:0;font-family: 'microsoft Yahei';font-size: 18px;font-size: 1rem;}
	html,body{width: 100%;height: 100%;display: block;background-color: #fffdef;}
	header{width: 100%;height: 100%;overflow: hidden;}
	
	hgroup{width: 100%;height: 100%;display: inline-block;float: left;background-color: #d7edb5;border-right: 1px solid white;text-align: center;box-sizing:border-box;}
	hgroup.current{background-color: #fffdef;}
	
	hgroup h1{width: 100%;height:10%;display: inline-block;background-color: #97d334;color: #fff;letter-spacing: 0.2rem;font-size: 1rem;}

	hgroup dl{width: 100%;height: 8%;float: left;display: inline-block;font-size: 1rem;}
	hgroup dl dt{width: 40%;height: 100%;display: inline-block;float: left;text-align: left;padding-left: 10%;}

    #echarts-pie,#echarts-rose{width: 100%;height: 55%;}
</style>
<body>
	<header>

		  <hgroup>
			<h1>火车</h1>
			<div id="echarts-rose"></div>	
			<?php if(is_array($statis_train) || $statis_train instanceof \think\Collection): $ko = 0; $__LIST__ = $statis_train;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($ko % 2 );++$ko;?>
			<dl><dt><?php echo $vo['name']; ?></dt><dd><?php echo $vo['value']; ?></dd></dl>
			<?php endforeach; endif; else: echo "" ;endif; ?>

		    </hgroup>
	</header>

<script src="__PUBLIC__/test/js/echarts/echarts.js"></script>
<script src="__PUBLIC__/test/js/echarts/Statistics-pie.js"></script>
<script type="text/javascript">
	/* 参数初始化 */
	var statis_trainjson={};

	statis_trainjson= <?php echo $statis_trainjson; ?>;

    var myChart2 = echarts.init(document.getElementById('echarts-rose'));
    myChart2.setOption(getEcharts_rose(statis_trainjson));
</script>

</body>
</html>
                 </div>
                 <div class="sy_col_4 sy_full">
                     <div class="sy_road_info">
    <div class="sy_road_info_header" style="height:10%;font-size:18px;font-weight:600;line-height:25px;">
        道路
    </div>
    <div class="sy_road_info_map">
        <div id="road_info" style="width: 100%;height:100%;text-align: center;"></div>
        <script type="text/javascript">
            var roadChart =<?php echo $roadChart; ?>;
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('road_info'));

        // 指定图表的配置项和数据
        var option = {
    tooltip: {
        trigger: 'axis',
         position: ['50%', '50%']
    },
    grid: {
            bottom: '20',
            top: '19',
            left:'20%',
            right:'20%'
	},
    xAxis:  {
        type: 'category',
         name:'时间',
        boundaryGap: false,
        data: roadChart[0]
    },
    yAxis: {
        type: 'value',
        name:'拥堵指标',
        left:'10',
        nameGap:'5',
        axisLabel: {
            formatter: '{value} %'
        }
    },
    series: [
        {
            name:'拥堵',
            type:'line',
            itemStyle:{
                    normal:{
                        label:{position: "right",
                                show: true
                        }
                    }
                },
            data:roadChart[1]
        }
    ]
};


        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
    </div>
    <div class="sy_road_info_info">
        <table>
            <tr>
                <td class="sy_td_left">虹桥商务区</td>
                <td class="sy_td_right"><?php echo $eveArr[0]; ?></td>
            </tr>
            <tr>
                <td class="sy_td_left">国家会展中心</td>
                <td><?php echo $eveArr[1]; ?></td>
            </tr>
            <tr>
                <td class="sy_td_left">上海虹桥机场</td>
                <td><?php echo $eveArr[2]; ?></td>
            </tr>
            <tr>
                <td class="sy_td_left">虹桥火车站</td>
                <td><?php echo $eveArr[3]; ?></td>
            </tr>
        </table>
    </div>
</div>
                 </div>
             </div>
            <div class="sy_left_main">
                <div id="container" class="sy_road_map_map"  style=" position: relative;" ></div>
  <script>
        var map = new AMap.Map('container', {
            resizeEnable: true,
            center: [121.318950,31.194022],
            zoom: 13
        });
        //实时路况图层
        var trafficLayer = new AMap.TileLayer.Traffic({
            zIndex: 10
        });
        trafficLayer.setMap(map);
        var isVisible = true;
        
        //标记
//        marker = new AMap.Marker({
//            position: [121.31296,31.194018],
//            title: "虹桥商务中心",
//            map: map
//        });
//        marker1 = new AMap.Marker({
//            position: [121.302151,31.189984],
//            title: "国家会展中心",
//            map: map
//        });
    </script>
               <!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>新界面</title>
	<script src="__PUBLIC__/js/jquery-1.8.0.js"></script>

</head>
<style type="text/css">
	*{margin: 0;padding:0;font-family: 'microsoft Yahei';font-size: 18px;}
	html,body{width: 100%;height: 100%;display: block;background-color: #fffdef;}
	section{width: 100%;height: 100%;background-color: #fffdef;float: left;}
	#china-map{width: 98%;height: 98%;display: inline-block;float: left;}
</style>
<body>

<section>
   	<div id="china-map" ></div>
</section>

<script src="__PUBLIC__/test/js/echarts/china.json"></script>
<script type="text/javascript">
	/* 参数初始化 */
	var city={};

    //态势数据
    city= <?php echo $topcity; ?>; 
</script>
<script src="__PUBLIC__/test/js/echarts-2.2.7/echarts.js"></script>
<script src="__PUBLIC__/test/js/map.js"></script>
<script type="text/javascript">
    geoCoord();//加载中国地图
    china_map(city);
</script>


</body>
</html>
            </div>
            <div class="sy_left_footer">
                 <marquee direction="left" scrollamount="4">
    <div class="sy_marquee_info sy_font_yellow">
        CA9856航班延误，请各位旅客合理安排时间！
    </div>
</marquee>
            </div>
        </div>
        <div class="sy_right">
            <div class="sy_right_weather">
                <div class="sy_weather">
    <div class="sy_weather_left">
        <div class="sy_weather_left">
             <img src="__PUBLIC__/weather/<?php echo $weather['icon']; ?>">
            <p><?php echo $weather['tmp_min']; ?>℃</p>

        </div>
        <div class="sy_weather_right">
            <p><?php echo $weather['weather']; ?></p>
            <p><?php echo $weather['tmp_min']; ?>℃ ~ <?php echo $weather['tmp_max']; ?>℃</p>
            <p>PM2.5 :  <?php echo $weather['pm2_5']; ?>   <?php echo $weather['quality']; ?></p>
        </div>		
    </div>
    <div class="sy_weather_right">
        <h1 id="time"><!-- <span><?php echo $variable; ?></span> --><?php echo $now; ?></h1>
        <pre><?php echo $weather['date']; ?></pre>
        <pre><?php echo $weather['week']; ?></pre>
    </div>
</div>
<script type="text/javascript" src="__PUBLIC__/test/js/time.js"></script>
<script type="text/javascript">
    nowtime();//客户端自动校时
</script>
            </div>
             <div class="sy_right_market">
                 <div class="sy_market">
    <?php if(is_array($sha) || $sha instanceof \think\Collection): $i = 0; $__LIST__ = $sha;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
     <div class="sy_market_left">
        <b><?php echo $vo['name']; ?></b>
        <?php if($vo['increase'] > 0): ?>
        <p>最新指数:<span class="sy_red"><?php echo $vo['nowpri']; ?></span></p>
        <p>指数涨跌:<span class="sy_red"><?php echo $vo['increPer']; ?>%</span></p>
        <p>涨跌幅度:<span class="sy_red"><?php echo $vo['increase']; ?></span></p>
         <p>总成交量:<span class="sy_red"><?php echo $vo['dealNum']; ?></span></p>
        <?php else: ?>
        <p>最新指数:<span class="sy_green"><?php echo $vo['nowpri']; ?></span></p>
        <p>指数涨跌:<span class="sy_green"><?php echo $vo['increPer']; ?>%</span></p>
        <p>涨跌幅度:<span class="sy_green"><?php echo $vo['increase']; ?></span></p>
         <p>总成交量:<span class="sy_green"><?php echo $vo['dealNum']; ?></span></p>
        <?php endif; ?>
    </div>
   <?php endforeach; endif; else: echo "" ;endif; ?>
</div>
            </div>
             <div class="sy_right_info">
                 <div class="sy_road_list">
    <h3>道路交通状态</h3>
    <table class="sy_road_head" >
        <tr style="height:10%;background-color:#97d334;">
            <th class="sy_th_left">道路</th>
            <th class="sy_th_right" >描述</th>
        </tr>
        <tr>
            <td colspan="2">
                <marquee direction="up" scrollamount="4">
            <table>
                <?php if(is_array($roadList) || $roadList instanceof \think\Collection): $i = 0; $__LIST__ = $roadList;if( count($__LIST__)==0 ) : echo "道路畅通" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
               <tr>
                    <td class="sy_td_left"><?php echo $vo['road']; ?></td>
                    <td class="sy_td_right"><?php echo $vo['description']; ?></td>
                </tr>
                <?php endforeach; endif; else: echo "道路畅通" ;endif; ?>
            </table>
                </marquee>
            </td>
        </tr>
    </table>
    

</div>
                 <!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>新界面</title>
</head>
<style type="text/css">
	*{margin: 0;padding:0;font-family: 'microsoft Yahei';font-size: 18px;}
	html,body{width: 100%;height: 100%;display: block;background-color: #fffdef;}


	aside{width: 100%;height: 100%;background-color: #fffdef;float: right;}
	
	aside nav{width: 100%;height:100%;display: inline-block;box-sizing:border-box;}


	aside h3{width: 100%;height: 10%;text-align: left;}
	aside nav ul{width: 100%;height: 100%;border-left: 1px solid #97d334;}
	aside nav ul li{width: 100%;height: 10%;display: inline-block;list-style: none;}
	aside nav ul li:nth-child(1){background-color: #97d334;height:8%;text-align: left;background:none;}
/* 	aside nav ul li{width: 100%;height: 8%;display: inline-block;list-style: none;font-size: 1rem;}
aside nav ul li:nth-child(1){height:10%;border:none;background-color: #97d334;font-size: 1rem;}
 */
    aside nav ul li span{width: 30%;height: auto;display: inline-block;line-height: 42px;text-align: center;}
    aside nav ul li span:nth-child(1){width: 30%;text-align: left;padding-left: 6%;}
    aside nav ul li.title span:nth-child(1){text-align: center;padding-left: -5%;}
    aside nav ul li span img{vertical-align: middle;}
    aside nav ul li span.yellow{color:#ff9900;}
    aside nav ul li span.red{color:red;}
	

</style>
<body>


<aside>
	<nav  class="lists">
	    <ul>
	        <h3>机场离港航班</h3>
	        <li class="title"><span>班次</span> <span>地点</span> <span>状态</span></li>
	        <?php if(is_array($air_info) || $air_info instanceof \think\Collection): $i = 0; $__LIST__ = $air_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
	        	 <li><span><i><img src="__PUBLIC__<?php echo $vo['ICON']; ?>" height="30px" />&nbsp;</i>
                <?php echo $vo['FLIGHT_NO']; ?></span> <span><?php echo $vo['CITY']; ?></span> <span><?php echo $vo['REMARK_XML']; ?></span></li>
	        <?php endforeach; endif; else: echo "" ;endif; ?>

	        </ul>
    	</nav>
</aside>
</body>
</html>
            </div>
        </div>
        </div>
        <div id="testScreen"  style="display:none;">test</div>
        <div id="testScreen_1"  style="display:none;">test</div>
        <div id="alm" style="width: 125px;height: 395px;display:none; "></div>
        <div id="delay_1" style="display:none;"></div>
        <div id="delay_2" style="display:none;"></div>
        <div id="delay_3" style="display:none;"></div>
        <!-- 视频模块  -->
        <div id="video" style="display:none;">
    <video  id="videoPlay"   autoplay="autoplay" loop="loop" >
        your browser does not support the video tag
     </video>
</div>
        <!-- 图片模块  -->
        <div id="imgModul" style="display: none;">
    <img src="__PUBLIC__/images/cdshh-dining-chinese-restaurant-table-1680-945.jpg" />
</div>
        <script>
            $(document).ready(function() {
               // $('.sy_road_info').css('background','#fff');
            });
        </script>
        <script>
            //黄历框数据
             var almanac = '';
             var almanacInfo = <?php echo $almanceInfo; ?>;
             console.log(almanacInfo);
             almanac += '<div class="sy_almanac"><div class="sy_almanac_calendar"><div class="sy_almanc_date_panel">';
             almanac += '<div class="sy_almanac_date_bar"> '+almanacInfo['month']+'</div>';
             almanac += '<div class="sy_almanac_date_day">'+almanacInfo['day']+'</div>';
             almanac += '</div><div class="sy_almanac_dates">';
             almanac += '<p>'+almanacInfo['weekday']+'</p>';
             almanac += '<p>农历'+almanacInfo['days']+'</p>';
             almanac += '<p>'+almanacInfo['lunar']+'</p>';
             almanac += '</div></div><div class="sy_almanac_action"><div class="sy_almanac_half"> <h3>宜</h3>';
             almanac += '<ul>';
             for(var i in almanacInfo['suit']) {
                 almanac += '<li>'+almanacInfo['suit'][i]+'</li>';
             }
             almanac += '</ul>';
             almanac += '</div><div class="sy_almanac_half"><h3>忌</h3>';
             almanac += '<ul>';
             for(var i in almanacInfo['avoid']) {
                 almanac += '<li>'+almanacInfo['avoid'][i]+'</li>';
             }
             almanac += '</ul>';
             almanac += '</div></div></div>';
            $(function(){
                //调用keyScreen.js插件
                $(document).keyScreen('main,video');
                $('#testScreen').screen('<p>测试模块1</p>',{"width":"200px","height":"200px","left":"550px","background":"#fff","z-index":"10"});
                $('#testScreen_1').screen('<p>测试模块2</p>',{"width":"200px","height":"200px","left":"850px","background":"#ea53d4","z-index":"10"});
                $('#alm').screen(almanac,{'width':'127px','height':'395',"left":"62%","bottom":"25%","border":"none","background":"#fff","z-index":"10"});
            });
            
            document.addEventListener("keydown",function(e){
                var code = e.keyCode;
                if(code == 13){
                    mainOrVideo();
                }

              });
             function mainOrVideo() {
                var screen_switch = getCookie('screen_switch');
                if(screen_switch == 'video') {
                     var myVideo=document.getElementById("videoPlay");
                    myVideo.pause();
                    setCookie("vedio_to_list",0);
                } else {
                     $("#videoPlay").attr("src","__PUBLIC__/vedio/Cordis_Corporate Video_Jan2017_Bilingual_HD.mp4");
                     setCookie("vedio_to_list",1);
                }
        }
        //获取cookie
	function getCookie(cname)
	{
	  var name = cname + "=";
	  var ca = document.cookie.split(';');
	  for(var i=0; i<ca.length; i++) 
	  {
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	  }
	  return "";
	}
	//设置cookie
	function setCookie(name,value){
		document.cookie =name+ "="+value+"; path=/;";
	}
        </script>
        <script>
            var tipCode = '';
            var keyEvent = 0;
            $(document).ready(function(){
                document.addEventListener('keydown',function(e) {
                    var code = e.keyCode;
                    tipCode += String.fromCharCode(code);
                    //组合键直接执行
                    if(tipCode.length >1) {
                        clearTimeout(keyEvent);
                        codeToEvent(tipCode);
                    } else {
                        keyEvent = setTimeout('codeToEvent(tipCode)',1000);
                         
                    }
//                    setCode(code);
                });
            });
             //按键控制事件
            function codeToEvent(code) {
                if(code == ''){
                    return false;
                }
                switch(tipCode) {
                    case 'A':
                        tipCode = '';
                        $('#alm').screenMove();
                        break;
                    case 'Q':
                        tipCode = '';
                        $('#delay_1').screenMove();
                        $('#delay_2').screenMove();
                        $('#delay_3').screenMove();
                        break;
                    case 'AQ':
                        tipCode = '';
                        nextModul();
                        break;
                    case 'QA':
                        tipCode = '';
                         preModul();
                        break;
                    default:
                        tipCode = '';
                        break;
                }
            }
            
            //下个模块
            function nextModul() {
                var tip = getCurrentModul();
                console.log('nexttip:'+tip);
                switch(tip) {
                    case 1:
                        $('#main').css('display','none');
                        $('#video').css('display','');
                        playVideo();
                        break;
                    case 2:
                        $('#video').css('display','none');
                        $('#imgModul').css('display','');
                        pauseVideo();
                        break;
                    case 3:
                        $('#imgModul').css('display','none');
                        $('#main').css('display','');
                        break;
                    default:
                        $('#main').css('display','none');
                        $('#video').css('display','');
                        playVideo();
                        break;
                }
            }
            
            //上个模块
            function preModul() {
                var tip = getCurrentModul();
                 console.log('pretip:'+tip);
                switch(tip) {
                    case 1:
                        $('#main').css('display','none');
                        $('#imgModul').css('display','');
                        break;
                    case 2:
                        $('#video').css('display','none');
                        $('#main').css('display','');
                        pauseVideo();
                        break;
                    case 3:
                        $('#imgModul').css('display','none');
                        $('#video').css('display','');
                        playVideo();
                        break;
                    default:
                        $('#main').css('display','none');
                        $('#imgModul').css('display','');
                        break;
                }
            }
            
            //当前显示模块
            function getCurrentModul() {
                if($('#main').css('display') != 'none') {
                    return 1;
                }
                if($('#video').css('display') != 'none') {
                    return 2 ;
                }
                 if($('#imgModul').css('display') != 'none') {
                    return 3 ;
                }
                return 1 ;
            }
            
            //播放视频
            function playVideo() {
                $("#videoPlay").attr("src","__PUBLIC__/vedio/Cordis_Corporate Video_Jan2017_Bilingual_HD.mp4");
                setCookie("vedio_to_list",1);
            }
            
            //停止视频播放
            function pauseVideo() {
                var myVideo=document.getElementById("videoPlay");
                myVideo.pause();
                setCookie("vedio_to_list",0);
            }
        </script>
        <script>
            var delay = '<div class="sy_popup"><div class="sy_popup_header"><div class="sy_popup_header_scroll"><div class="sy_popup_logo">';
                delay += '<img src="__PUBLIC__/images/people1.png" alt="logo" />';
                delay += '</div><div class="sy_popup_line">&nbsp;</div><div class="sy_popup_line_active" style="width: 50px;"></div>';
                delay += '<div class="sy_popup_tip" style="left: 128px;">00:38</div>';
                delay += '<div class="sy_popup_node_active"></div>';
                delay += '<div class="sy_popup_node" style="left: 50%;"></div>';
                delay += '<div class="sy_popup_node" style="left: 86%;"></div>';
                delay += '<div class="sy_popup_node_time" style="left:19%;">10:00</div>';
                delay += '<div class="sy_popup_node_tip" style="left:20%;">定制</div>';
                delay += '<div class="sy_popup_node_time" style="left:49%;">11:00</div>';
                delay += '<div class="sy_popup_node_tip" style="left:50%;">值机</div>';
                delay += '<div class="sy_popup_node_time" style="left:85%;">11:30</div>';
                delay += '<div class="sy_popup_node_tip" style="left:86%;">起飞</div>';
                delay += '</div></div><div class="sy_popup_main"><div class="sy_col_6 sy_full"><div class="sy_popup_border_right">';
                delay += '<h2>航班号：CA9568</h2>';
                delay += '<p>始发地：上海虹桥T2航站楼</p>';
                delay += '<p>计划起飞：2017-10-20 10:30</p>';
                delay += '<p>目的地：成都双流机场</p>';
                delay += '<p>计划到达：2017-10-20 10:30</p>';
                delay += '<p>当前状态：<span class="sy_font_red">延误</span></p>';
                delay += '</div></div><div class="sy_col_6 sy_full"><div id="air_delay" style="width: 207px;height:169px;text-align: center;"></div>';
                delay += '<script type="text/javascript">';
                delay += '$(document).ready(function() { ';
                delay += 'var myChart = echarts.init(document.getElementById("air_delay"));';
                delay += 'var option = { title: { text: "近5日延误时间", textStyle:{fontSize:"14"}, left:"center" }, tooltip: { trigger: "axis", position: ["50%", "50%"]},';
                delay += 'grid: { bottom: "30", top: "40", left:"30", right:"30" },';
                delay += 'xAxis:  { type: "category",  name:"", boundaryGap: false, data: ["周一","周二","周三","周四","周五"] },';
                delay += 'yAxis: { type:  "value", name:"min", left:"10", nameGap:"5",';
                delay += 'axisLabel: { formatter: "{value}" }  },';
                delay += 'series: [ { name:"分钟", type:"line", itemStyle:{  normal:{  label:{position: "right",  show: true } } }, data:[11, 11, 15, 13, 12] }  ]';
                delay += '};';
                delay += ' myChart.setOption(option);});';
                delay += '<\/script>';
                delay += '</div></div></div>';
                
                var delay2 = '<div class="sy_popup"><div class="sy_popup_header"><div class="sy_popup_header_scroll"><div class="sy_popup_logo">';
                delay2 += '<img src="__PUBLIC__/images/people2.png" alt="logo" />';
                delay2 += '</div><div class="sy_popup_line">&nbsp;</div><div class="sy_popup_line_active" style="width: 50px;"></div>';
                delay2 += '<div class="sy_popup_tip" style="left: 128px;">00:38</div>';
                delay2 += '<div class="sy_popup_node_active"></div>';
                delay2 += '<div class="sy_popup_node" style="left: 50%;"></div>';
                delay2 += '<div class="sy_popup_node" style="left: 86%;"></div>';
                delay2 += '<div class="sy_popup_node_time" style="left:19%;">10:00</div>';
                delay2 += '<div class="sy_popup_node_tip" style="left:20%;">定制</div>';
                delay2 += '<div class="sy_popup_node_time" style="left:49%;">11:00</div>';
                delay2 += '<div class="sy_popup_node_tip" style="left:50%;">值机</div>';
                delay2 += '<div class="sy_popup_node_time" style="left:85%;">11:30</div>';
                delay2 += '<div class="sy_popup_node_tip" style="left:86%;">起飞</div>';
                delay2 += '</div></div><div class="sy_popup_main"><div class="sy_col_6 sy_full"><div class="sy_popup_border_right">';
                delay2 += '<h2>航班号：CA9568</h2>';
                delay2 += '<p>始发地：上海虹桥T2航站楼</p>';
                delay2 += '<p>计划起飞：2017-10-20 10:30</p>';
                delay2 += '<p>目的地：成都双流机场</p>';
                delay2 += '<p>计划到达：2017-10-20 10:30</p>';
                delay2 += '<p>当前状态：<span class="sy_font_red">延误</span></p>';
                delay2 += '</div></div><div class="sy_col_6 sy_full"><div id="air_delay_2" style="width: 207px;height:169px;text-align: center;"></div>';
                delay2 += '<script type="text/javascript">';
                delay2 += '$(document).ready(function() { ';
                delay2 += 'var myChart = echarts.init(document.getElementById("air_delay_2"));';
                delay2 += 'var option = { title: { text: "近5日延误时间", textStyle:{fontSize:"14"}, left:"center" }, tooltip: { trigger: "axis", position: ["50%", "50%"]},';
                delay2 += 'grid: { bottom: "30", top: "40", left:"30", right:"30" },';
                delay2 += 'xAxis:  { type: "category",  name:"", boundaryGap: false, data: ["周一","周二","周三","周四","周五"] },';
                delay2 += 'yAxis: { type:  "value", name:"min", left:"10", nameGap:"5",';
                delay2 += 'axisLabel: { formatter: "{value}" }  },';
                delay2 += 'series: [ { name:"分钟", type:"line", itemStyle:{  normal:{  label:{position: "right",  show: true } } }, data:[11, 11, 15, 13, 12] }  ]';
                delay2 += '};';
                delay2 += ' myChart.setOption(option);});';
                delay2 += '<\/script>';
                delay2 += '</div></div></div>';
                
                var delay3 = '<div class="sy_popup"><div class="sy_popup_header"><div class="sy_popup_header_scroll"><div class="sy_popup_logo">';
                delay3 += '<img src="__PUBLIC__/images/people3.png" alt="logo" />';
                delay3 += '</div><div class="sy_popup_line">&nbsp;</div><div class="sy_popup_line_active" style="width: 50px;"></div>';
                delay3 += '<div class="sy_popup_tip" style="left: 128px;">00:38</div>';
                delay3 += '<div class="sy_popup_node_active"></div>';
                delay3 += '<div class="sy_popup_node" style="left: 50%;"></div>';
                delay3 += '<div class="sy_popup_node" style="left: 86%;"></div>';
                delay3 += '<div class="sy_popup_node_time" style="left:19%;">10:00</div>';
                delay3 += '<div class="sy_popup_node_tip" style="left:20%;">定制</div>';
                delay3 += '<div class="sy_popup_node_time" style="left:49%;">11:00</div>';
                delay3 += '<div class="sy_popup_node_tip" style="left:50%;">值机</div>';
                delay3 += '<div class="sy_popup_node_time" style="left:85%;">11:30</div>';
                delay3 += '<div class="sy_popup_node_tip" style="left:86%;">起飞</div>';
                delay3 += '</div></div><div class="sy_popup_main"><div class="sy_col_6 sy_full"><div class="sy_popup_border_right">';
                delay3 += '<h2>航班号：CA9568</h2>';
                delay3 += '<p>始发地：上海虹桥T2航站楼</p>';
                delay3 += '<p>计划起飞：2017-10-20 10:30</p>';
                delay3 += '<p>目的地：成都双流机场</p>';
                delay3 += '<p>计划到达：2017-10-20 10:30</p>';
                delay3 += '<p>当前状态：<span class="sy_font_red">延误</span></p>';
                delay3 += '</div></div><div class="sy_col_6 sy_full"><div id="air_delay_3" style="width: 207px;height:169px;text-align: center;"></div>';
                delay3 += '<script type="text/javascript">';
                delay3 += '$(document).ready(function() { ';
                delay3 += 'var myChart = echarts.init(document.getElementById("air_delay_3"));';
                delay3 += 'var option = { title: { text: "近5日延误时间", textStyle:{fontSize:"14"}, left:"center" }, tooltip: { trigger: "axis", position: ["50%", "50%"]},';
                delay3 += 'grid: { bottom: "30", top: "40", left:"30", right:"30" },';
                delay3 += 'xAxis:  { type: "category",  name:"", boundaryGap: false, data: ["周一","周二","周三","周四","周五"] },';
                delay3 += 'yAxis: { type:  "value", name:"min", left:"10", nameGap:"5",';
                delay3 += 'axisLabel: { formatter: "{value}" }  },';
                delay3 += 'series: [ { name:"分钟", type:"line", itemStyle:{  normal:{  label:{position: "right",  show: true } } }, data:[11, 11, 15, 13, 12] }  ]';
                delay3 += '};';
                delay3 += ' myChart.setOption(option);});';
                delay3 += '<\/script>';
                delay3 += '</div></div></div>';
                $(function(){
                //调用keyScreen.js插件
                $('#delay_1').screen(delay,{'width':'420px','height':'250px',"left":"0","bottom":"0","border":"none","background":"#fff","z-index":"200"});
                $('#delay_2').screen(delay2,{'width':'420px','height':'250px',"left":"22%","bottom":"0","border":"none","background":"#fff","z-index":"10"});
                $('#delay_3').screen(delay3,{'width':'420px','height':'250px',"left":"44%","bottom":"0","border":"none","background":"#fff","z-index":"10"});
                
            });
        </script>

<script src="__PUBLIC__/test/js/echarts/echarts.js"></script>
<script src="__PUBLIC__/test/js/echarts/china.json"></script>
<script src="__PUBLIC__/test/js/echarts/Statistics-pie.js"></script>
<script src="__PUBLIC__/test/js/echarts-2.2.7/echarts.js"></script>
<script src="__PUBLIC__/test/js/map.js"></script>
<!--循环加载-->
<script type="text/javascript" src="__PUBLIC__/test/js/public.js"></script>
<script type="text/javascript" src="__PUBLIC__/test/js/ajax.js"></script>
<script type="text/javascript" src="__PUBLIC__/test/js/index.js"></script>
<script type="text/javascript">
    //数据更新--航班统计
     moredata.statis_air();
     //数据更新--高铁统计
     moredata.statis_train();

     //数据更新--航班与地图
     moredata.airmaplists();
     //数据更新--高铁与地图
     moredata.trainmaplists();

    var page = 1;
    var chagepage = setInterval(judge.refresh,10000);
       
        $('#container').css('display','none');
        $('.sy_road_list').css('display','none');
        $('.sy_road_info').css('background','#d7edb5');
        $('.sy_road_info_header').css('background','#97d334');
/* 道路合并 */
var isroad = function()
{
    var type = getCookie('changetype');
     if (type == 'road') 
     {
        setTimeout(function(){
            $('#container').css('display','block');
            $('.sy_road_list').css('display','block');
            $('.sy_road_info').css('background-color','#fffdef');
            var hgroup = document.getElementsByTagName('hgroup');
            for (var i = 0; i < hgroup.length; i++) {
                hgroup[i].classList.remove('current');
            };
            document.cookie="changetype=air";
            document.cookie="isFromSha=1";
        },5000);
       // window.clearInterval(chagepage);
     }else{
        $('#container').css('display','none');
        $('.sy_road_list').css('display','none');
        $('.sy_road_info').css('background','#d7edb5');
        $('.sy_road_info_header').css('background','#97d334');

        // if(!chagepage){
        //     var chagepage = setInterval(judge.refresh,5000);
        // }
        return false;
     }
}
/* 道路合并end */
    var roads = setInterval(isroad,5000);
</script>
    </body>
</html>

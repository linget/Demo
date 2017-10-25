/* 
 * 道路指标，拥堵信息，地图模块
 * ajax 数据处理
 * ckj-2017-10-21
 */
//道路地图
var eve_success =function(data) {
    var ajaxRoadMap = '<div id="container" class="sy_road_map_map"  style=" position: relative;" ></div>';
    ajaxRoadMap += '<script>';
    ajaxRoadMap += 'var map = new AMap.Map("container", {resizeEnable: true, center: [121.318950,31.194022], zoom: 13});';
    ajaxRoadMap += 'var trafficLayer = new AMap.TileLayer.Traffic({zIndex: 10});';
    ajaxRoadMap += 'trafficLayer.setMap(map);';
    ajaxRoadMap += 'var isVisible = true;';
    ajaxRoadMap += '<\/script>';
    $('#ajax_road_map').html(ajaxRoadMap);
    var roadMore =JSON.parse(data);
    
    //道路指标信息
    var ajaxRoadInfo = '<div class="sy_road_info"><div class="sy_road_info_header">道路</div>';
    ajaxRoadInfo += '<div class="sy_road_info_map"><div id="road_info" style="width: 100%;height:100%;text-align: center;"></div>';
    ajaxRoadInfo += ' <script type="text/javascript">';
    ajaxRoadInfo += 'var myChart = echarts.init(document.getElementById("road_info"));';
    ajaxRoadInfo += 'var option = { tooltip: { trigger: "axis", position: ["50%", "50%"]},';
    ajaxRoadInfo += 'grid: { bottom: "20", top: "19", left:"20%", right:"20%"},';
    ajaxRoadInfo += ' xAxis:  { type: "category", name:"时间", boundaryGap: false, data:'+JSON.stringify(roadMore.roadChart[0])+' },';
    ajaxRoadInfo += 'yAxis: { type: "value", name:"拥堵指标", left:"10", nameGap:"5", axisLabel: { formatter: "{value}%"} },';
    ajaxRoadInfo += ' series: [ { name:"拥堵", type:"line", itemStyle:{  normal:{label:{position: "right",  show: true }}},  data:'+JSON.stringify(roadMore.roadChart[1])+' } ]};';
    ajaxRoadInfo += ' myChart.setOption(option);  <\/script>';
    ajaxRoadInfo += '</div>';
    ajaxRoadInfo += '<div class="sy_road_info_info"><table>';
    ajaxRoadInfo += '<tr><td class="sy_td_left">虹桥商务区</td><td class="sy_td_right">'+roadMore.eve[0]+'</td></tr>';
    ajaxRoadInfo += '<tr><td class="sy_td_left">国家会展中心</td><td>'+roadMore.eve[1]+'</td></tr>';
    ajaxRoadInfo += '<tr><td class="sy_td_left">上海虹桥机场</td><td>'+roadMore.eve[2]+'</td></tr>';
    ajaxRoadInfo += '<tr><td class="sy_td_left">虹桥火车站</td><td>'+roadMore.eve[3]+'</td></tr>';
    ajaxRoadInfo += '</table></div></div>';
    $('#ajax_road_info').html(ajaxRoadInfo);

    //道路列表
    var  roadList = '<div class="sy_road_list"> <h3>道路交通状态</h3><table class="sy_road_head"> <tr> <th class="sy_th_left">道路</th><th class="sy_th_right">描述</th></tr>'; 
    roadList += '<tr><td colspan="2"><marquee direction="up" scrollamount="4"> <table>';
    for(var i in roadMore.roadList ) {
        roadList += '<tr>';
        roadList += '<td class="sy_td_left">'+roadMore.roadList[i]["road"]+'</td>';
        roadList += '<td class="sy_td_right">'+roadMore.roadList[i]["description"]+'</td>';
        roadList += '</tr>';
    }
    roadList += '</table></marquee> </td> </tr></table></div>';
    $('#ajax_road_list').html(roadList);
};
var error = function() {
    console.log('errror');
};
var params = {
    method:'POST',
    async:true,
    url:"./getRoadMore",
    data:{},
    success:eve_success,
    error:error
};
// ajaxs(params);   //请求数据



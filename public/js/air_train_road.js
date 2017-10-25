 $(document).ready(function() {
    setInterval(modulToModul,3000);
    var airTip = 1,trainTip = 0,roadTip = 0 ;
    var roadFlag = 0;  //道路标识，用于控制播放时长
    var airFlag = 1;  //航班轮播结束标识
    var trainFlag = 1; //高铁轮播结束标识

    var type = getCookie('changetype');
    var isFromSha = getCookie('isFromSha');

    function modulToModul() {
        if(airTip == 1 && airTip == 1) {
            airTip = 0;
            trainTip = 1;
            roadTip = 0;
            //顶部统计模块
            $('#air_delay_info').css('background','#fff');
            $('#train_delay_info').css('background','#d7edb5');
            //$('.sy_road_info').css('background','#d7edb5');
            //地图模块
            $('#air_delay_map').css('display','');
            $('#train_delay_map').css('display','none');
            $('#container').css('display','none');
            //列表信息
            $('#air_delay_list').css('display','');
            $('#train_delay_list').css('display','none');
            $('.sy_road_list').css('display','none');
            return true;
        }else if(trainTip == 1 && airFlag == 1) {
            airTip = 0;
            trainTip = 0;
            roadTip = 1;
            $('#air_delay_info').css('background','#d7edb5');
            $('#train_delay_info').css('background','#fff');
            //$('.sy_road_info').css('background','#d7edb5');

            $('#air_delay_map').css('display','none');
            $('#train_delay_map').css('display','');
            $('#container').css('display','none');

            $('#air_delay_list').css('display','none');
            $('#train_delay_list').css('display','');
            $('.sy_road_list').css('display','none');
            return true;
        } else if(roadTip == 1){
            if(roadFlag == 0) {
                roadFlag++;
                $('#air_delay_info').css('background','#d7edb5');
                $('#train_delay_info').css('background','#d7edb5');
               // $('.sy_road_info').css('background','#fff');

                $('#air_delay_map').css('display','none');
                $('#train_delay_map').css('display','none');
                $('#container').css('display','none');

                $('#air_delay_list').css('display','none');
                $('#train_delay_list').css('display','none');
                $('.sy_road_list').css('display','');
                ajaxs(params);
            }else if(roadFlag == 1) {
                roadFlag =0;
                airTip = 1;
                trainTip = 0;
                roadTip = 0;
            }


        }
    }
});
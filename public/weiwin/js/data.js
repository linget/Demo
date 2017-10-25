var Data = {
    airlineStatusMap: {
        0: '计划',
        1: '正在值机',
        2: '值机截止',
        3: '正在登机',
        4: '立即登机',
        5: '登机口关闭',
        6: '起飞',
        7: '到达',
        8: '取消',
        9: '延误',
        10: '返航'
    },
    cityInit: function () {
        var firstLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        var ltoi = {'A': 1, 'B': 2, 'C': 3, 'D': 4, 'E': 5, 'F': 6, 'G': 7, 'H': 8, 'I': 9, 'J': 10,
            'K': 11, 'L': 12, 'M': 13, 'N': 14, 'O': 15, 'P': 16, 'Q': 17, 'R': 18, 'S': 19, 'T': 20,
            'U': 21, 'V': 22, 'W': 23, 'X': 24, 'Y': 25, 'Z': 26};
        var cityArea = ['0', '1'];
		var cookarr=document.cookie.split(";");
        var len=cookarr.length;
        var strkind="";
         for(var i=0;i<len;i++)
         {
             if(cookarr[i].indexOf("traffic")!=-1){
                 strkind=cookarr[i];
                 break;
             }
         }
		var kind=strkind.split('=')[1];
        // 预置数据结构
        Data.cityData['inland'] = [{ character: '热门', cities:[]}];
        Data.cityData['outland'] = [{ character: '热门', cities_out:[]}];
        firstLetters.forEach(function (l) {
            var obj = {
                character: l,
                cities:[]
            };
			 var obj_out = {
                character: l,
                cities_out:[]
            };
            Data.cityData['inland'].push(obj);
            Data.cityData['outland'].push(obj_out);
			
        });
		if(kind=="train"){
			 // 填充高铁数据
           TrainList.forEach(function (item) {
            var tmpObj = {
                code: item.code,
                name: item.portName,
                str: item.searchStr
            };
            var l = item.firstLetter;
            var i = ltoi[l];

            if (+item.isHot > 0) {
                Data.cityData['inland'][0].cities.push(tmpObj);
            } else {
               Data.cityData['inland'][i].cities.push(tmpObj);
            }
        });
		}else{
			// 填充国内国外的“热门”城市列表
         AirportList.forEach(function (item) {
            var tmpObj = {
                code: item.code,
                name: item.portName,
                str: item.searchStr
            };
            var l = item.firstLetter;
            var i = ltoi[l];

            if (+item.isHot > 0) {
                if (item.cityArea == '0') {
                    Data.cityData['inland'][0].cities.push(tmpObj);
                } else {
                    Data.cityData['outland'][0].cities_out.push(tmpObj);
                }
            } else {
                if (item.cityArea == '0') {
                    Data.cityData['inland'][i].cities.push(tmpObj);
                } else {
                    Data.cityData['outland'][i].cities_out.push(tmpObj);
                }
            }
        });
		}
		   
    },
    cityData: {
    },
    festival: {
        '20150620': '端午',
    },
    source: function (key, options, callback) {
        var url = '';

        switch (key) {
        case 'flightSearch':
            url = '../flightSearch.flight';
            break;
        case 'flightDetail':
            url = '../flightDetail.flight';
            break;
        case 'attentionFlight':
            url = '../attentionFlight.flight';
            break;
        case 'addAttention': // 参数： flightID:"1" role:"0"
            url = '../addAttention.flight';
            break;
        case 'delAttention': // 参数： flightID:"1"
            url = '../delAttention.flight';
            break;
        }

        try {
            $.ajax({
                type: "GET",
                url: url,
                data: options,
                dataType: "json",
                error: function () {
                    // TODO 上线时remove调这块的假数据
                    callback && callback(Data.fake[key]);
                },
                success: function (data) {
                    callback && callback(data);
                }
            });
        } catch (e) {
            console.warn('Ajax error!');
        }
    },
    fake: {
        flightSearch: {
            body: {
                code2: '0',
                flightList:[{
                    flightID:"1",//航班id
                    airCom:"中国国航航空有限公司",      //航空公司
                    airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                    startDrome:"首都机场",  //起飞机场
                    arriveDrome:"重庆机场", //到达机场
                    startTeam:"T3",     //起飞航站楼
                    arriveTeam:"T3",        //到达航站楼
                    planStartTime:"2013-07-09 07:45",       //计划起飞时间
                    planArriveTime:"2013-07-09 10:30",      //计划到达时间
                    flightNumber: "CZ0090",     //航班号
                    isAttent:"0",    //0：不可关注   1：可关注
                    yetFocus:"0",   //0：没关注 1：已关注
                    wayStopDrome:"首都机场",    //经停机场
                    wayStopTime: "2013-07-09 10:30",    //经停时间
                    updateTime:" 2013-07-09 10:30", //更新时间
                    isDetail:"0",    //0：不提供详细信息 1：提供详细信息
                    status:"1",     //0 计划 1 正在值机 2 值机截止 3正在登机 4 立即登机 5 登机口关闭 6 起飞 7 到达 8 取消 9延误 10 返航
                }, {
                    flightID:"2",//航班id
                    airCom:"中国国航航空有限公司",      //航空公司
                    airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                    startDrome:"南苑机场",  //起飞机场
                    arriveDrome:"西安机场", //到达机场
                    startTeam:"T3", //起飞航站楼
                    arriveTeam:"T3", //到达航站楼
                    planStartTime:"2013-07-09 07:45", //计划起飞时间
                    planArriveTime:"2013-07-09 10:30", //计划到达时间
                    flightNumber: "CZ0090", //航班号
                    isAttent:"0", //0：不可关注   1：可关注
                    yetFocus:"1", //0：没关注 1：已关注
                    wayStopDrome:"首都机场",    //经停机场
                    wayStopTime: "2013-07-09 10:30",    //经停时间
                    updateTime:" 2013-07-09 10:30", //更新时间
                    isDetail:"0",    //0：不提供详细信息 1：提供详细信息
                    status:"1",     //0 计划 1 正在值机 2 值机截止 3正在登机 4 立即登机 5 登机口关闭 6 起飞 7 到达 8 取消 9延误 10 返航
                }, {
                    flightID:"3",//航班id
                    airCom:"中国南航航空有限公司",      //航空公司
                    airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                    startDrome:"首都机场",  //起飞机场
                    arriveDrome:"深圳机场", //到达机场
                    startTeam:"T3",     //起飞航站楼
                    arriveTeam:"T3",        //到达航站楼
                    planStartTime:"2013-07-09 07:45",       //计划起飞时间
                    planArriveTime:"2013-07-09 10:30",      //计划到达时间
                    flightNumber: "CZ0090",     //航班号
                    isAttent:"0",    //0：不可关注   1：可关注
                    yetFocus:"0",    //0：没关注 1：已关注
                    wayStopDrome:"首都机场",    //经停机场
                    wayStopTime: "2013-07-09 10:30",    //经停时间
                    updateTime:" 2013-07-09 10:30", //更新时间
                    isDetail:"0",    //0：不提供详细信息 1：提供详细信息
                    status:"1",     //0 计划 1 正在值机 2 值机截止 3正在登机 4 立即登机 5 登机口关闭 6 起飞 7 到达 8 取消 9延误 10 返航
                }]
            }
        },
        flightDetail: {
            body: {
                flightID:"1",//航班id
                airCom:"国航",      //航空公司
                flightNumber: "CZ0090",     //航班号
                airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                flightMode:"AK0991",     //机型
                isAttent:"0",    //0：不可关注   1：可关注
                yetFocus:"0",    //0：没关注 1：已关注
                planStartTime:"2013-07-09 07:45",       //计划起飞时间
                planArriveTime:"2013-07-09 10:30",      //计划到达时间
                predictStartTime:"2013-07-09 07:45",    //预计起飞时间
                predictArriveTime:"2013-07-09 10:30",   //预计到达时间
                startTime:"2013-07-09 07:45",   //实际起飞时间
                arriveTime:"2013-07-09 10:30",  //实际到达时间
                updateTime:" 2013-07-09 10:30", //更新时间
                startDrome:"首都机场",  //起飞机场
                arriveDrome:"重庆机场", //到达机场
                startTeam:"T3",     //起飞航站楼
                arriveTeam:"T3",        //到达航站楼
                procedure:"C10",    //值机柜台
                startRoad:"C19",    //登机口
                luggage:"D21",      //行李转盘
                arriveRoad:"B1",    //接机口
                wayStopDrome:"首都机场",    //经停机场
                wayStopTime: "2013-07-09 10:30",    //经停时间
                wayStopLongTime: "30",  //经停时长（分钟）
                status:"1",     //0立即登机1计划2登机口关闭3起飞4到达5取消6延误7正在登机
                startWeather:"1",    //起飞地天气
                arriveWeather:"2",   //到达地天气
                startTem:"26°C",     //起飞地温度
                arriveTem:"26°C",        //到达地温度
                startCityCode:"0001",    //起飞城市Code
                arriveCityCode:"0002",   //到达城市Code
                flightMessage:"航班计划于。。。",    //航班信息
                date:" 2013-07-09 10:30",   //航班时间
                eventList:[{
                    type:"登机口变更",   //事件类别
                    time:"2012-01-22 12:30", //事件发生时间
                    data:"登机口由C1变为C2" //事件发生内容
                }, {
                    type:"起飞预告",   //事件类别
                    time:"2012-01-22 13:30", //事件发生时间
                    data:"飞机即将起飞，请您抓紧时间登机！" //事件发生内容
                }]
            }
        },
        attentionFlight: {
            body1: {flightList: []},
            body: {
                code2: '0',
                flightList:[{
                    flightID:"1",//航班id
                    airCom:"中国南航航空有限公司",      //航空公司
                    airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                    startDrome:"首都机场",  //起飞机场
                    arriveDrome:"重庆机场", //到达机场
                    startTeam:"T3",     //起飞航站楼
                    arriveTeam:"T3",        //到达航站楼
                    planStartTime:"2013-07-09 07:45",       //计划起飞时间
                    planArriveTime:"2013-07-09 10:30",      //计划到达时间
                    flightNumber: "CZ0090",     //航班号
                    isAttent:"0",    //0：不可关注   1：可关注
                    yetFocus:"0",   //0：没关注 1：已关注
                    wayStopDrome:"首都机场",    //经停机场
                    wayStopTime: "2013-07-09 10:30",    //经停时间
                    updateTime:" 2013-07-09 10:30", //更新时间
                    isDetail:"0",    //0：不提供详细信息 1：提供详细信息
                    status:"1",     //0 计划 1 正在值机 2 值机截止 3正在登机 4 立即登机 5 登机口关闭 6 起飞 7 到达 8 取消 9延误 10 返航
                }, {
                    flightID:"2",//航班id
                    airCom:"中国南航航空有限公司",      //航空公司
                    airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                    startDrome:"南苑机场",  //起飞机场
                    arriveDrome:"西安机场", //到达机场
                    startTeam:"T3", //起飞航站楼
                    arriveTeam:"T3", //到达航站楼
                    planStartTime:"2013-07-09 07:45", //计划起飞时间
                    planArriveTime:"2013-07-09 10:30", //计划到达时间
                    flightNumber: "CZ0090", //航班号
                    isAttent:"0", //0：不可关注   1：可关注
                    yetFocus:"0", //0：没关注 1：已关注
                    wayStopDrome:"首都机场",    //经停机场
                    wayStopTime: "2013-07-09 10:30",    //经停时间
                    updateTime:" 2013-07-09 10:30", //更新时间
                    isDetail:"0",    //0：不提供详细信息 1：提供详细信息
                    status:"1",     //0 计划 1 正在值机 2 值机截止 3正在登机 4 立即登机 5 登机口关闭 6 起飞 7 到达 8 取消 9延误 10 返航
                }, {
                    flightID:"3",//航班id
                    airCom:"中国南航航空有限公司",      //航空公司
                    airComLogo:"../images/airline_logo.png",     //航空公司logoUrl
                    startDrome:"首都机场",  //起飞机场
                    arriveDrome:"深圳机场", //到达机场
                    startTeam:"T3",     //起飞航站楼
                    arriveTeam:"T3",        //到达航站楼
                    planStartTime:"2013-07-09 07:45",       //计划起飞时间
                    planArriveTime:"2013-07-09 10:30",      //计划到达时间
                    flightNumber: "CZ0090",     //航班号
                    isAttent:"0",    //0：不可关注   1：可关注
                    yetFocus:"0",    //0：没关注 1：已关注
                    wayStopDrome:"首都机场",    //经停机场
                    wayStopTime: "2013-07-09 10:30",    //经停时间
                    updateTime:" 2013-07-09 10:30", //更新时间
                    isDetail:"0",    //0：不提供详细信息 1：提供详细信息
                    status:"1",     //0 计划 1 正在值机 2 值机截止 3正在登机 4 立即登机 5 登机口关闭 6 起飞 7 到达 8 取消 9延误 10 返航
                }]
            },
        },
        addAttention: {
            body: {
                code2: '0',
                code: "0", //0:成功
                message: "message" //"失败原因"
            }
        },
        delAttention: {
            body: {
                code2: '0',
                code: "0", //0:成功
                message: "message" //"失败原因"
            }
        }
    }
};

/**
 * [getEcharts_pie 航班统计数据]
 * @param  {json} data 航班统计情况
 * @return {[type]}   option
 */
var getEcharts_pie = function(data){

    if (!data) 
    {
        var data = [
            {value:4, name:'实际到港'},
            {value:3, name:'实际离港'},
            {value:7, name:'延误航班'},
            {value:3, name:'取消航班'}
        ];//演示数据
    }

    var option = {
        title: {
            text: '',
            left: 'center',
            top: 20,
            textStyle: {
                color: 'black'
            }
        },

        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        series : [

            {
                name:'航班',
                type:'pie',
                radius : ['12%', '80%'],
                center: ['50%', '50%'],
                data:data.sort(function (a, b) { return a.value - b.value; }),//value排序
                roseType: 'radius',
                //饼图色彩设置
                itemStyle: {
                     normal:{
                        label:{
                        show:true,
                        formatter: '{b} \n({d}%)'
                       },
                     labelLine:{
                        show:true
                       }
                    },emphasis: {
                       shadowBlur: 10,
                       shadowOffsetX: 0,
                       shadowColor: 'rgba(0, 0, 0, 0.5)'
                   }
                },

                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                    return Math.random() * 200;
                }
            }
        ],
         color: ['rgb(51, 255, 153)','rgb(171, 217, 36)','rgb(40, 171, 227)','rgb(102, 102, 102)']
    };     
    return  option;
}

/**
 * [getEcharts_rose 高铁统计数据]
 * @param  {json} data 航班统计情况
 * 南丁格尔玫瑰图
 */
var getEcharts_rose = function(data){
    if(!data){
         var data = [
            {value:4, name:'实际到港'},
            {value:3, name:'实际离港'},
            {value:7, name:'晚点车次'},
            {value:3, name:'取消车次'}
        ];   
    }


    var option = {
    calculable : true,
    series : [
        {
            name:'半径模式',
            type:'pie',
            radius : ['12%', '80%'],
            center : ['50%', '50%'],
            roseType : 'radius',
            label: {
                normal: {
                    show: true,
                    formatter: '{b} \n({d}%)'
                },
                emphasis: {
                    show: true
                }
            },
            lableLine: {
                normal: {
                    show: true
                },
                emphasis: {
                    show: true
                }
            },
            data:data.sort(function(a,b){return a.value-b.value})
        }
    ]
};
return option;
}

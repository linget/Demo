//页面时间
    function nowtime(){
    /*
     * ev:显示时间的元素
     * type:时间显示模式.若传入12则为12小时制,不传入则为24小时制
     */
    //年月日时分秒
    var H,I;
    //月日时分秒为单位时前面补零
    function fillZero(v){
        if(v<10){v='0'+v;}
        return v;
    }
    (function(){
        var d=new Date();
        H=fillZero(d.getHours());
        I=fillZero(d.getMinutes());

        document.getElementById('time').innerHTML=H+':'+I;
        //每秒更新时间
        setTimeout(arguments.callee,60000);
    })();
}
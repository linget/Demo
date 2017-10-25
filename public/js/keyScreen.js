/* 
 * 键盘控制显示区域切换，可以多块区域，每次只显示一块
 * data 为区域id 为string，多个用“,”隔开
 */
(function ($) {
    $.fn.keyScreen = function(data){
        var $this = $(this);
        var strArr = getArr(data);
        //键盘按下切换显示屏幕
        document.addEventListener('keydown',function(e) {
             var code = e.keyCode;
                 if(code == 13 ){
                     changeScreen(strArr);
                 }
        });
        //字符串转为数组
        function getArr(str){
            var newArr = [], strArr = [];
            if(str == '' || str == null || typeof str != 'string' ){
                return false;
            }
            strArr = str.split(',');
            for(var i in strArr) {
                if($.trim(strArr[i]) != '') {
                    newArr.push($.trim(strArr[i]));
                }
            }
            return newArr ;
        }
        
        //当前显示和下个显示Id
        function currentNextId(strArr) {
            var newArr = [], count = strArr.length;
            if(count < 2) {
                newArr.push(strArr[0]);
                return newArr;
            }
            for(var i in strArr) {
                if($('#' + strArr[i]).css('display') != 'none') {
                    newArr.push(strArr[i]);
                    if(Number(i) == count-1){
                         newArr.push(strArr[0]);
                    } else {
                         newArr.push(strArr[Number(i)+1]);
                    }
                    break;
                }
            }
            if(newArr.length == 0){
                newArr.push(strArr[0]);
            }
            return newArr;
        }
        
        //切换显示屏幕
        function changeScreen(strArr) {
            var acitiveId = currentNextId(strArr);
            if(acitiveId.lenght < 2){
                $('#' + acitiveId[0]).fadeIn('slow');
                document.cookie ="screen_switch="+acitiveId[0]+"; path=/;";
            } else {
                $('#' + acitiveId[0]).fadeOut('slow');
                $('#' + acitiveId[1]).fadeIn('slow');
                document.cookie ="screen_switch="+acitiveId[1]+"; path=/;";
            }
        }
    };
})(window.jQuery);



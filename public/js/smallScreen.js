/*
 * 生产一个窗口
 * 使用选择选择器调用
 * 传入两个参数
 * data 窗口内放置的内容，可以是html代码
 * options 是窗口的样式，如果不修改默认样式，直接传入{}
 */
(function ($) {
    $.fn.extend({
        screen:function(data, options){
            var $this = $(this);
            var defaultCcss = {
                position:"absolute",
                width:"500px",
                height:"500px",
                left:"0",
                bottom:"0",
                background:"#aaa",
                opacity:"0.9",
                overflow:"hidden",
                border:"1px #efefef solid"
            };
            var opts ;
            if(isValid(options)){
                opts = $.extend({}, defaultCcss, options);
            }else{
                opts = defaultCcss;
            }
            $this.css(opts);
            if(data != null && typeof(data) == "string"){
                $this.html(data);
            }
        },
        screenMove:function(){//document.cookie =name+ "="+value+"; path=/;";
            var   $this =$(this);
            slideAuto($this);
        /*    document.addEventListener('keydown',function(e) {
                 var code = e.keyCode;
                 if(code == 65 ){
                     slideAuto($this);
                 }
                 if(code == 66) {
                     slideAuto_1($this);
                 }
                 if(code == 67) {
                     slideAuto_2($this);
                 }
                 if(code == 69) {
                      slideAuto_3($this);
                 }
            }); */
            function slideAuto($this) {
                if($this.css('animation') !='') {
                    $this.css('animation','');
                }
                if($this.css('display') == 'none') {
                    document.cookie =$this.attr('id')+ "_slide=1; path=/;";
                } else {
                    document.cookie =$this.attr('id')+ "_slide=0; path=/;";
                }
                 $this.slideToggle("slow");
            }
            
            function slideAuto_1($this) {
                var moveIn ={"-webkit-animation":"flipInX 1s .2s ease both", "-moz-animation" : "flipInX 1s .2s ease both","display":"block"};
                var moveOut ={"-webkit-animation":"fadeOutUpBig 1s .2s ease both", "-moz-animation" : "fadeOutUpBig 1s .2s ease both"};
                if($this.css('display') == 'none') {
                    document.cookie =$this.attr('id')+ "_slide=1; path=/;";
                    $this.css(moveIn);
                } else {
                    document.cookie =$this.attr('id')+ "_slide=0; path=/;";
                    $this.css(moveOut);
                    setTimeout(function(){
                        $this.css('display','none');
			},500);
                }
            }
            function slideAuto_2($this) {
                var moveIn ={"-webkit-animation":"bounceIn 1s .2s ease both", "-moz-animation" : "bounceIn 1s .2s ease both","display":"block"};
                var moveOut ={"-webkit-animation":"fadeOutLeft 1s .2s ease both", "-moz-animation" : "fadeOutLeft 1s .2s ease both"};
                if($this.css('display') == 'none') {
                    document.cookie =$this.attr('id')+ "_slide=1; path=/;";
                    $this.css(moveIn);
                } else {
                    document.cookie =$this.attr('id')+ "_slide=0; path=/;";
                    $this.css(moveOut);
                    setTimeout(function(){
                        $this.css('display','none');
			},500);
                }
            }
             function slideAuto_3($this) {
                if($this.css('animation') !='') {
                    $this.css('animation','');
                }
                if($this.css('display') == 'none') {
                    document.cookie =$this.attr('id')+ "_slide=1; path=/;";
                } else {
                    document.cookie =$this.attr('id')+ "_slide=0; path=/;";
                }
                 $this.fadeToggle("slow");
            }
        }
        
    });
     //私有方法，检测参数是否合法
  function isValid(options) {
    return !options || (options && typeof options === "object") ? true : false;
  }
})(window.jQuery);



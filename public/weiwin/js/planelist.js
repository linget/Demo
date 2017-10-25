//遮罩
$(document).on("click","#mask",function() {
          $("#mask").css("visibility","hidden");
          $("#mask_fx").css("visibility","hidden");
          $("#mask_dz").css("visibility","hidden");
          $("#message_phone").val('');
          $("#message_yzm").val('');
          $(".myui-message-tip").hide();
          $("#tips").html("");
      });
//验证数字
$(document).on("keyup","#message_yzm,#message_phone",function() {
   var reg=/^[0-9]*$/;
    flag=reg.test($(this).val());
    if(!flag){
        $(".myui-message-tip").show();
        $("#tips").html("只能为数字！");
    }else{
        $(".myui-message-tip").hide();
        $("#tips").html("");
    }  
});
 //分享
function share(val)
{
	document.cookie="planekey="+val.getAttribute("data-key")+";path=/Demo";
    $("#mask").css("visibility","visible");
    $("#mask_fx").css("visibility","visible"); 
}

//提示
function notice(val)
{
	document.cookie="planekey="+val.getAttribute("data-key")+";path=/Demo";
    $("#mask").css("visibility","visible");
    $("#mask_dz").css("visibility","visible");
}
//微信和短信切换
function qiehuan()
{
    if($("#message_wx").attr("checked")){
        $("#message_wx").attr("checked",false);
        $("#message_dx").attr("checked",true);
        $("#message_phone").val('');
        $("#message_yzm").val('');
        $("#message_msg").show();
    }else{
        $("#message_dx").attr("checked",false);
        $("#message_wx").attr("checked",true);
         $("#message_msg").hide();
    }
}
//取消按钮
function noticecancel()
{
    $("#mask").css("visibility","hidden");
    $("#mask_fx").css("visibility","hidden");
    $("#mask_dz").css("visibility","hidden");
    $("#message_phone").val('');
    $("#message_yzm").val('');
}
//计时
var countdown=60; 
function settime(val) { 
if (countdown == 0) { 
val.setAttribute("onclick","settime(this)");   
val.innerHTML="获取验证码"; 
countdown = 60;
} else { 
val.removeAttribute("onclick");  
val.innerHTML="重新发送(" + countdown + ")"; 
countdown--; 
setTimeout(function() { 
settime(val) 
},1000);
}   
}
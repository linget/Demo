//火车切换到城市
function train_city()
{
	var html="";
	html='<li class="curr item"><a href="javascript:void(0);"  data-key="inland">国内</a></li>';
	$("#in_out").html(html);
	$("#div_train_city").show();
	$("#div_train_code").hide();
	$("#div_plane_city").hide();
	$("#div_plane_code").hide();
}
//火车切换到车次
function train_code()
{
	$("#div_train_city").hide();
	$("#div_train_code").show();
	$("#div_plane_city").hide();
	$("#div_plane_code").hide(); 
}
//航班切换到城市
function plane_city()
{
	var html="";
	html='<li class="curr item"><a href="javascript:void(0);"  data-key="inland">国内</a></li>';
	html+='<li class="item"><a href="javascript:void(0);"  data-key="outland">国际</a></li>';
	$("#in_out").html(html);
	$("#div_train_city").hide();
	$("#div_train_code").hide();
	$("#div_plane_city").show();
	$("#div_plane_code").hide();
}
//航班切换到航班号
function plane_code()
{
	$("#div_train_city").hide();
	$("#div_train_code").hide();
	$("#div_plane_city").hide();
	$("#div_plane_code").show();
}
//切换火车查询
function train()
{
	var html="";
	html='<li class="curr item"><a href="javascript:void(0);"  data-key="inland">国内</a></li>';
	$("#in_out").html(html);
	$("#div_train").show();
	$("#div_plane").hide();
	$("#div_train_city").show();
	$("#div_train_code").hide();
	$("#div_plane_city").hide();
	$("#div_plane_code").hide();
}
//切换航班查询
function plane()
{
	var html="";
	html='<li class="curr item"><a href="javascript:void(0);"  data-key="inland">国内</a></li>';
	html+='<li class="item"><a href="javascript:void(0);" data-key="outland">国际</a></li>';
	$("#in_out").html(html);
	$("#div_train").hide();
	$("#div_plane").show();
	$("#div_train_city").hide();
	$("#div_train_code").hide();
	$("#div_plane_city").show();
	$("#div_plane_code").hide();
}
 //检查航班号不能为空
    function checkplaneON()
    {
        var num=$("#plane_no").val();
        if(!num){
            alert("请先输入航班号，再查询！");
            return false;
        }
    }
    //检查车次号不能为空
    function checktrainON()
    {
        var num=$("#train_no").val();
        if(!num){
            alert("请先输入车次号，再查询！");
            return false;
        }
    }
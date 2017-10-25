var j=-1;
var temp_str;
function topp(obj,div_idd,wj_ljj){
text_idd=obj.name;
window.text_id=text_idd;/////////////////////////////将文本框 id(局部变量) 定义为全局变量
window.div_id=div_idd;//////////////////////////////////将提示的 div 的id 定义为全局变量
window.wj_lj=wj_ljj;//////////////////////////////////将提示的 div 的id 定义为全局变量
}
var $=function(node){return document.getElementById(node);}//由于document.getElementById经常被使用，我们用$来简写此函数
var $$=function(node){return document.getElementsByTagName(node);}//对document.getElementsByTagName也做简写

////开始/////////////////////////////////// div 提示框位置与宽度定义（宽度与文本框相同，也可自己调整）
function div_fun(){
function getOffset(obj){  
  var offsetleft = obj.offsetLeft;  
  var offsettop = obj.offsetTop;  
  while (obj.offsetParent != document.body)  
  {  
  obj = obj.offsetParent;  
  offsetleft += obj.offsetLeft;  
  offsettop += obj.offsetTop;  
  }  
  return {Left : offsetleft, Top : offsettop};  
}  
var o=$(text_id);
var b=$(div_id);
var l=getOffset(o).Left;
var t=getOffset(o).Top;
var w=o.offsetWidth;
var h=o.offsetHeight;
var i=t+h-1;
var l2=l+11;
//alert ("w="+w+"L="+l+"t="+t);
b.style.left=l2+"px";//提示框左对齐于文本框
b.style.top=i+"px";////提示框与文本框下框对接
b.style.width=w+"px";
b.style.height=0+"px";
}
////结束/////////////////////////////////// div 提示框位置与宽度定义（宽度与文本框相同，也可自己调整）

//、、、、开始、、、、////////////////异步读取数据库数据，
function ajax_keyword(){
if(document.getElementById(text_id).value!=''){
div_fun();
	var xmlhttp;
	try{
		xmlhttp=new XMLHttpRequest();
		}
	catch(e){
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	xmlhttp.onreadystatechange=function(){
	if (xmlhttp.readyState==4){
		if (xmlhttp.status==200){
			var data=xmlhttp.responseText;
			$(div_id).innerHTML=unescape(data);//对结果进行unescape解码以防止中文乱码
			j=-1;
			}
		}
	}
xmlhttp.open("post", wj_lj+"?text_name="+escape(document.getElementById(text_id).value)+"&div_id="+div_id+"&QiTaBianLiang=其它变量",true);
	xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	xmlhttp.send("keyw222="+escape($(text_id).value));//在IE6下，此行基本无用/*		 xmlhttp.open("post","ajax_result.php?keyword="+escape(document.getElementById("text_id").value),true);*/
}
}
//、、、、结束、、、、////////////异步读取数据库数据，
//、、开始、、、、处理键盘按键动作
function keyupdeal(e){
	var keyc;
	if(window.event){
		keyc=e.keyCode;
	}
	else if(e.which){
		keyc=e.which;
	}
	if(keyc!=40 && keyc!=38){
		ajax_keyword();
		temp_str=$(text_id).value;
}}
//、、结束、、处理键盘按键动作
///开始////////设置 li 样式表，高亮显示
function set_style(num){
/* var li_value=$$("li")[j].childNodes[0].nodeValue;
 if(li_value=="【关闭】")$(text_id).value='';else $(text_id).value=li_value;//数据显示在文本框里*/
 for(var i=0;i<$$("li").length;i++){
		var li_node=$$("li")[i];
		li_node.className="";
	}
	if(j>=0 && j<$$("li").length){
		var i_node=$$("li")[j];
		$$("li")[j].className="select_li";
	}
}
///结束////////设置 li 样式表，高亮显示


//开始、、、、、、、、、、、、、、、与查询数据对应的其它数据返回函数
function re_val_fun(hid_val){
var re_js_array=hid_val.split(",");//将英文逗号隔点的字符串打散成数组
$("mc").value=re_js_array[0]?re_js_array[0]:'';////////////////名称 文本框
$("lx").value=re_js_array[1]?re_js_array[1]:'';////////////////类型 文本框
$("bs").value=re_js_array[2]?re_js_array[2]:'';///////////////备注  文本框
$("bz").value=re_js_array[3]?re_js_array[3]:'';//////////标识   Lable标签
$("time").innerText=re_js_array[4]?re_js_array[4]:'';//////////标识   Lable标签
$("id").innerText=re_js_array[5]?re_js_array[5]:'';//////////标识   Lable标签
//$("qt").innerText=re_js_array[6]?re_js_array[6]:'';/////////
}//$mc.",".$lx.",".$bs.",".$bz.",".$time."".$id;
//结束、、、、、、、、、、、、、、、与查询数据对应的其它数据返回函数


//开始//////////鼠标或键盘选定 li 时，高亮显示当前行
function mo(nodevalue,hid_val){
	j=nodevalue;
	set_style(j);///////////////////// li 样式函数
       var li_value=$$("li")[j].childNodes[0].nodeValue;
       if(li_value=="【关闭】"){$(text_id).value='';}
	   re_val_fun(hid_val);
}
function li_fun(obj,hid_val){/////////////////////////////////////////////////////鼠标点击数据此数据将显示在文本框里并关闭提示框 
       var li_value=$$("li")[j].childNodes[0].nodeValue;
       if(li_value=="【关闭】"){$(text_id).value=''; }else{$(text_id).value=li_value; }
	   re_val_fun(hid_val);/////////数据显示在文本框里
	   hide_suggest();/////////////////////////////////////////////////关闭提示 
}
//鼠标点击 li 里的数据时提交表单动作
/*function form_submit(){
	if(j>=0 && j<$$("li").length){
		$$("input")[0].value=$$("li")[j].childNodes[0].nodeValue;
	}
	document.search.submit();
}*/
//鼠标点击 li 里的数据时提交表单动作

////////////////////////////////隐藏提示框
function hide_suggest(){
	var nodes=document.body.childNodes
	for(var i=0;i<nodes.length;i++){
		if(nodes[i]!=$(text_id)){
			$(div_id).innerHTML="";
			}
		}
	}
////////////////////////////////隐藏提示框

////开始/////////////////////////键盘方向键操作
function keydowndeal(e){
	var keyc;
	if(window.event)keyc=e.keyCode;
	else if(e.which)keyc=e.which;
if(keyc==40 || keyc==38){
	                       if(keyc==40){if(j<$$("li").length)j++;else j=-1;}////键盘方向键向下
	                       if(keyc==38){if(j>=0)j--;else j=$$("li").length-1;}//键盘方向键向上
                           set_style(j);
var li_value=$$("li")[j].childNodes[0].nodeValue;
if(li_value=="【关闭】"){$(text_id).value=''; re_val_fun();}else{ $(text_id).value=li_value;re_val_fun($("hid_val"+j).value);}
          }
if(keyc==13){
	   hide_suggest();/////////////////////////////////////////////////关闭提示 
       var li_value=$$("li")[j].childNodes[0].nodeValue;
       if(li_value=="【关闭】"){$(text_id).value=''; re_val_fun();}else{ $(text_id).value=li_value;re_val_fun($("hid_val"+j).value);}
}}
////结束///////////////////////键盘方向键操作
function document.onclick() //任意点击时关闭该控件 //ie6的情况可以由下面的切换焦点处理代替
{ 
 with(window.event)
 {
  hide_suggest();
 }
}
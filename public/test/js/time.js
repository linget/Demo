/**
 * [settime 时间校准]
 * @author  zjl-2017-10-21
 */
function settime(){
	 	 var date = new Date();
		 var hours = date.getHours();
		 var minue = date.getMinutes();
		 if(hours<10){
		 	hours = "0"+hours;
		 }
		 if(minue<10){
		 	minue = "0"+minue;
		 }
		 var time = hours+':'+minue;
		 $('.weath').text(time);
}
<?php
 $ch=curl_init();
 curl_setopt($ch,CURLOPT_URL,"http://zhihuijingang.com/yz/public/index.php/Message/Infomation/changeData");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$res=curl_exec($ch);
curl_close($ch);
echo $res;
?>
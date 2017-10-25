<?php
namespace app\index\service;
use think\Db;
/**
	航班信息接口
*/
class Air 
{
	/**
     * 查询当前1小时之后4小时出发航班(从上海虹桥出发)
     * @param isFromSha 1/2 从上海出发/到达上海
     * @param int start,end 查询数据范围()
     * @param string flightNo 航班号
     * @param string startstation 起飞站
     * @param string terninalstation 落地站
     * @param string operationdate 运行日
     * @param date   starttime 起飞时间
     * @param date   endtime  落地时间
     * @param int   type  语言类型
     * 
     */
	public function AirinfosAll($start,$end,$isFromSha,$type)
	{
		$IndexService = new \app\index\service\IndexService();
		$time = $IndexService->get_time();
    
		$sql = "SELECT * FROM
			(SELECT F.FLIGHT_NO,F.ORIGIN_AIRPORT_IATA,F.DEST_AIRPORT_IATA,F.REMARK,
				F.CODE_SHARE1,F.CODE_SHARE2,F.CODE_SHARE3,F.CODE_SHARE4,F.CODE_SHARE5,F.CODE_SHARE6,
				(SELECT NAME_XML FROM SHUNIU.F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.ORIGIN_AIRPORT_IATA) FROMCITY,
				(SELECT NAME_XML FROM SHUNIU.F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.DEST_AIRPORT_IATA) TOCITY,
				(SELECT NAME_XML FROM SHUNIU.F_3_REMARK FR WHERE FR.REMARK_CODE = F.REMARK) REMARK_XML,
				F.OPERATION_DATE,TO_CHAR(NVL(F.ATD,NVL(F.ETD,F.STD)),'HH24:MI') STARTTIME,
				TO_CHAR(NVL(F.ATA,NVL(F.ETA,F.STA)),'HH24:MI') ENDTIME,
			ROWNUM RN FROM
				(SELECT * FROM SHUNIU.F_1_DYNFLIGHT  F1 WHERE 
				TO_CHAR(OPERATION_DATE,'yyyy-mm-dd')='".date( "Y-m-d" )."' ";
		if($isFromSha == '1')
		{
			//上海虹桥出发
			$sql .="AND ORIGIN_AIRPORT_IATA='SHA' ";
		}else
		{
			//到达上海虹桥
			$sql .="AND DEST_AIRPORT_IATA='SHA' ";
		}	
			$sql .="AND REMARK != 'CSF' 
				AND REMARK != 'DEP' 
				AND TO_CHAR(NVL(ATD,NVL(ETD,STD)),'HH24:MI') between '".$time['now_time']."' and '".$time['end_time']."'
				ORDER By TO_CHAR(NVL(ATD,NVL(ETD,STD)),'HH24:MI') ASC
				)F
			WHERE ROWNUM <= '".$end."') FR
			WHERE RN > '".$start."'
			ORDER BY STARTTIME ASC";
	
		$res = Db::query($sql);	
		$result = handle_result($res,$type);

		return $result;
	}

	/**
     * 条件查询航班
     * @param int start,end 查询数据区间(范围)
     * @param string flightNo 航班号
     * @param string startstation 起飞地点
     * @param string terninalstation 降落地点
     * @param string date 运行日
     * @return array()
     */
	static public function getAirSearch($flightNo, $startstation,$terninalstation,$date,$start,$end,$type)
	{
		$sql = "SELECT * FROM
		(SELECT F.FLIGHT_NO,F.ORIGIN_AIRPORT_IATA,F.DEST_AIRPORT_IATA,F.REMARK,
				F.CODE_SHARE1,F.CODE_SHARE2,F.CODE_SHARE3,F.CODE_SHARE4,F.CODE_SHARE5,F.CODE_SHARE6,	
				(SELECT NAME_XML FROM SHUNIU.F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.ORIGIN_AIRPORT_IATA) FROMCITY,
				(SELECT NAME_XML FROM SHUNIU.F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.DEST_AIRPORT_IATA) TOCITY,
				(SELECT NAME_XML FROM SHUNIU.F_3_REMARK FR WHERE FR.REMARK_CODE = F.REMARK) REMARK_XML,
				F.OPERATION_DATE,TO_CHAR(NVL(F.ATD,NVL(F.ETD,F.STD)),'HH24:MI') STARTTIME,
				TO_CHAR(NVL(F.ATA,NVL(F.ETA,F.STA)),'HH24:MI') ENDTIME, ROWNUM RN
				FROM 
				(SELECT * FROM SHUNIU.F_1_DYNFLIGHT WHERE REMARK != 'CSF' AND REMARK != 'DEP' ";

		if(!empty($flightNo))
		{
			$flightNo = strtoupper($flightNo);
			$sql.="AND (FLIGHT_NO ='".$flightNo."' 
				   OR CODE_SHARE1 ='".$flightNo."' 
				   OR CODE_SHARE2 ='".$flightNo."' 
				   OR CODE_SHARE3 ='".$flightNo."' 
				   OR CODE_SHARE4 ='".$flightNo."' 
				   OR CODE_SHARE5 ='".$flightNo."' 
				   OR CODE_SHARE6 ='".$flightNo."' )";
		}
		
		if(!empty($startstation)&&!empty($terninalstation)&&($startstation == 'SHA'||$terninalstation == 'SHA'))
		{
			$sql .= " AND ORIGIN_AIRPORT_IATA='".$startstation."' AND DEST_AIRPORT_IATA='".$terninalstation."'";
		}

		if(!empty($date))
		{
			$sql .= "AND TO_CHAR(OPERATION_DATE,'yyyy-mm-dd')='".$date."' ORDER BY TO_CHAR(NVL(ATD,NVL(ETD,STD)),'HH24:MI') ASC) F";
		}
		$sql .= " WHERE ROWNUM <= '".$end."' ORDER BY STARTTIME ASC) WHERE RN > '".$start."' ";
	
		$res = Db::query($sql);
		$result = handle_result($res,$type);
		return $result;
	}
        /*
         * 访问云端Mysql 获取航班信息
         */
        public function getFlightList($fromCity=null,$toCity=null,$flightNo=null,$fDate=null){
        $sql = "SELECT * FROM (SELECT F.FLIGHT_NO,F.CODE_SHARE1,F.CODE_SHARE2,F.CODE_SHARE3,F.CODE_SHARE4,F.CODE_SHARE5,F.CODE_SHARE6,F.ORIGIN_AIRPORT_IATA,(SELECT NAME_XML FROM F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.ORIGIN_AIRPORT_IATA) FROMCITY,";
        $sql .= "(SELECT NAME_XML FROM F_3_AIRLINE F4 WHERE F4.AIRLINE_IATA = F.AIRLINE_IATA) AIRLINE_IATA,";
        $sql .="date_format(F.ATD,'%H:%i') ATD,date_format(F.STD,'%H:%i') STD,date_format(F.STA,'%H:%i') STA,date_format(F.ATA,'%H:%i') ATA,date_format(F.ETA,'%H:%i') ETA,date_format(F.ETD,'%H:%i') ETD,";
        $sql .= "date_format(IFNULL(F.ATD,IFNULL(F.ETD,F.STD)),'%H:%i') STARTTIME,F.DEST_AIRPORT_IATA,(SELECT NAME_XML FROM F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.DEST_AIRPORT_IATA) TOCITY,";
        $sql .= "date_format(IFNULL(F.ATA,IFNULL(F.ETA,F.STA)),'%H:%i') ENDTIME,F.RECENT_ABNORMAL_STATUS,F.REMARK,(SELECT NAME_XML FROM F_3_REMARK FR ";
        $sql .= "WHERE FR.REMARK_CODE = REMARK) REMARK_XML FROM  (SELECT * FROM F_1_DYNFLIGHT F1";
        $sql .= " WHERE OPERATION_DATE='".$fDate."' ";
        if(!is_null($flightNo) && "" != trim($flightNo) && $flightNo != "null")
        {
            $sql .= " AND (FLIGHT_NO = '".strtoupper(trim($flightNo))."'
            OR CODE_SHARE1 = '".strtoupper(trim($flightNo))."'
            OR CODE_SHARE2 = '".strtoupper(trim($flightNo))."'
            OR CODE_SHARE3 = '".strtoupper(trim($flightNo))."'
            OR CODE_SHARE4 = '".strtoupper(trim($flightNo))."'
            OR CODE_SHARE5 = '".strtoupper(trim($flightNo))."'
            OR CODE_SHARE6 = '".strtoupper(trim($flightNo))."') AND REMARK != 'CSF' ";
        }
        $sql .= " ORDER By IFNULL(F1.ATD,IFNULL(F1.ETD,F1.STD)) ASC) F ";
        $sql .= " ORDER BY STARTTIME ASC ) FD";
        if(!is_null($fromCity) && !is_null($toCity) && $fromCity != "null" && $toCity != "null")
        {
            $sql .= " WHERE FROMCITY like '%".$fromCity."%' AND REMARK != 'CSF' AND REMARK!='null' AND TOCITY like '%".$toCity."%'";
        }
        if(!is_null($flightNo) && "" != trim($flightNo) && $flightNo != "null")
           {
               $sql .="LIMIT 1";
           }
        $res =db('','db_config2')->query($sql);
        return $res;
    }

	
}


?>
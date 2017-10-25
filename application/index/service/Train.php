<?php
namespace app\index\service;

use think\Db;
/**
	列车信息server												
*/
class Air
{	 

	

	/**
     * 查询1小时前4小时后出发列车(从上海虹桥出发)
     * @param isFromSha 1/2 从上海出发/到达上海
     * @param int start,end 查询数据范围()
     * @param string TRAIN_NO 车次
     * @param string FROMCITY 始发地
     * @param string TOCITY 目的地
     * @param date   starttime 出发时间
     * @param date   endtime  到达时间
     * @param date  AbnormalTime 早晚点时差
     * @param int   REMARK  状态
     * 
     */
	static public function TraininfosAll($start,$end,$isFromSha,$language){

		$IndexService = new \app\index\service\IndexService();
		$time = $IndexService->get_time();

        $sql = "SELECT * FROM
        		(SELECT TRAIN_NO,";
			if($isFromSha == '1'){ 
				$sql .="DEPART_TRAIN_NO FLIGHT_NO,";
			}else{
				$sql .= "ARRIVE_TRAIN_NO FLIGHT_NO,";
			}	
	        	$sql .="AbnormalStatus REMARK,AbnormalTime,
						(SELECT STA_NAME FROM SHUNIU.R_STA RS WHERE STA = StartStation) FROMCITY,
						(SELECT STA_NAME FROM SHUNIU.R_STA RS WHERE STA = TerminalStation) TOCITY, 
						TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') STARTTIME,
						TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI') ENDTIME,ROWNUM RN
						from 
						(SELECT * FROM SHUNIU.R_RDT WHERE 
						";

			if ($isFromSha == '1') {
				//上海出发
				$sql .="TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'yyyy-mm-dd')='".date( "Y-m-d" )."'
					AND StartStation = 'AOH' 
					AND TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') 
					between '".$time['now_time']."' and '".$time['end_time']."'
					ORDER BY TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') ASC)";
			}else{
				$sql .="
					TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'yyyy-mm-dd')='".date( "Y-m-d" )."'
					AND TerminalStation = 'AOH' 
					AND TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI')
					between '".$time['now_time']."' and '".$time['end_time']."'
					ORDER BY TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI') ASC)";
			}
			$sql .= "
					WHERE ROWNUM <= '".$end."'
					ORDER BY STARTTIME ASC) 
					WHERE RN >'".$start."' ";

//file_put_contents('./logs.txt',date('Y-m-d H:i:s',time()).'sql:'.$sql);
		$res = Db::query($sql);

		$result = handle_train_result($res,$language);
		
		return $result;
	}

	/**
	 * 列车查询(默认查询从上海虹桥出发)
     * @param int start,end 查询数据范围()
	 * @param string trainno 车次
     * @param string startstation 始发地
     * @param string terninalstation 目的地
     * @param string date 时间
	 *
	*/
	public function getTrainSearch($trainno,$startstation,$terninalstation,$date,$start,$end,$language)
	{

		if($startstation == '' && $terninalstation =='' && $trainno==''){
			return false;
		}

		if($startstation == '' && $terninalstation !='AOH' && $trainno ==''){
			$startstation ='AOH';
		}

		if ($terninalstation == '' && $startstation !='AOH' && $trainno ==''){
			$terninalstation = 'AOH';
		}
		


		//车次
		if(!empty($trainno) && $trainno != 'null') {
			// 有列车号
				$sql = "SELECT * FROM
					(SELECT TRAIN_NO,AbnormalStatus REMARK,AbnormalTime,";
				
					if($startstation == 'AOH'){ 
						$sql .="DEPART_TRAIN_NO FLIGHT_NO,";
					}else{
						$sql .= "ARRIVE_TRAIN_NO FLIGHT_NO,";
					}		
							$sql .= "(SELECT STA_NAME FROM SHUNIU.R_STA RS WHERE STA = STARTSTATION) FROMCITY,
								(SELECT STA_NAME FROM SHUNIU.R_STA RS WHERE STA = TERMINALSTATION) TOCITY, 
								TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') STARTTIME,
								TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI') ENDTIME,
								ROWNUM RN FROM 
								(SELECT * FROM SHUNIU.R_RDT WHERE 1=1 ";
				


				if (!empty($startstation) && !empty($terninalstation) && $startstation != 'null' && $terninalstation !='null') 
				{
					$sql .="STARTSTATION = '".$startstation."' AND TERMINALSTATION = '".$terninalstation."'";
				}
				elseif (empty($startstation) && !empty($terninalstation) && $terninalstation !='null'){
					$sql .="TERMINALSTATION = '".$terninalstation."'";
				}
				elseif(!empty($startstation) && empty($terninalstation) && $startstation != 'null'){
					$sql .="STARTSTATION = '".$startstation."'";
				}
				$sql .="AND DEPART_TRAIN_NO = '".strtoupper($trainno)."'
						AND TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'yyyy-mm-dd')='".$date."'
						ORDER BY TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') ASC";
				$sql .=") WHERE ROWNUM <='".$end."'
				ORDER BY STARTTIME ASC)
				WHERE RN >'".$start."'";

				$res = Db::query($sql);	
		}else{
			// 无列车号
			
			$res = self::Train_Search($startstation,$terninalstation,$start,$end,$date);
		}
		
				

			
			
        

		
		$result = handle_train_result($res,$language);
		
		return $result;
	}

	//查询列车地址
	public static function Train_Search($startstation,$terninalstation,$start,$end,$date){
		//出发	
		if (strtoupper($startstation) == 'AOH') {
			$ter = Db::query("SELECT STA_NAME FROM " . config("prefix") . "R_CITY_STA WHERE STA_CODE = '$terninalstation' ");
            $sql_ter = "SELECT STA FROM " . config("prefix") . "R_STA WHERE STA_NAME LIKE '%" . $ter[0]['STA_NAME'] . "%' ";
            $ter_code = Db::query($sql_ter);
			$count = count($ter_code);

			for ($k = 0; $k < $count; $k++) {
                    $ter_codes = $ter_code[$k]['STA'];
                    $sql = "SELECT * FROM (SELECT TRAIN_NO ,DEPART_TRAIN_NO FLIGHT_NO,
                    TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') STARTTIME, 
                    TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI') ENDTIME,";
                    $sql .= " (SELECT STA_NAME FROM " . config("prefix") . "R_STA WHERE STA = StartStation) FROMCITY,(SELECT STA_NAME FROM " . config("prefix") . "R_STA WHERE STA = TerminalStation) TOCITY,
     AbnormalStatus REMARK,AbnormalTime,ROWNUM RN ";
                    $sql .= "FROM (SELECT * FROM " . config("prefix") . "R_RDT ";
                    $sql .= " WHERE  1 = 1 AND TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'yyyy-mm-dd')='".$date."'";
                    if (!empty($startstation) && !empty($terninalstation) && $startstation != 'null' && $terninalstation !='null') 
					{
						$sql .="AND STARTSTATION = '".$startstation."' AND TERMINALSTATION = '".$ter_codes."'";
					}
					elseif (empty($startstation) && !empty($terninalstation) && $terninalstation !='null'){
						$sql .="AND TERMINALSTATION = '".$ter_codes."'";
					}
					elseif(!empty($startstation) && empty($terninalstation) && $startstation != 'null'){
						$sql .="AND STARTSTATION = '".$startstation."'";
					}
                    
                    $sql .= " ORDER BY TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') ASC) WHERE  ROWNUM < $end ) R WHERE RN > $start";
                    $result[$k] = Db::query($sql);
                }

		}else{

			//到达
			$sta = Db::query("SELECT STA_NAME FROM " . config("prefix") . "R_CITY_STA WHERE STA_CODE = '$startstation' ");
            

            $sql_sta = "SELECT STA_CODE FROM " . config("prefix") . "R_CITY_STA WHERE STA_NAME LIKE '" . $sta[0]['STA_NAME'] . "%' ";
          
            $sta_code =Db::query($sql_sta);
            $count = count($sta_code);
            for ($k = 0; $k < $count; $k++) {
                    $sta_codes = $sta_code[$k]['STA_CODE'];
                    $sql = "SELECT * FROM (SELECT TRAIN_NO ,DEPART_TRAIN_NO FLIGHT_NO,
                    TO_CHAR(NVL(DEPART_TIME,ESTIMATED_DEPART_TIME),'HH24:MI') STARTTIME, 
                    TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI') ENDTIME,";
                    $sql .= " (SELECT STA_NAME FROM " . config("prefix") . "R_STA WHERE STA = StartStation) FROMCITY,(SELECT STA_NAME FROM " . config("prefix") . "R_STA WHERE STA = TerminalStation) TOCITY,
     AbnormalStatus REMARK,AbnormalTime,ROWNUM RN ";
                    $sql .= "FROM (SELECT * FROM " . config("prefix") . "R_RDT ";
                    $sql .= " WHERE  1 = 1 AND TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'yyyy-mm-dd')='".$date."'";
                     if (!empty($startstation) && !empty($terninalstation) && $startstation != 'null' && $terninalstation !='null') 
					{
						$sql .="AND STARTSTATION = '".$sta_codes."' AND TERMINALSTATION = '".$terninalstation."'";
					}
					elseif (empty($startstation) && !empty($terninalstation) && $terninalstation !='null'){
						$sql .="AND TERMINALSTATION = '".$terninalstation."'";
					}
					elseif(!empty($startstation) && empty($terninalstation) && $startstation != 'null'){
						$sql .="AND STARTSTATION = '".$sta_codes."'";
					}
                    
                    $sql .= " ORDER BY TO_CHAR(NVL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'HH24:MI') ASC) WHERE  ROWNUM < $end ) R WHERE RN > $start";
            	
             		

                    $result[$k] = Db::query($sql);
                    
                }
          	
      
		}
			$items = array();
            foreach ($result as $v) 
            {
                    foreach ($v as $vv) {
                        $items[] = $vv;
                    }
            }

        return $items;

	}
        /*
         * Mysql库查询高铁信息，根据车次或者城市查询
         */
        public function getTrainList($fromCity=null,$toCity=null,$trainNo=null,$tDate=null){
        $sql = "SELECT * FROM (SELECT RR.RUN_DATE,RR.StartStation,RR.DEPART_TRAIN_NO TRAIN_ID,date_format(RR.DEPART_TIME,'%H:%i') DEPART_TIME,date_format(RR.ESTIMATED_DEPART_TIME,'%H:%i') ESTIMATED_DEPART_TIME,date_format(RR.ARRIVE_TIME,'%H:%i') ARRIVE_TIME,date_format(RR.ESTIMATED_ARRIVE_TIME,'%H:%i') ESTIMATED_ARRIVE_TIME,date_format(IFNULL(DEPART_TIME,ESTIMATED_DEPART_TIME),'%H:%i') STARTTIME, date_format(IFNULL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'%H:%i') ENDTIME,";
        $sql .= " (SELECT STA_NAME FROM R_STA WHERE STA = RR.StartStation) FROMCITY,(SELECT STA_NAME FROM R_STA WHERE STA = RR.TerminalStation) TOCITY,
            ABNORMALSTATUS REMARK,ABNORMALTIME ";
        $sql .= "FROM (SELECT * FROM R_RDT ";
        $sql .= " WHERE RUN_DATE ='".$tDate."'";
        if (!empty($trainNo)) {
            $sql .= " AND ARRIVE_TRAIN_NO = '" . strtoupper(trim($trainNo)) . "'";
        }
        if(strcmp($fromCity,"上海虹桥")==0){
            $sql .= " ORDER BY date_format(IFNULL(DEPART_TIME,ESTIMATED_DEPART_TIME),'%H:%i') ASC) RR) R";
        }else{
            $sql .= " ORDER BY date_format(IFNULL(ARRIVE_TIME,ESTIMATED_ARRIVE_TIME),'%H:%i') ASC) RR) R";
        }
        if (!empty($fromCity) && !empty($toCity) && $fromCity != "null" && $toCity != "null") {
            $sql .= " WHERE FROMCITY like '%$fromCity%' AND TOCITY like '%$toCity%'";
        } 
        $res = db('','db_config2') -> query($sql); 
        return $res;
    }
    public function getTrain($fromCity=null,$toCity=null,$trainNo=null,$tDate=null)
    {
        if($fromCity==null&&$toCity==null&&$trainNo==null){
            return "";
        }
        $sql="SELECT RR.RUN_DATE,RR.StartStation,RR.DEPART_TRAIN_NO TRAIN_ID,";
        $sql.="date_format(RR.ESTIMATED_DEPART_TIME,'%H:%i') ESTIMATED_DEPART_TIME,";
        $sql.="date_format(RR.ESTIMATED_ARRIVE_TIME,'%H:%i') ESTIMATED_ARRIVE_TIME,";
        if($fromCity =="上海虹桥"){
            $sql .="date_format(RR.DEPART_TIME, '%H:%i') DEPART_TIME,";
            $sql.="C.ARRIVE_TIME,";
            $sql.="IFNULL(date_format(IFNULL(RR.DEPART_TIME,RR.ESTIMATED_DEPART_TIME),'%H:%i'),C.DEPART_TIME) STARTTIME,";
            $sql.="C.ARRIVE_TIME ENDTIME,";
        }
        if($toCity=="上海虹桥"){
            $sql.="C.DEPART_TIME,";
            $sql.="date_format(RR.ARRIVE_TIME,'%H:%i') ARRIVE_TIME,";
            $sql.="C.DEPART_TIME STARTTIME,";
            $sql.="IFNULL(date_format(IFNULL(RR.ARRIVE_TIME,RR.ESTIMATED_ARRIVE_TIME),'%H:%i'),C.ARRIVE_TIME) ENDTIME,";
        }
        if($fromCity !="上海虹桥" && $toCity!="上海虹桥"){
            $sql.="C.DEPART_TIME,";
            $sql.="C.ARRIVE_TIME,";
            $sql.="C.DEPART_TIME STARTTIME,";
            $sql.="C.ARRIVE_TIME ENDTIME,";
        }
        if($fromCity==null&&$toCity==null&&$trainNo!=null){
            $sql.="C.DEPART_TIME,";
            $sql.="C.ARRIVE_TIME,";
            $sql.="C.DEPART_TIME STARTTIME,";
            $sql.="C.ARRIVE_TIME ENDTIME,";
        }
        $sql.="C.FROMCITY,C.TOCITY,RR.ABNORMALSTATUS REMARK,RR.ABNORMALTIME";
        $sql.=" FROM R_RDT RR JOIN (";
        if($fromCity!=null&&$toCity!=null&&$trainNo==null){
            $sql.="SELECT a.STOP_STA FROMCITY,b.STOP_STA TOCITY,a.DEPART_TRAIN_NO,date_format(a.PLAN_DEPARTTIME, '%H:%i') DEPART_TIME,date_format(b.PLAN_ARRIVETIME, '%H:%i') ARRIVE_TIME";
            $sql.=" FROM R_STOP_TIME a JOIN R_STOP_TIME b ON a.DEPART_TRAIN_NO = b.DEPART_TRAIN_NO";
            $sql.=" WHERE a.STOP_STA LIKE '%$fromCity%' AND b.STOP_STA LIKE '%$toCity%' AND a.PLAN_ARRIVETIME < b.PLAN_ARRIVETIME";
        }
        if($fromCity==null&&$toCity==null&&$trainNo!=null){
            $sql.=" SELECT a.STOP_STA FROMCITY,b.STOP_STA TOCITY,a.DEPART_TRAIN_NO,date_format(a.PLAN_DEPARTTIME, '%H:%i') DEPART_TIME,date_format(b.PLAN_ARRIVETIME, '%H:%i') ARRIVE_TIME";
            $sql.=" FROM R_STOP_TIME a JOIN R_STOP_TIME b ON a.DEPART_TRAIN_NO = b.DEPART_TRAIN_NO";
            $sql.=" WHERE a.DEPART_TRAIN_NO='$trainNo' AND  a.PLAN_ARRIVETIME = '1900-01-01 00:00:00' AND b.PLAN_DEPARTTIME = '1900-01-01 00:00:00' LIMIT 1";
        }
        if($fromCity!=null&&$toCity!=null&&$trainNo!=null){
             $sql.="SELECT a.STOP_STA FROMCITY,b.STOP_STA TOCITY,a.DEPART_TRAIN_NO,date_format(a.PLAN_DEPARTTIME, '%H:%i') DEPART_TIME,date_format(b.PLAN_ARRIVETIME, '%H:%i') ARRIVE_TIME";
             $sql.=" FROM R_STOP_TIME a JOIN R_STOP_TIME b ON a.DEPART_TRAIN_NO = b.DEPART_TRAIN_NO";
            $sql.=" WHERE a.DEPART_TRAIN_NO='$trainNo' AND a.STOP_STA LIKE '%$fromCity%' AND b.STOP_STA LIKE '%$toCity%' AND a.PLAN_ARRIVETIME < b.PLAN_ARRIVETIME";
        }
        $sql.=") C ON RR.DEPART_TRAIN_NO = C.DEPART_TRAIN_NO";
        $sql.=" WHERE RR.RUN_DATE ='$tDate'";
         if($fromCity!=null&&$toCity!=null&&$trainNo==null){
             $sql.="ORDER BY C.DEPART_TIME ASC";
         }
        $res = db('','db_config2') -> query($sql);
        return $res;
    }

}
?>
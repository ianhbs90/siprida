<?php
	

	function get_distanceWeight($dao,$distance){

		if($distance==0 or is_null($distance))
		{
			return 0;
		}

		if($distance<=4000){
			$sql = "SELECT bobot FROM pengaturan_bobot_jarak WHERE thn_pelajaran='2018/2019' 
					AND (jarak_min<='".$distance."' AND jarak_max>='".$distance."')";			

			$distance_weight_row = $dao->execute(0,$sql)->row_array();
			return (!is_null($distance_weight_row['bobot'])?$distance_weight_row['bobot']:0);
		}else{
			return 10;
		}
	}

	function get_achievementWeight($dao,$level,$rate){
		

		$sql = "SELECT bobot_juara1,bobot_juara2,bobot_juara3 FROM pengaturan_bobot_prestasi WHERE thn_pelajaran='2018/2019'
				AND tkt_kejuaraan_id='".$level."'";
				
		$row = $dao->execute(0,$sql)->row_array();
		switch($rate){
			case 1:
				$weight = $row['bobot_juara1'];break;
			case 2:
				$weight = $row['bobot_juara2'];break;
			case 3:
				$weight = $row['bobot_juara3'];break;
			default:$weight = 0;
		}
		return $weight;
	}

	function generate_score($dao,$stage,$path,$distance,$ac_value,$levAchievement,$ratAchievement,$mode){

		if($path=='2'){
			$score = ($stage=='1'?$distance:$ac_value);
		}
		else if($path=='3'){
			$add1 = (strtolower($mode)=='unbk'?$ac_value*0.2:0);
			$add2 = ($stage=='1'?get_distanceWeight($dao,$distance):0);
			$score = $ac_value+$add1+$add2;
		}else if($path=='4'){
			$score = get_achievementWeight($dao,$levAchievement,$ratAchievement);
		}else{
			$score = $ac_value;
		}
		return $score;
	}
?>
<?php
	class settlement{

		private $_ci;
		function __construct(){
			$this->_ci =& get_instance();
		}


		function choice_settlement($choice,$type_school,$regid,$path){

			switch($path){
				case '1': $order = 'a.score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '2': $order = 'a.score '.($type_school=='1'?'DESC':'ASC').','.($type_school=='1'?'b.tot_nilai,':'').'b.waktu_pendaftaran ASC';break;
				case '3': $order = "a.score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";break;
				case '4': $order = 'a.score DESC,c.tot_nilai DESC,c.waktu_pendaftaran ASC';break;
				case '5': $order = 'b.score DESC,b.waktu_pendaftaran ASC';break;
			}

			$main_sql = "SELECT a.id_pendaftaran,b.nama,b.sekolah_asal,a.score,b.tot_nilai,b.nil_matematika,
						b.nil_bhs_inggris,b.nil_bhs_indonesia,b.waktu_pendaftaran 
						FROM hasil_seleksi as a LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) WHERE a.jalur_id='".$path."'";

			$passed = array();
			$_passed = array();

			$this->_ci->global_model->reinitialize_dao();
			$dao = $this->_ci->global_model->get_dao();

			if($type_school=='1')
			{

				foreach($choice as $school=>$quota){

					$sql = $main_sql." AND a.sekolah_id='".$school."' ORDER BY ".$order." LIMIT 0,".number_format($quota);

					$rows2 = $dao->execute(0,$sql)->result_array();
					$i = 0;
					$score = 0;

					foreach($rows2 as $row2){
						$i++;

						if($row2['id_pendaftaran']==$regid){
							$score = $row2['score'];
							break;
						}
					}


					if($i<=$quota)
						$passed[$school] = $score;
				}


				if($path=='3'){

					arsort($passed);
					
					$x = reset($passed);
					foreach($passed as $school=>$score){
						if($x==$score)
							$_passed[] = $school;
					}
				}
			}
			else
			{

			}

			return $_passed;
		}

	}
?>
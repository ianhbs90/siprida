<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	require_once APPPATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'Backoffice_parent.php';

	class report extends Backoffice_parent{

		public $active_controller;

		function __construct(){
			parent::__construct();
			$this->active_controller = __CLASS__;
		}

		function final_report(){

			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/final_report');
			$read_access = $this->aah->check_privilege('read',$nav_id);

			if($read_access)
			{
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				
				$sql = "SELECT * FROM ref_tipe_sekolah";
				$jenjang_rows = $dao->execute(0,$sql)->result_array();

				$sql = "SELECT * FROM ref_dt2 WHERE dt2_id!='1900'";
				$dt2_rows = $dao->execute(0,$sql)->result_array();


				$data['jenjang_rows'] = $jenjang_rows;
				$data['dt2_rows'] = $dt2_rows;
				$data['form_id'] = 'search_form';
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['active_controller'] = $this->active_controller;
				$this->backoffice_template->render($this->active_controller.'/final_report/index',$data);

			}else{
				$this->error_403();
			}
		}

		function control_final_report(){

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$mode = $this->input->post('filter_mode');
			$dt2_id = '';
			$jenjang = '';

			if($mode=='1'){

				$dt2_id = $this->input->post('filter_dt2');
				$jenjang = $this->input->post('filter_jenjang');

			}

			$format = $this->input->post('filter_format');
			
			$data['mode'] = $mode;
			$data['dt2_id'] = $dt2_id;
			$data['jenjang'] = $jenjang;
			$data['format'] = $format;
			$data['active_controller'] = $this->active_controller;

			$this->load->view($this->active_controller.'/final_report/control_final_report',$data);

		}

		function final_report_print($mode,$jenjang='',$dt2_id='',$advanced='0'){

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$jalur_rows = $dao->execute(0,"SELECT * FROM ref_jalur_pendaftaran")->result_array();

			if($mode=='1'){

				$dt2_row = $dao->execute(0,"SELECT nama_dt2 FROM ref_dt2 WHERE dt2_id='".$dt2_id."'")->row_array();

				$sekolah_rows = $dao->execute(0,"SELECT * FROM sekolah WHERE tipe_sekolah_id='".$jenjang."' AND dt2_id='".$dt2_id."'")->result_array();

				$registrants = array();
				$graduates = array();
				$settlements = array();
				$quotas = array();
				$quotas2 = array();
				
				foreach($sekolah_rows as $row1){

					$tbl1 = ($jenjang=='1' || $jenjang=='3'?'pendaftaran_sekolah_pilihan':'pendaftaran_kompetensi_pilihan');

					foreach($jalur_rows as $row2){

						if($jenjang=='1'){
							$tbl2 = ($row2['ref_jalur_id'] == '1'?'pengumuman_seleksi_sulsel2':'pengumuman_seleksi_sulsel1');
						}else{
							$tbl2 = ($row2['ref_jalur_id'] == '3'?'pengumuman_seleksi_sulsel2':'pengumuman_seleksi_sulsel1');
						}

						if($jenjang=='1'){
							$db_name = ($row2['ref_jalur_id'] == '1'?'ppdb_sulsel':'ppdb_sulsel_tahap1'); 
						}else{
							$db_name = ($row2['ref_jalur_id'] == '3'?'ppdb_sulsel':'ppdb_sulsel_tahap1');
						}

						$sql = "SELECT COUNT(1) as n FROM ".$db_name.".".$tbl1." WHERE sekolah_id='".$row1['sekolah_id']."' AND jalur_id='".$row2['ref_jalur_id']."'";
						$row3 = $dao->execute(0,$sql)->row_array();

						$sql = "SELECT COUNT(1) as n FROM ".$tbl2." WHERE sekolah_id='".$row1['sekolah_id']."' AND jalur_id='".$row2['ref_jalur_id']."'";
						$row4 = $dao->execute(0,$sql)->row_array();

						$sql = "SELECT COUNT(1) as n FROM daftar_ulang WHERE sekolah_id='".$row1['sekolah_id']."' AND jalur_id='".$row2['ref_jalur_id']."'";
						$row5 = $dao->execute(0,$sql)->row_array();

						$path_name = "";
						switch($row2['ref_jalur_id']){
							case 1:$path_name = 'domisili';break;
							case 2:$path_name = 'afirmasi';break;
							case 3:$path_name = 'akademik';break;
							case 4:$path_name = 'prestasi';break;
							case 5:$path_name = 'khusus';break;
						}

						$sql = "SELECT kuota_".$path_name." as tot_kuota FROM ppdb_sulsel_tahap1.pengaturan_kuota_sma WHERE sekolah_id='".$row1['sekolah_id']."'";
						$row6 = $dao->execute(0,$sql)->row_array();

						$sql = "SELECT kuota_".$path_name." as tot_kuota FROM ppdb_sulsel.pengaturan_kuota_sma WHERE sekolah_id='".$row1['sekolah_id']."'";
						$row7 = $dao->execute(0,$sql)->row_array();

						$registrants[$row1['sekolah_id']][$row2['ref_jalur_id']] = $row3['n'];
						$graduates[$row1['sekolah_id']][$row2['ref_jalur_id']] = $row4['n'];
						$settlements[$row1['sekolah_id']][$row2['ref_jalur_id']] = $row5['n'];
						$quotas[$row1['sekolah_id']][$row2['ref_jalur_id']] = $row6['tot_kuota'];
						$quotas2[$row1['sekolah_id']][$row2['ref_jalur_id']] = $row7['tot_kuota'];
					}	
				}

				$data['jenjang'] = $jenjang;
				$data['nama_dt2'] = $dt2_row['nama_dt2'];
				$data['sekolah_rows'] = $sekolah_rows;
				$data['jalur_rows'] = $jalur_rows;
				$data['registrants'] = $registrants;
				$data['graduates'] = $graduates;
				$data['settlements'] = $settlements;
				$data['quotas'] = $quotas;
				$data['quotas2'] = $quotas2;

			}
			else{

				$jenjang_rows = $dao->execute(0,"SELECT * FROM ref_tipe_sekolah")->result_array();

				$registrants = array();
				$graduates = array();
				$settlements = array();

				foreach($jenjang_rows as $row1){
					
					$tbl1 = ($row1['ref_tipe_sklh_id']=='1' || $row1['ref_tipe_sklh_id']=='3'?'pendaftaran_sekolah_pilihan':'pendaftaran_kompetensi_pilihan');

					foreach($jalur_rows as $row2){

						$tbl2 = ($row2['ref_jalur_id'] == '1'?'pengumuman_seleksi_sulsel2':'pengumuman_seleksi_sulsel1');
						$db_name = ($row2['ref_jalur_id'] == '1'?'ppdb_sulsel_tahap2':'ppdb_sulsel_tahap1'); 

						$sql = "SELECT COUNT(1) as n FROM ".$db_name.".".$tbl1." WHERE jalur_id='".$row2['ref_jalur_id']."'";
						$row3 = $dao->execute(0,$sql)->row_array();

						$sql = "SELECT COUNT(1) as n FROM ".$tbl2." WHERE tipe_sekolah_id='".$row1['ref_tipe_sklh_id']."' AND jalur_id='".$row2['ref_jalur_id']."'";
						$row4 = $dao->execute(0,$sql)->row_array();

						$sql = "SELECT COUNT(1) as n FROM daftar_ulang WHERE tipe_sekolah_id='".$row1['ref_tipe_sklh_id']."' AND jalur_id='".$row2['ref_jalur_id']."'";


						$row5 = $dao->execute(0,$sql)->row_array();

						$registrants[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']] = $row3['n'];
						$graduates[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']] = $row4['n'];
						$settlements[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']] = $row5['n'];

					}

				}

				$data['jenjang_rows'] = $jenjang_rows;
				$data['jalur_rows'] = $jalur_rows;
				$data['registrants'] = $registrants;
				$data['graduates'] = $graduates;
				$data['settlements'] = $settlements;
			}

			$data['mode'] = $mode;
			

			$this->load->view($this->active_controller.'/final_report/final_report_print',$data);

		}

		function get_schools(){
			$dt2_id = $this->input->post('dt2_id');

			$onchange = (!is_null($this->input->post('onchange'))?"onchange=\"get_fields(this.value)\"":"");

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			$cond = "WHERE dt2_id='".$dt2_id."'";
			$cond .= (!is_null($this->input->post('tipe_sekolah_id'))?" AND tipe_sekolah_id='".$this->input->post('tipe_sekolah_id')."'":"");
			$sql = "SELECT sekolah_id,nama_sekolah FROM sekolah ".$cond;			
			$rows = $dao->execute(0,$sql)->result_array();
			$this->load->view($this->active_controller.'/global/school_opts.php',array('rows'=>$rows,'onchange'=>$onchange));
		}



		//SELECTION RESULT PACKET
		function selection_result(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/selection_result');
			$read_access = $this->aah->check_privilege('read',$nav_id);

			if($read_access)
			{
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$data['form_id'] = 'search_form';
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$this->backoffice_template->render($this->active_controller.'/selection_result/index',$data);

			}else{
				$this->error_403();
			}
		}

	

		function search_result(){
			$this->aah->check_access();

			$error = 0;

			$this->load->helper(array('date_helper','mix_helper'));
			$id_pendaftaran = $this->security->xss_clean($this->input->post('src_registrasi'));

			$dao = $this->global_model->get_dao();
			$sql = "SELECT a.id_pendaftaran,a.nama,a.sekolah_asal,b.tipe_sekolah_id,b.jalur_id,b.nama_jalur FROM pendaftaran as a 
												LEFT JOIN (SELECT a.tipe_sekolah_id,a.id_pendaftaran,b.nama_jalur,a.jalur_id 
													FROM pendaftaran_jalur_pilihan as a LEFT JOIN ref_jalur_pendaftaran as b ON a.jalur_id=b.ref_jalur_id) as b  
												ON (a.id_pendaftaran=b.id_pendaftaran)
												WHERE a.id_pendaftaran='".$id_pendaftaran."'";
							
			$pendaftaran_row = $dao->execute(0,$sql)->row_array();

			$kuota_row1 = array();
			$kuota_row2 = array();
			
			$new_result_rows = array();
			$old_result_rows = array();
			
			$pilihan_rows = array();


			if($pendaftaran_row['tipe_sekolah_id']=='1'){
				
				$sql1 = "SELECT sekolah_id,".($pendaftaran_row['tipe_sekolah_id']=='1'?"'' as kompetensi_id":"kompetensi_id")." FROM hasil_seleksi_reserved WHERE id_pendaftaran='".$id_pendaftaran."' ";
				 
				$sql2 = "SELECT sekolah_id,".($pendaftaran_row['tipe_sekolah_id']=='1'?"'' as kompetensi_id":"kompetensi_id")." FROM ".($pendaftaran_row['tipe_sekolah_id']=='1'?"v_hasil":"v_hasil_smk")." WHERE id_pendaftaran='".$id_pendaftaran."'";
				
				$old_result = $dao->execute(0,$sql1)->row_array();
				$curr_result = $dao->execute(0,$sql2)->row_array();
				$jalur_id = $pendaftaran_row['jalur_id'];
				$order = "";
				switch($jalur_id){
					case '1': $order = 'a.score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='domisili';break;
					case '2': $order = 'a.score '.($jalur_id=='1'?'ASC':'DESC').',b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='afirmasi';break;
					case '3': $order = "a.score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";$path_name='akademik';break;
					case '4': $order = 'a.score DESC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='prestasi';break;
					case '5': $order = 'b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='khusus';break;
				}


				if($pendaftaran_row['tipe_sekolah_id']=='1'){

					$sql = "SELECT kuota_".$path_name." as tot_kuota,b.nama_sekolah FROM pengaturan_kuota_sma as a LEFT JOIN 
							sekolah as b ON (a.sekolah_id=b.sekolah_id) WHERE a.sekolah_id='".$old_result['sekolah_id']."'";
				}else{

					$sql = "SELECT kuota_".$path_name." as tot_kuota,b.nama_sekolah,b.nama_kompetensi FROM pengaturan_kuota_sma as a LEFT JOIN 
							(SELECT a.kompetensi_id,a.sekolah_id,b.nama_kompetensi,b.nama_sekolah FROM kompetensi_smk as a 
							 LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)) as b ON (a.sekolah_id=b.sekolah_id) WHERE a.sekolah_id='".$old_result['sekolah_id']."'";

				}
				
				
				$kuota_row1 = $dao->execute(0,$sql)->row_array();
				

				if($pendaftaran_row['tipe_sekolah_id']=='1'){
					$sql = "SELECT kuota_".$path_name." as tot_kuota,b.nama_sekolah FROM pengaturan_kuota_sma as a LEFT JOIN 
							sekolah as b ON (a.sekolah_id=b.sekolah_id) WHERE a.sekolah_id='".$curr_result['sekolah_id']."'";

				}else{

					$sql = "SELECT kuota_".$path_name." as tot_kuota,b.nama_sekolah,b.nama_kompetensi FROM pengaturan_kuota_sma as a LEFT JOIN 
							(SELECT a.kompetensi_id,a.sekolah_id,b.nama_kompetensi,b.nama_sekolah FROM kompetensi_smk as a 
							 LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)) as b ON (a.sekolah_id=b.sekolah_id) WHERE a.sekolah_id='".$curr_result['sekolah_id']."'";

				}

				$kuota_row2 = $dao->execute(0,$sql)->row_array();

				if($pendaftaran_row['tipe_sekolah_id']=='1'){
					$sql1 = "SELECT 
							a.id_pendaftaran,
							b.nama,
							b.sekolah_asal,
							a.score,
							b.tot_nilai,
							b.nil_matematika,
							b.nil_bhs_inggris,
							b.nil_bhs_indonesia,
							b.waktu_pendaftaran 
							FROM v_hasil as a
							LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran)  
							WHERE a.jalur_id='".$jalur_id."' AND a.tipe_sekolah_id='".$pendaftaran_row['tipe_sekolah_id']."' 
							AND a.sekolah_id='".$curr_result['sekolah_id']."'
							ORDER BY ".$order;


					$sql2 = "SELECT 
						a.id_pendaftaran,
						b.nama,
						b.sekolah_asal,
						a.score,
						b.tot_nilai,
						b.nil_matematika,
						b.nil_bhs_inggris,
						b.nil_bhs_indonesia,
						b.waktu_pendaftaran 
						FROM hasil_seleksi_reserved as a 
						LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran)  
						WHERE a.jalur_id='".$jalur_id."' 
						AND a.tipe_sekolah_id='".$pendaftaran_row['tipe_sekolah_id']."' 
						AND a.sekolah_id='".$old_result['sekolah_id']."'
						ORDER BY ".$order;
				}else{
					$sql1 = "SELECT 
							a.id_pendaftaran,
							b.nama,
							b.sekolah_asal,
							a.score,
							b.tot_nilai,
							b.nil_matematika,
							b.nil_bhs_inggris,
							b.nil_bhs_indonesia,
							b.waktu_pendaftaran 
							FROM v_hasil_smk as a
							LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran)  
							WHERE a.jalur_id='".$jalur_id."' AND a.tipe_sekolah_id='".$pendaftaran_row['tipe_sekolah_id']."' 
							AND a.kompetensi_id='".$curr_result['sekolah_id']."'
							ORDER BY ".$order;

					$sql2 = "SELECT 
						a.id_pendaftaran,
						b.nama,
						b.sekolah_asal,
						a.score,
						b.tot_nilai,
						b.nil_matematika,
						b.nil_bhs_inggris,
						b.nil_bhs_indonesia,
						b.waktu_pendaftaran 
						FROM hasil_seleksi_reserved as a 
						LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran)  
						WHERE a.jalur_id='".$jalur_id."' 
						AND a.tipe_sekolah_id='".$pendaftaran_row['tipe_sekolah_id']."' 
						AND a.kompetensi_id='".$old_result['kompetensi_id']."'
						ORDER BY ".$order;
				}

				$new_result_rows = $dao->execute(0,$sql1)->result_array();
				$old_result_rows = $dao->execute(0,$sql2)->result_array();


				$sql = "SELECT a.sekolah_id,b.nama_sekolah FROM pendaftaran_sekolah_pilihan as a LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
						WHERE a.id_pendaftaran='".$id_pendaftaran."'";
				$pilihan_rows = $dao->execute(0,$sql)->result_array();

				$ext_cond1 = "";
				if($jalur_id==2 or $jalur_id==3){
					$ext_cond1 = " AND a.jarak_sekolah is not null";
				}

				$cond = "WHERE a.status='3' AND a.jalur_id='".$jalur_id."' ".$ext_cond1;

				switch($jalur_id){
					case '1': $order = 'score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='domisili';break;
					case '2': $order = 'score '.($jalur_id=='1'?'ASC':'DESC').',b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='afirmasi';break;
					case '3': $order = "score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";$path_name='akademik';break;
					case '4': $order = 'score DESC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='prestasi';break;
					case '5': $order = 'b.tot_nilai DESC,b.waktu_pendaftaran ASC';$path_name='khusus';break;
				}

				if($jalur_id==2){

					$main_sql = "SELECT 
							   a.id_pendaftaran,
							   b.nama,
							   b.sekolah_asal,
							   a.jarak_sekolah as score,
							   a.jarak_sekolah,
							   a.pilihan_ke,
							   b.tot_nilai,
							   b.waktu_pendaftaran 
							   FROM pendaftaran_sekolah_pilihan as a 
							   LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
							   LEFT JOIN sekolah as c ON (a.sekolah_id=c.sekolah_id) 
							   INNER JOIN (SELECT id_pendaftaran FROM pendaftaran_dokumen_kelengkapan WHERE dokumen='10' AND status='1') as d ON (a.id_pendaftaran=d.id_pendaftaran) ".$cond;
				}
				else if($jalur_id==3){
					$main_sql = "SELECT 
								a.id_pendaftaran,
								b.nama,
							   	b.sekolah_asal,
								a.pilihan_ke,
								get_academicScore(b.tot_nilai,a.jarak_sekolah,b.mode_un,".$pendaftaran_row['tipe_sekolah_id'].") as score,
								b.nil_matematika,
								b.nil_bhs_inggris,
								b.nil_bhs_indonesia,
								b.waktu_pendaftaran 
								FROM pendaftaran_sekolah_pilihan as a 
								LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran)
								LEFT JOIN sekolah as c ON (a.sekolah_id=c.sekolah_id) ".$cond;
				}else if($jalur_id==4){
					$main_sql = "SELECT 
								a.id_pendaftaran,
								b.nama,
							   	b.sekolah_asal,
								a.pilihan_ke,
								c.tot_nilai,
								get_achievementWeight((SELECT x.tkt_kejuaraan_id
								FROM pendaftaran_prestasi as x WHERE a.id_pendaftaran=x.id_pendaftaran 
								ORDER BY x.tkt_kejuaraan_id ASC, x.peringkat ASC LIMIT 0,1),(SELECT x.peringkat
								FROM pendaftaran_prestasi as x WHERE a.id_pendaftaran=x.id_pendaftaran 
								ORDER BY x.tkt_kejuaraan_id ASC, x.peringkat ASC LIMIT 0,1)) as score,
								c.waktu_pendaftaran 
								FROM pendaftaran_sekolah_pilihan as a
								LEFT JOIN pendaftaran as c ON (a.id_pendaftaran=c.id_pendaftaran) ".$cond;
				}
				else if($jalur_id==5){
					$main_sql = "SELECT 
								a.id_pendaftaran,
								b.nama,
							   	b.sekolah_asal,
								a.pilihan_ke,
								b.tot_nilai as score,
								b.waktu_pendaftaran 
								FROM pendaftaran_sekolah_pilihan as a 
								LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) ".$cond;
				}

				foreach($pilihan_rows as $row){

					$kuota_row = $dao->execute(0,"SELECT kuota_".$path_name." as tot_kuota FROM pengaturan_kuota_sma WHERE sekolah_id='".$row['sekolah_id']."'")->row_array();

					$sql = $main_sql." AND a.sekolah_id='".$row['sekolah_id']."' ORDER BY ".$order." LIMIT 0,".number_format($kuota_row['tot_kuota']);

					$rows = $dao->execute(0,$sql)->result_array();
					
					$hasil_seleksi_arr[$row['sekolah_id']] = array('rows'=>$rows,'kuota'=>$kuota_row);

				}
			}else{
				$hasil_seleksi_arr = array();
			}
	

			$data['kuota_row1'] = $kuota_row1;
			$data['kuota_row2'] = $kuota_row2;
			
			$data['new_result_rows'] = $new_result_rows;
			$data['old_result_rows'] = $old_result_rows;
			$data['pendaftaran_row'] = $pendaftaran_row;
			$data['pilihan_rows'] = $pilihan_rows;
			$data['hasil_seleksi_arr'] = $hasil_seleksi_arr;

			$this->load->view($this->active_controller.'/selection_result/monitor',$data);
		}

		//END OF SELECTION RESULT PACKET


		function registration_history(){
			$this->aah->check_access();
						
			$data['form_id'] = 'search_form';
			$data['active_url'] = str_replace('::','/',__METHOD__);
			$this->backoffice_template->render($this->active_controller.'/registration_history/index',$data);
		}

		function search_registration_history(){
			$this->aah->check_access();
			$this->load->helper('date_helper');

			$error = 0;
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$no_peserta = $this->input->post('src_settlement');
			
			$error = 0;

			$pendaftaran_row = $dao->execute(0,"SELECT a.*,b.tipe_sekolah_id,b.sekolah_id,b.kompetensi_id,DATE_FORMAT(b.created_time,'%Y-%m-%d') as tgl_daftar_ulang 
												FROM pendaftaran as a LEFT JOIN daftar_ulang as b ON (a.id_pendaftaran=b.id_pendaftaran) 
												WHERE a.id_pendaftaran='".$no_peserta."'")->row_array();						
			
			$sekolah_tujuan_row = array();
			if($pendaftaran_row['sekolah_id']){
				if($pendaftaran_row['tipe_sekolah_id']=='1'){
					$sql = "SELECT nama_sekolah FROM sekolah WHERE sekolah_id='".$pendaftaran_row['sekolah_id']."'";
					$sekolah_tujuan_row = $dao->execute(0,$sql)->row_array();
				}else{
					$sql = "SELECT a.nama_kompetensi,b.nama_sekolah FROM kompetensi_smk as a LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
							WHERE a.kompetensi_id='".$pendaftaran_row['kompetensi_id']."'";
					$sekolah_tujuan_row = $dao->execute(0,$sql)->row_array();
				}
			}

			$data['error'] = $error;
			$data['pendaftaran_row'] = $pendaftaran_row;
			$data['sekolah_tujuan_row'] = $sekolah_tujuan_row;

			$this->load->view($this->active_controller.'/registration_history/data_view',$data);
		}
	}
?>
<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class reg extends CI_Controller{

		public $active_controller;
		private $tipe_sekolah_rows,
				$tipe_sekolah_arr,
				$jalur_pendaftaran_rows,
				$jalur_pendaftaran_arr,
				$tabs,$tabs_view,$default_tab,$_SYS_PARAMS;

		function __construct(){
			
			parent::__construct();

			$this->load->library(array('public_template','DAO','session'));
			$this->load->helper('url');
			$this->load->model(array('ref_tipe_sekolah_model','ref_jalur_pendaftaran_model','global_model'));
			
			$this->active_controller = __CLASS__;			

			foreach($this->ref_tipe_sekolah_model->get_all_data() as $row){
				$this->tipe_sekolah_arr[$row['ref_tipe_sklh_id']] = $row['akronim'];
			}

			foreach($this->ref_jalur_pendaftaran_model->get_all_data() as $row){
				$this->jalur_pendaftaran_arr[$row['ref_jalur_id']] = $row['nama_jalur'];
			}

			$this->tipe_sekolah_rows = $this->ref_tipe_sekolah_model->get_all_data();

			$this->tabs = array('Panduan','Aturan','Jadwal','Prosedur','Daftar','Hasil','Data','Kuota');
			$this->tabs_view = array('home','regulation','schedule','procedure','registration','result','statistic','quota','registration_data');

			$this->_SYS_PARAMS = $this->global_model->get_system_params();
			$this->default_tab = 1;
		}

		private function get_registration_path($stage){
			
			$this->load->model('global_model');
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			$sql = "SELECT b.* FROM pengaturan_kuota_jalur as a 
					LEFT JOIN ref_jalur_pendaftaran as b ON (a.jalur_id=b.ref_jalur_id) 
					WHERE tipe_sekolah_id='".$stage."'";
			$rows = $dao->execute(0,$sql)->result_array();			
			return $rows;
		}

		function stage($stage,$path='',$tab=''){
			
			$data['_SYS_PARAMS'] = $this->_SYS_PARAMS;
			$data['active_controller'] = $this->active_controller;
			$data['breadcrumbs'] = $this->generate_breadcrumbs($stage,$path);
			$data['jalur_pendaftaran_rows'] = $this->get_registration_path($stage);
			$data['tipe_sekolah_rows'] = $this->tipe_sekolah_rows;
			$data['stage'] = $stage;
			$data['path'] = $path;

			if($path=='')
				$data['tab_view'] = $this->tabs_view[0];
			else{
				$data['tab_view'] = $this->tabs_view[(!empty($tab)?$tab:$this->default_tab)];
			}
			
			$dao = $this->global_model->get_dao();

			if($path!='')
			{
				if($this->default_tab=='1'){
					$sql = "SELECT ketentuan FROM ketentuan_jalur WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' AND jalur_id='".$path."'";
					$ketentuan_jalur_rows = $dao->execute(0,$sql)->result_array();

					$data['ketentuan_jalur_rows'] = $ketentuan_jalur_rows;
					$data['nama_jalur'] = $this->jalur_pendaftaran_arr[$path];
				}
			}else{

				$this->load->helper('date_helper');
				$sql = "SELECT * FROM pengumuman WHERE status='1' ORDER BY waktu_posting DESC";				
				$data['pengumuman_row'] = $dao->execute(0,$sql)->row_array();
			}

			$this->public_template->render('reg/index.php',$data);

		}

		function generate_breadcrumbs($stage,$path='',$tab=''){
			
			$tab = (empty($tab)?$this->default_tab:$tab);

			$breadcrumbs = array(
							array('url'=>base_url(),'text'=>'Home','active'=>false),
							array('url'=>base_url().'reg/stage/'.$stage,'text'=>$this->tipe_sekolah_arr[$stage],'active'=>false),
						);
			if($path==''){
				$breadcrumbs[] = array('url'=>'#','text'=>'Info Umum','active'=>true);
			}else{
				$breadcrumbs[] = array('url'=>base_url().'reg/stage/'.$stage.'/'.$path,'text'=>$this->jalur_pendaftaran_arr[$path],'active'=>false);
				$breadcrumbs[] = array('url'=>'#','text'=>$this->tabs[$tab],'active'=>true);
			}
			return $breadcrumbs;
		}


		function content_tab_menu(){
			
			$this->load->helper(array('date_helper','scoring_helper'));
			$availability = '';
			$data['availability'] = $availability;
			$stage = (!is_null($this->input->post('stage'))?$this->input->post('stage'):'');
			$path = (!is_null($this->input->post('path'))?$this->input->post('path'):'');
			$tab_id = (!is_null($this->input->post('tab_id'))?$this->input->post('tab_id'):$this->default_tab);

			$data = array();
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$view = $this->tabs_view[$tab_id];

			$data['nama_jalur'] = $this->jalur_pendaftaran_arr[$path];
			$data['tipe_sekolah'] = $this->tipe_sekolah_arr[$stage];

			$sql = "SELECT a.jml_sekolah,a.persen_kuota,a.lintas_dt2,b.tgl_buka,b.tgl_tutup FROM pengaturan_kuota_jalur as a 
					LEFT JOIN jadwal_jalur_pendaftaran as b ON (a.jalur_id=b.jalur_id) AND (a.tipe_sekolah_id=b.tipe_sklh_id) 
					WHERE a.thn_pelajaran='".$this->_SYS_PARAMS[0]."' AND a.jalur_id='".$path."' AND a.tipe_sekolah_id='".$stage."'";
			
			$kuota_jalur_row = $dao->execute(0,$sql)->row_array();
			
			$data['kuota_jalur_row'] = $kuota_jalur_row;

			if($tab_id==1){
				$sql = "SELECT ketentuan FROM ketentuan_jalur WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' AND jalur_id='".$path."'";
				$ketentuan_jalur_rows = $dao->execute(0,$sql)->result_array();
				$data['ketentuan_jalur_rows'] = $ketentuan_jalur_rows;				
			}else if($tab_id==2){
				$sql = "SELECT * FROM jadwal_kegiatan_pendaftaran WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' AND jalur_id='".$path."'
						AND tipe_sklh_id='".$stage."'";
				$jadwal_kegiatan_rows = $dao->execute(0,$sql)->result_array();
				$data['jadwal_kegiatan_rows'] = $jadwal_kegiatan_rows;				
			}else if($tab_id==3){
				// no action
			}
			if($tab_id==4)
			{

				$availability = check_status_dateRange($kuota_jalur_row['tgl_buka'],$kuota_jalur_row['tgl_tutup']);
				$data['availability'] = $availability;
				if($availability=='on')
				{

					if(!is_null($this->session->userdata('nopes')) or $path=='5' or $stage=='3')
					{
						$bidang_kejuaraan_opts = "<option value=''></option>";
						
						$sql = "SELECT a.status,b.* FROM pengaturan_dokumen_persyaratan as a LEFT JOIN ref_dokumen_persyaratan as b ON (a.dokumen_id=b.ref_dokumen_id) 
								WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' AND jalur_id='".$path."'";
						$dokumen_persyaratan_rows = $dao->execute(0,$sql)->result_array();
						$data['dokumen_persyaratan_rows'] = $dokumen_persyaratan_rows;

						if(!is_null($this->session->userdata('nopes'))){
							$nopes = $this->session->userdata('nopes');

							$sql = "SELECT a.mode_un,a.tpt_lahir,a.tgl_lahir,a.nm_orang_tua,a.nil_bhs_indonesia,
									a.nil_bhs_inggris,a.nil_matematika,a.nil_ipa,a.tot_nilai,a.dt2_id,
									a.status,a.no_pendaftaran,b.dt2_kd
									FROM pendaftaran as a 
									LEFT JOIN (SELECT dt2_id,dt2_kd FROM ref_dt2) as b ON (a.dt2_id=b.dt2_id)
									WHERE a.id_pendaftaran='".$nopes."'";
							
							
							$peserta_row = $dao->execute(0,$sql)->row_array();
							$status_peserta = trim($peserta_row['status']);

							
						}			

						$row = $dao->execute(0,"SELECT * FROM ketentuan_berlaku")->row_array();
						$data['ketentuan_berlaku'] = $row['deskripsi'];


						if(($path!='5' and $stage!='3') or ($stage=='3' and !is_null($this->session->userdata('nopes'))))
						{

							if($status_peserta=='0')
							{
								$pengaturan_dt2_sekolah_rows = array();
								
								if($kuota_jalur_row['lintas_dt2']=='2')
								{
									if($stage!='3')
									{
										$sql = "SELECT dt2_id as dt2_sekolah_id,nama_dt2 FROM ref_dt2";
									}else{
										$sql = "SELECT dt2_id as dt2_sekolah_id,nama_dt2 FROM ref_dt2 WHERE dt2_id IN (SELECT dt2_id FROM sekolah WHERE tipe_sekolah_id='3')";
									}
									$pengaturan_dt2_sekolah_rows = $dao->execute(0,$sql)->result_array();
								}
								
								$sql = "SELECT * FROM ref_kecamatan WHERE dt2_id='".$peserta_row['dt2_kd']."'";
								$kecamatan_rows = $dao->execute(0,$sql)->result_array();							

								$sql = "SELECT sekolah_id,nama_sekolah FROM sekolah WHERE dt2_id='".$this->session->userdata('id_dt2')."' AND tipe_sekolah_id='".$stage."'";
								$sekolah_rows = $dao->execute(0,$sql)->result_array();

								$data['peserta_row'] = $peserta_row;
								$data['pengaturan_dt2_sekolah_rows'] = $pengaturan_dt2_sekolah_rows;
								$data['kecamatan_rows'] = $kecamatan_rows;
								$data['sekolah_rows'] = $sekolah_rows;
								
								$input_arr = array('nopes'=>$this->session->userdata('nopes'),'nama'=>$this->session->userdata('nama'),
												   'jk'=>($this->session->userdata('jk')=='L'?'Laki-laki':'Perempuan'),'sklh_asal'=>$this->session->userdata('sklh_asal'),
												   'tpt_lahir'=>$peserta_row['tpt_lahir'],'tgl_lahir'=>indo_date_format($peserta_row['tgl_lahir'],'shortDate'),
												   'alamat'=>$this->session->userdata('alamat'),'nm_dt2'=>$this->session->userdata('nm_dt2'),
												   'nil_bhs_indonesia'=>$peserta_row['nil_bhs_indonesia'],
												   'nil_bhs_inggris'=>$peserta_row['nil_bhs_inggris'],'nil_matematika'=>$peserta_row['nil_matematika'],
												   'nil_ipa'=>$peserta_row['nil_ipa'],
												   'tot_nilai'=>$peserta_row['tot_nilai']);

								
								$data['input_arr'] = $input_arr;
								$data['input_rule'] = 'readonly';

							}
							else
							{

								$data = $this->prepare_registration_data($nopes);
							}

							$view = $status_peserta=='0'?$this->tabs_view[$tab_id]:$this->tabs_view[8];

						}else{

							if(is_null($this->session->userdata('nopes')) or (!is_null($this->session->userdata('nopes') and $stage=='3')))
							{								
								$input_arr = array('nopes'=>'','nama'=>'','jk'=>'','sklh_asal'=>'','tpt_lahir'=>'',
												   'tgl_lahir'=>'','alamat'=>'','nm_dt2'=>'',
												   'nil_bhs_indonesia'=>'','nil_bhs_inggris'=>'','nil_matematika'=>'','nil_ipa'=>'',
												   'tot_nilai'=>'');

								// if($path=='5'){
								// 	$sql = "SELECT dt2_id as dt2_sekolah_id,nama_dt2 FROM ref_dt2";
								// 	$pengaturan_dt2_sekolah_rows = $dao->execute(0,$sql)->result_array();
								// }else{
								// 	$pengaturan_dt2_sekolah_rows = array();
								// }

								if($stage!='3')
								{
									$sql = "SELECT dt2_id as dt2_sekolah_id,nama_dt2 FROM ref_dt2";
								}else{
									$sql = "SELECT dt2_id as dt2_sekolah_id,nama_dt2 FROM ref_dt2 WHERE dt2_id IN (SELECT dt2_id FROM sekolah WHERE tipe_sekolah_id='3')";
								}
								$pengaturan_dt2_sekolah_rows = $dao->execute(0,$sql)->result_array();

								$data['input_arr'] = $input_arr;
								$input_rule = ($stage!='3'?'required':'disabled');
								$data['input_rule'] = $input_rule;
								
								$data['pengaturan_dt2_sekolah_rows'] = $pengaturan_dt2_sekolah_rows;
								$data['sekolah_rows'] = array();
							}else{

								if($status_peserta=='0')
								{
									$data['warning'] = 'Jalur ini hanya diperuntukkan untuk peserta yang belum terdaftar!';
									$view = 'warning';
								}else{
									$data = $this->prepare_registration_data($nopes);
									$view = $this->tabs_view[8];
								}
							}
						}

						$sql = "SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."' ".($stage!='3'?" AND dt2_id!='1900'":"");
						$dt2_rows = $dao->execute(0,$sql)->result_array();

						$data['dt2_rows'] = $dt2_rows;

						if($path==4)
						{
							$tingkat_kejuaraan_rows = $dao->execute(0,"SELECT * FROM ref_tingkat_kejuaraan")->result_array();
							$bidang_kejuaraan_rows = $dao->execute(0,"SELECT * FROM ref_bidang_kejuaraan")->result_array();					

							foreach($bidang_kejuaraan_rows as $row){
								$bidang_kejuaraan_opts .= "<option value='".$row['ref_bdg_kejuaraan_id']."'>".$row['bidang_kejuaraan']."</option>";
							}

							$data['tingkat_kejuaraan_rows'] = $tingkat_kejuaraan_rows;							
						}
						$data['bidang_kejuaraan_opts'] = $bidang_kejuaraan_opts;

					}else{
						$data['warning'] = 'Silahkan login untuk melakukan pendaftaran!';
						$view = "warning";					
					}
				}
			}
			else if($tab_id==5)
			{

				$this->load->library(array('settlement'));

				
				$sql = "SELECT a.tipe_sekolah_id,b.nama_jalur,a.jalur_id FROM pendaftaran_jalur_pilihan as a LEFT JOIN ref_jalur_pendaftaran as b 
						ON (a.jalur_id=b.ref_jalur_id) WHERE a.id_pendaftaran='".$this->session->userdata('nopes')."'";

				$jalur_row = $dao->execute(0,$sql)->row_array();
				$hasil_seleksi_arr = array();
				$sekolah_pilihan_rows = array();
				$passed_choice = array();
				if(count($jalur_row)>0)
				{
	                $jenis_kuota = '';
	                switch($jalur_row['jalur_id']){
	                	case '1':$jenis_kuota='domisili';break;
	                	case '2':$jenis_kuota='afirmasi';break;
	                	case '3':$jenis_kuota='akademik';break;
	                	case '4':$jenis_kuota='prestasi';break;
	                	case '5':$jenis_kuota='khusus';break;
	                }

                	//fields
					$sql1 = "SELECT a.kompetensi_id,b.nama_kompetensi,c.kuota_jalur 
							FROM pendaftaran_kompetensi_pilihan as a 
							LEFT JOIN kompetensi_smk as b ON (a.kompetensi_id=b.kompetensi_id)
							LEFT JOIN (SELECT kompetensi_id,kuota_".$jenis_kuota." as kuota_jalur FROM pengaturan_kuota_smk 
										WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' AND sekolah_id=?) as c ON (a.kompetensi_id=c.kompetensi_id)
							WHERE a.id_pendaftaran='".$this->session->userdata('nopes')."' AND a.sekolah_id=? ORDER BY a.pilihan_ke ASC";
					

					$this->global_model->reinitialize_dao();
					$kompetensi_dao = $this->global_model->get_dao();
					$kompetensi_dao->set_sql_with_params($sql1);

					//schools
					if($jalur_row['tipe_sekolah_id']=='1')
					{
						$sql = "SELECT a.sekolah_id,c.kuota_jalur,a.status,b.nama_sekolah FROM pendaftaran_sekolah_pilihan as a 
								LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
								LEFT JOIN (SELECT sekolah_id,kuota_".$jenis_kuota." as kuota_jalur 
									FROM pengaturan_kuota_sma WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."') as c ON (a.sekolah_id=c.sekolah_id)
								WHERE id_pendaftaran='".$this->session->userdata('nopes')."'";
						
					}else{
						$sql = "SELECT a.sekolah_id,a.status,b.nama_sekolah FROM pendaftaran_sekolah_pilihan as a 
								LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)
								WHERE id_pendaftaran='".$this->session->userdata('nopes')."'";
					}
					$sql .= " ORDER BY pilihan_ke ASC";


					$sekolah_pilihan_rows = $dao->execute(0,$sql)->result_array();
					$sekolah_pilihan_arr = array();
					foreach($sekolah_pilihan_rows as $row){
						$sekolah_pilihan_arr[] = array($row['sekolah_id'],$row['status']);
					}

					$hasil_seleksi_arr = array();
					
					$order = "";
					$tipe_sekolah = $jalur_row['tipe_sekolah_id'];

					switch($jalur_row['jalur_id']){
						case '1': $order = 'a.score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
						case '2': $order = 'a.score '.($tipe_sekolah=='1'?'DESC':'ASC').','.($tipe_sekolah=='1'?'b.tot_nilai,':'').'b.waktu_pendaftaran ASC';break;
						case '3': $order = "a.score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";break;
						case '4': $order = 'a.score DESC,c.tot_nilai DESC,c.waktu_pendaftaran ASC';break;
						case '5': $order = 'b.score DESC,b.waktu_pendaftaran ASC';break;
					}


					if($jalur_row['tipe_sekolah_id']=='1')
					{

						$main_sql = "SELECT a.id_pendaftaran,b.nama,b.sekolah_asal,a.score,b.tot_nilai,b.nil_matematika,
								b.nil_bhs_inggris,b.nil_bhs_indonesia,b.waktu_pendaftaran 
								FROM verifikasi as a 
								LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
								WHERE a.jalur_id='".$jalur_row['jalur_id']."'";
						 
						foreach($sekolah_pilihan_arr as $key=>$val){

							$sql = $main_sql." AND a.sekolah_id='".$val[0]."' ORDER BY ".$order;
							$rows2 = $dao->execute(0,$sql)->result_array();
							$hasil_seleksi_arr[$val[0]] = $rows2;
						}

						$choice = array_combine(array_column($sekolah_pilihan_rows,'sekolah_id'),
												array_column($sekolah_pilihan_rows,'kuota_jalur'));
						
					}
					else
					{


						$constraintStatus = false;
						$status_cond = ($constraintStatus?" AND a.status='3'":"");

						$cond = "WHERE a.jalur_id='".$jalur_row['jalur_id']."' ".$status_cond;
						
						$main_sql = "SELECT a.id_pendaftaran,b.nama,b.sekolah_asal,a.score,b.tot_nilai,b.nil_matematika,
									b.nil_bhs_inggris,b.nil_bhs_indonesia,b.waktu_pendaftaran 
									FROM verifikasi as a 
									LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
									WHERE a.jalur_id='".$jalur_row['jalur_id']."'";


						foreach($sekolah_pilihan_arr as $key=>$val){

							
							$params1 = array($val[0],$val[0]);
							$kompetensi_dao->set_sql_params($params1);
							$rows2 = $kompetensi_dao->execute(1)->result_array();

							foreach($rows2 as $row2)
							{
								
								$sql = $main_sql." AND a.kompetensi_id='".$row2['kompetensi_id']."' ORDER BY ".$order;

								$rows3 = $dao->execute(0,$sql)->result_array();
								$hasil_seleksi_arr[$val[0]][$row2['kompetensi_id']] = $rows3;

							}

						}			
					
					}

					$data['kompetensi_dao'] = $kompetensi_dao;

					
				}
				
				$this->global_model->reinitialize_dao();				
				$data['jalur_pilihan'] = $jalur_row['jalur_id'];
				$data['nama_jalur_pilihan'] = $jalur_row['nama_jalur'];
				$data['sekolah_pilihan_rows'] = $sekolah_pilihan_rows;
				$data['hasil_seleksi_arr'] = $hasil_seleksi_arr;
				$data['_SYS_PARAMS'] = $this->_SYS_PARAMS;
				$data['passed_choice'] = $passed_choice;

				$data['tipe_sekolah'] = $jalur_row['tipe_sekolah_id'];


			}else if($tab_id==6){								

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();			

				$sql = "SELECT (SELECT COUNT(1) FROM pendaftaran_jalur_pilihan as x WHERE x.jalur_id=a.ref_jalur_id) as n_pendaftaran, 
						(SELECT SUM(jumlah_kuota) FROM pengaturan_kuota_jalur as y WHERE y.jalur_id=a.ref_jalur_id) as n_kuota
						FROM ref_jalur_pendaftaran as a WHERE a.ref_jalur_id=?";

				$dao->set_sql_with_params($sql);
				
				$labelChart = "[";
				$dataChart = "[";
				$s = false;
				foreach($this->jalur_pendaftaran_arr as $key=>$val){
					$params = array($key);
					$dao->set_sql_params($params);
					$row = $dao->execute(1)->row_array();


					$ratio = ($row['n_kuota']>0?$row['n_pendaftaran']/$row['n_kuota']*100:0);
					$labelChart .= ($s?",":"")."'".$val."'";
					$dataChart .= ($s?",":"").number_format($ratio,3,'.',',');
					$s = true;
				}

				$labelChart .= "]";
				$dataChart .= "]";
				$data['labelChart'] = $labelChart;
				$data['dataChart'] = $dataChart;

				$jenis_kuota = '';
                switch($path){
                	case '1':$jenis_kuota='domisili';break;
                	case '2':$jenis_kuota='afirmasi';break;
                	case '3':$jenis_kuota='akademik';break;
                	case '4':$jenis_kuota='prestasi';break;
                	case '5':$jenis_kuota='khusus';break;
                }

				$sql = "SELECT SUM(CASE WHEN a.tipe_sekolah=? THEN kuota_".$jenis_kuota." else 0 END) as tot_kuota 
						FROM
						(SELECT sekolah_id,kuota_".$jenis_kuota.",'1' AS tipe_sekolah FROM pengaturan_kuota_sma 
						WHERE thn_pelajaran=?
						UNION
						SELECT DISTINCT sekolah_id, SUM(kuota_".$jenis_kuota.") AS kuota_".$jenis_kuota.", '2' AS tipe_sekolah FROM pengaturan_kuota_smk 
						WHERE thn_pelajaran=? 
						GROUP BY sekolah_id) AS a";
				
				
				$this->global_model->reinitialize_dao();
				$dao1 = $this->global_model->get_dao();
				$dao1->set_sql_with_params($sql);

				$sql = "SELECT COUNT(1) as tot_pendaftar FROM pendaftaran_jalur_pilihan WHERE jalur_id='".$path."' AND tipe_sekolah_id=?";
				$this->global_model->reinitialize_dao();
				$dao2 = $this->global_model->get_dao();
				$dao2->set_sql_with_params($sql);

				$kuota_sekolah = array();
				$pendaftar_sekolah = array();

				foreach($this->tipe_sekolah_arr as $key=>$val){
					
					$params1 = array($key,$this->_SYS_PARAMS[0],$this->_SYS_PARAMS[0]);
					$dao1->set_sql_params($params1);
					$row1 = $dao1->execute(1)->row_array();

					$params2 = array($key);
					$dao2->set_sql_params($params2);
					$row2 = $dao2->execute(1)->row_array();

                    $kuota_sekolah[$key] = $row1['tot_kuota'];
                    $pendaftar_sekolah[$key] = $row2['tot_pendaftar'];
				}

				$data['tipe_sekolah_arr'] = $this->tipe_sekolah_arr;
				$data['kuota_sekolah'] = $kuota_sekolah;
				$data['pendaftar_sekolah'] = $pendaftar_sekolah;


			}else if($tab_id==7){

				$data['tipe_sekolah_arr'] = $this->tipe_sekolah_arr;

				$sql = "SELECT SUM(CASE WHEN a.tipe_sekolah=? THEN jml_kuota else 0 END) as tot_kuota 
						FROM
						(SELECT sekolah_id,jml_kuota,'1' AS tipe_sekolah FROM pengaturan_kuota_sma 
						WHERE thn_pelajaran=?
						UNION
						SELECT DISTINCT sekolah_id, SUM(jml_kuota) AS jml_kuota, '2' AS tipe_sekolah FROM pengaturan_kuota_smk 
						WHERE thn_pelajaran=? 
						GROUP BY sekolah_id) AS a";
				
				$dao->set_sql_with_params($sql);
				
				$kuota_sekolah = array();

				foreach($this->tipe_sekolah_arr as $key=>$val){
					
					$params = array($key,$this->_SYS_PARAMS[0],$this->_SYS_PARAMS[0]);
					$dao->set_sql_params($params);
					$query = $dao->execute(1);
                    $row = $query->row_array();

                    $kuota_sekolah[$key] = $row['tot_kuota'];
				}
				
				if($stage=='1'){
					$sub_select = "SELECT * FROM pengaturan_kuota_sma WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."'";
				}else{
					$sub_select = "SELECT DISTINCT sekolah_id,SUM(jml_rombel) as jml_rombel,
								  SUM(kuota_domisili) as kuota_domisili,
								  SUM(kuota_afirmasi) as kuota_afirmasi,
								  SUM(kuota_akademik) as kuota_akademik,
								  SUM(kuota_prestasi) as kuota_prestasi,
								  SUM(kuota_khusus) as kuota_khusus,
								  SUM(jml_kuota) as jml_kuota 
								  FROM pengaturan_kuota_smk 
								  WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' GROUP BY sekolah_id";
				}

				$sql = "SELECT *,(SELECT COUNT(1) FROM pendaftaran_sekolah_pilihan as x WHERE x.sekolah_id=a.sekolah_id AND x.jalur_id='".$path."') as tot_pendaftar 
						FROM (".$sub_select.") as a 
						INNER JOIN (SELECT x.sekolah_id,x.nama_sekolah,x.alamat,y.nama_dt2 FROM sekolah as x 
									LEFT JOIN ref_dt2 as y ON (x.dt2_id=y.dt2_id) WHERE x.tipe_sekolah_id='".$stage."') as b 
						ON (a.sekolah_id=b.sekolah_id)";

				$pengaturan_kuota_sekolah_rows = $dao->execute(0,$sql)->result_array();
				
				$data['kuota_sekolah'] = $kuota_sekolah;
				$data['kuota_jalur'] = $kuota_jalur_row['persen_kuota'] * array_sum($kuota_sekolah)/100;
				$data['pengaturan_kuota_sekolah_rows'] = $pengaturan_kuota_sekolah_rows;
			}

			$data['stage'] = $stage;
			$data['path'] = $path;
			$data['tab_id'] = $tab_id;

			$this->load->view($this->active_controller.'/'.$view,$data);
		}

		

		function get_destSchools(){
			$school_regency = $this->input->post('school_regency');
			$regency = $this->input->post('regency');
			$school_type = $this->input->post('school_type');
			$path = $this->input->post('path');
			$accross_regency = $this->input->post('accross_regency');

			$i = $this->input->post('i');
			
			$dao = $this->global_model->get_dao();			

			if($regency==$school_regency or ($school_type=='2' or $school_type=='3') or $accross_regency=='2')
			{

				$sql = "SELECT a.sekolah_id,a.nama_sekolah 
						FROM sekolah as a 
						LEFT JOIN (SELECT SUM(grand_kuota) as grand_kuota,sekolah_id FROM ".($school_type=='1' || $school_type=='3'?'pengaturan_kuota_sma':'pengaturan_kuota_smk')." GROUP BY sekolah_id) as b ON (a.sekolah_id=b.sekolah_id)
						WHERE a.dt2_id='".$school_regency."' AND b.grand_kuota>0";
			}else{
				$sql = "SELECT a.sekolah_id,b.nama_sekolah FROM pengaturan_sekolah_perbatasan as a 
						LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
						LEFT JOIN (SELECT SUM(grand_kuota) as grand_kuota,sekolah_id FROM ".($school_type=='1' || $school_type=='3'?'pengaturan_kuota_sma':'pengaturan_kuota_smk')." GROUP BY sekolah_id) as c ON (a.sekolah_id=c.sekolah_id)
						WHERE a.dt2_id='".$regency."' AND a.dt2_perbatasan_id='".$school_regency."' 
						AND c.grand_kuota>0";
			}

			$sql .= " AND tipe_sekolah_id='".$school_type."'";
			
			
			$sekolah_rows = $dao->execute(0,$sql)->result_array();
			
			$data['sekolah_rows'] = $sekolah_rows;
			$data['tipe_sekolah'] = $school_type;
			$data['jalur_id'] = $path;
			$data['i'] = $i;
			
			$this->load->view($this->active_controller.'/dest_schools',$data);

		}

		function get_destFields(){
			$x_school = explode('_',$this->input->post('school'));
			$path = $this->input->post('path');
			$i = $this->input->post('i');

			$dao = $this->global_model->get_dao();			

			if(count($x_school)>1){
				$sql = "SELECT a.* FROM kompetensi_smk as a LEFT JOIN pengaturan_kuota_smk as b ON (a.kompetensi_id=b.kompetensi_id) 
						WHERE a.sekolah_id='".$x_school[0]."' AND b.grand_kuota>0";
				$kompetensi_smk_rows = $dao->execute(0,$sql)->result_array();
			}else{
				$kompetensi_smk_rows = array();
			}

			$data['kompetensi_smk_rows'] = $kompetensi_smk_rows;
			$data['jalur_id'] = $path;
			$data['i'] = $i;
			
			$this->load->view($this->active_controller.'/dest_fields',$data);

		}

		function view_school_registrants(){
			$sekolah_id = $this->input->post('school');
			$jalur_id = $this->input->post('path');
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$sql = "SELECT COUNT(1) n_registrants FROM pendaftaran_sekolah_pilihan WHERE sekolah_id='".$sekolah_id."' AND jalur_id='".$jalur_id."'";
			$row1 = $dao->execute(0,$sql)->row_array();

			$sql = "SELECT grand_kuota FROM pengaturan_kuota_sma WHERE sekolah_id='".$sekolah_id."'";
			$row2 = $dao->execute(0,$sql)->row_array();

			$data['jml_pendaftar'] = $row1['n_registrants'];
			$data['jml_kuota'] = $row2['grand_kuota'];

			$this->load->view($this->active_controller.'/school_registrants',$data);
		}

		function view_field_registrants(){
			$kompetensi_id = $this->input->post('field');
			$jalur_id = $this->input->post('path');
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$sql = "SELECT COUNT(1) n_registrants FROM pendaftaran_kompetensi_pilihan WHERE kompetensi_id='".$kompetensi_id."' AND jalur_id='".$jalur_id."'";
			$row1 = $dao->execute(0,$sql)->row_array();

			$sql = "SELECT grand_kuota FROM pengaturan_kuota_smk WHERE kompetensi_id='".$kompetensi_id."'";
			$row2 = $dao->execute(0,$sql)->row_array();

			$data['jml_pendaftar'] = $row1['n_registrants'];
			$data['jml_kuota'] = $row2['grand_kuota'];

			$this->load->view($this->active_controller.'/field_registrants',$data);
		}


		function get_data_linked_toRegency(){
			$regency = $this->input->post('regency');
			$x_regency = explode('_',$regency);			
			$school_type = $this->input->post('school_type');
			$path = $this->input->post('path');
			$accross_regency = $this->input->post('accross_regency');
			$n_schools = $this->input->post('n_schools');

			$dao = $this->global_model->get_dao();			

			$kecamatan_rows = array();
			$pengaturan_dt2_sekolah_rows = array();
			$sekolah_rows = array();

			if(count($x_regency)>1){

				$sql = "SELECT * FROM ref_kecamatan WHERE dt2_id='".$x_regency[1]."'";
				$kecamatan_rows = $dao->execute(0,$sql)->result_array();
				

				if($accross_regency!='2')
				{
					$sql = "SELECT a.dt2_sekolah_id,b.nama_dt2,a.status FROM pengaturan_dt2_sekolah as a INNER JOIN ref_dt2 as b ON (a.dt2_sekolah_id=b.dt2_id) 
							WHERE a.dt2_id='".$x_regency[0]."' ".($accross_regency=='0'?"AND dt2_sekolah_id='".$x_regency[0]."'":"");
				}else{
					$sql = "SELECT dt2_id as dt2_sekolah_id,nama_dt2 FROM ref_dt2";
				}
								
				$pengaturan_dt2_sekolah_rows = $dao->execute(0,$sql)->result_array();

				$sql = "SELECT a.sekolah_id,a.nama_sekolah 
						FROM sekolah as a 
						LEFT JOIN (SELECT SUM(grand_kuota) as grand_kuota,sekolah_id FROM ".($school_type=='1'?'pengaturan_kuota_sma':'pengaturan_kuota_smk')." GROUP BY sekolah_id) as b ON (a.sekolah_id=b.sekolah_id)
						WHERE a.dt2_id='".$x_regency[0]."' AND b.grand_kuota>0 
						AND a.tipe_sekolah_id='".$school_type."'";
				
				$sekolah_rows = $dao->execute(0,$sql)->result_array();			
				

			}
			
			$data['kecamatan_rows'] = $kecamatan_rows;
			$data['pengaturan_dt2_sekolah_rows'] = $pengaturan_dt2_sekolah_rows;
			$data['sekolah_rows'] = $sekolah_rows;
			$data['tipe_sekolah'] = $school_type;
			$data['jalur_id'] = $path;
			$data['jml_sekolah'] = $n_schools;
			$data['dt2_id'] = $x_regency[0];
			$data['lintas_dt2'] = $accross_regency;
			
			$this->load->view($this->active_controller.'/data_linked_toRegency',$data);
		}

		function generate_seri_numb($reg_time,$stage,$path){
			$x_reg_time = explode(' ',$reg_time);
			$x_date = explode('-',$x_reg_time[0]);
			$x_time = explode(':',$x_reg_time[1]);

			$shuffled = str_shuffle($x_date[2].$x_date[1].$x_date[0].$x_time[2].$x_time[1].$x_time[0].$stage.$path);
			return $shuffled;
		}

		function submit_reg(){

			$this->load->helper(array('date_helper','mix_helper'));

			$this->load->model(array('pendaftaran_model','pendaftaran_dokumen_kelengkapan_model','pendaftaran_jalur_pilihan_model',
									 'pendaftaran_sekolah_pilihan_model','pendaftaran_kompetensi_pilihan_model',
									 'pendaftaran_nilai_un_model','pendaftaran_prestasi_model','log_status_pendaftaran_model'));

			$jalur_pendaftaran = str_replace("'","\'",$this->input->post('input_jalur_pendaftaran'));
			$tipe_sekolah = $this->input->post('input_tipe_sekolah');
			$no_peserta = $this->input->post('input_no_peserta');
			$nama = str_replace("'","\'",$this->input->post('input_nama'));
			$jk = $this->input->post('input_jk');
			$sekolah_asal = str_replace("'","\'",$this->input->post('input_sekolah_asal'));
			$tpt_lahir = str_replace("'","\'",$this->input->post('input_tpt_lahir'));
			$tgl_lahir = us_date_format($this->input->post('input_sekolah_asal'));
			$alamat = str_replace("'","\'",$this->input->post('input_alamat'));

			try{

				$x_dt2 = explode('_',$this->input->post('input_dt2'));
				$dt2_id = $x_dt2[0];
				$nm_dt2 = $x_dt2[2];

				$x_kecamatan = explode('_',$this->security->xss_clean($this->input->post('input_kecamatan')));
				$kecamatan_id = $x_kecamatan[0];
				$nm_kecamatan = (count($x_kecamatan)>1?$x_kecamatan[1]:'');

				$no_telp = $this->security->xss_clean($this->input->post('input_no_telp'));

				$jml_sekolah = $this->input->post('input_jml_sekolah');
				$n_berkas = $this->input->post('input_n_berkas');
				
				$nil_bhs_indonesia = $this->input->post('input_nil_bhs_indonesia');
				$nil_bhs_inggris = $this->input->post('input_nil_bhs_inggris');
				$nil_matematika = $this->input->post('input_nil_matematika');
				$nil_ipa = $this->input->post('input_nil_ipa');			
				$tot_nilai = $this->input->post('input_tot_nilai');

				$mode_un = $this->input->post('input_mode_un');
				

				//VALIDATING HERE

				//check school/field chosen
				$chosen = array();
				$input_name = ($tipe_sekolah=='1'?'input_sekolah_tujuan':'input_kompetensi_tujuan');

				$filled = 0;
		   		for($i=1;$i<=$jml_sekolah;$i++){
		   			$val = $this->input->post($input_name.$i);
		   			$filled += ($val!=''?1:0);
		   		}

		   		if($filled>1){
					for($i=1;$i<=$jml_sekolah;$i++)
					{
						$val = $this->input->post($input_name.$i);
						if(!in_array($val,$chosen)){
							$chosen[] = $val;
						}else{
							die('ERROR: '.($tipe_sekolah=='1'?'Sekolah':'Kompetensi').' pilihan harus berbeda');
						}
					}
				}

				//check id_pendaftaran
				$dao = $this->global_model->get_dao();
				if($jalur_pendaftaran=='5')
				{
					//check no_peserta
					$sql = "SELECT count(id_pendaftaran) n_row FROM pendaftaran WHERE id_pendaftaran='".$no_peserta."'";

					$row = $dao->execute(0,$sql)->row_array();
					if($row['n_row']>0)
						die('ERROR:No. Peserta sudah digunakan!');
				}

				
				//begin transaction
				$this->db->trans_begin();

				$no_registrasi = $this->generate_regnumb($dt2_id,$tipe_sekolah,$jalur_pendaftaran);
				$wkt_pendaftaran = date('Y-m-d H:i:s');
				$no_seri = $this->generate_seri_numb($wkt_pendaftaran,$tipe_sekolah,$jalur_pendaftaran);
				$passphrase = generatePassword(8);

				//update pendaftaran
				$m = $this->pendaftaran_model;
				
				$m->set_no_telp($no_telp);
				$m->set_dt2_kk($dt2_id);
				$m->set_kecamatan_id($kecamatan_id);
				$m->set_alamat($alamat);			
				$m->set_nil_bhs_indonesia($nil_bhs_indonesia);
				$m->set_nil_bhs_inggris($nil_bhs_inggris);
				$m->set_nil_matematika($nil_matematika);
				$m->set_nil_ipa($nil_ipa);			
				$m->set_tot_nilai($tot_nilai);
				$m->set_mode_un($mode_un);
				$m->set_waktu_pendaftaran($wkt_pendaftaran);
				$m->set_status('1');
				$m->set_no_pendaftaran($no_registrasi);
				$m->set_no_seri($no_seri);
				$m->set_passphrase($passphrase);
				$m->set_show_passphrase('1');

				if(($jalur_pendaftaran!='5' and $tipe_sekolah!='3') or ($tipe_sekolah=='3' and !is_null($this->session->userdata('nopes'))))
				{
					$result = $dao->update($m,array('id_pendaftaran'=>$no_peserta));
				}else{
					$m->set_id_pendaftaran($no_peserta);
					$m->set_nama($nama);
					$m->set_jk($jk);
					$m->set_tpt_lahir($tpt_lahir);
					$m->set_tgl_lahir($tpt_lahir);
					$m->set_alamat($alamat);				
					$m->set_dt2_id($dt2_id);
					$m->set_sekolah_asal($sekolah_asal);
					$result = $dao->insert($m);
				}

				if(!$result){				
					$this->db->trans_rollback();				
					die('failed');
				}

				//insert pendaftaran_jalur_pilihan
				$m = $this->pendaftaran_jalur_pilihan_model;
				$m->set_id_pendaftaran($no_peserta);
				$m->set_jalur_id($jalur_pendaftaran);
				$m->set_tipe_sekolah_id($tipe_sekolah);			
							
				$result = $dao->insert($m);			

				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				//insert pendaftaran_sekolah_pilihan
				$nmSekolah_pilihan_arr = array();
				$m1 = $this->pendaftaran_sekolah_pilihan_model;
				$m1->set_id_pendaftaran($no_peserta);
				
				if($tipe_sekolah=='1' or $tipe_sekolah=='3')
				{
					for($i=1;$i<=$jml_sekolah;$i++){
						$sekolah_pilihan = $this->input->post('input_sekolah_tujuan'.$i);
						
						if($sekolah_pilihan!='')
						{
							$x_sekolah_pilihan = explode('_',$sekolah_pilihan);					

							$m1->set_sekolah_id($x_sekolah_pilihan[0]);
							$m1->set_jalur_id($jalur_pendaftaran);
							$m1->set_pilihan_ke($i);
							$m1->set_status('0');

							$result = $dao->insert($m1);
							if(!$result){
								$this->db->trans_rollback();
								die('failed');
							}

							$nmSekolah_pilihan_arr[] = $x_sekolah_pilihan[1];


						}
					}
				}else{
					$m2 = $this->pendaftaran_kompetensi_pilihan_model;
					$m2->set_id_pendaftaran($no_peserta);

					$sekolah_pilihan_arr = array();
					$kompetensi_pilihan_arr = array();
					$j = 0;
					
					for($i=1;$i<=$jml_sekolah;$i++){

						$sekolah_pilihan = $this->input->post('input_sekolah_tujuan'.$i);
						$kompetensi_pilihan = $this->input->post('input_kompetensi_tujuan'.$i);
						
						if($sekolah_pilihan!='' && $kompetensi_pilihan!='')
						{
							$x_sekolah_pilihan = explode('_',$sekolah_pilihan);
							$x_kompetensi_pilihan = explode('_',$kompetensi_pilihan);
							
							if(!in_array($x_sekolah_pilihan[0],$sekolah_pilihan_arr))
							{
								$j++;

								$sekolah_pilihan_arr[] = $x_sekolah_pilihan[0];
								$m1->set_sekolah_id($x_sekolah_pilihan[0]);
								$m1->set_jalur_id($jalur_pendaftaran);
								$m1->set_pilihan_ke($j);
								$m1->set_status('0');

								$result = $dao->insert($m1);
								if(!$result){
									$this->db->trans_rollback();
									die('failed');
								}

							}

							$m2->set_sekolah_id($x_sekolah_pilihan[0]);
							$m2->set_jalur_id($jalur_pendaftaran);
							$m2->set_kompetensi_id($x_kompetensi_pilihan[0]);
							$m2->set_pilihan_ke($i);
							$m2->set_status('0');

							$result = $dao->insert($m2);
							if(!$result){
								$this->db->trans_rollback();
								die('failed');
							}

							$nmSekolah_pilihan_arr[] = $x_sekolah_pilihan[1]." (".$x_kompetensi_pilihan[1].")";
						}
					}
				}

				//insert pendaftaran_dokumen_kelengkapan
				$m = $this->pendaftaran_dokumen_kelengkapan_model;
				$m->set_id_pendaftaran($no_peserta);
				$m->set_status('0');

				for($i=1;$i<=$n_berkas;$i++){
					if(!is_null($this->input->post('input_berkas'.$i))){
						
						$berkas = $this->input->post('input_berkas'.$i);
						$m->set_dokumen($berkas);
						$result = $dao->insert($m);
						if(!$result){
							$this->db->trans_rollback();
							die('failed');
						}


					}
				}

				//insert pendaftaran_nilai_un
				$m = $this->pendaftaran_nilai_un_model;
				$m->set_id_pendaftaran($no_peserta);			
				$m->set_nil_bhs_indonesia($nil_bhs_indonesia);
				$m->set_nil_bhs_inggris($nil_bhs_inggris);
				$m->set_nil_matematika($nil_matematika);
				$m->set_nil_ipa($nil_ipa);			
				$m->set_tot_nilai($tot_nilai);
				$result = $dao->insert($m);
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				//insert pendaftaran_prestasi
				if($jalur_pendaftaran=='4'){

					$n_tingkat_kejuaraan = $this->input->post('input_n_tingkat_kejuaraan');

					$m = $this->pendaftaran_prestasi_model;
					$m->set_id_pendaftaran($no_peserta);
					
					for($i=1;$i<=$n_tingkat_kejuaraan;$i++){

						if(!is_null($this->input->post('input_tingkat_kejuaraan'.$i))){

							$tingkat_kejuaraan = $this->input->post('input_tingkat_kejuaraan'.$i);

							$m->set_tkt_kejuaraan_id($tingkat_kejuaraan);

							$n_prestasi = $this->input->post('input_n_prestasi'.$i);


							for($j=1;$j<=$n_prestasi;$j++){

								if(!is_null($this->input->post('input_bidang'.$i.'_'.$j)))
								{

									$bidang = $this->input->post('input_bidang'.$i.'_'.$j);
									$nm_kejuaraan = $this->input->post('input_nm_kejuaraan'.$i.'_'.$j);
									$penyelenggara = $this->input->post('input_penyelenggara'.$i.'_'.$j);
									$no_sertifikat = $this->input->post('input_no_sertifikat'.$i.'_'.$j);
									
									$peringkat = $this->input->post('input_peringkat'.$i.'_'.$j);
									$thn_kejuaraan = $this->input->post('input_thn_kejuaraan'.$i.'_'.$j);								

									$m->set_bdg_kejuaraan_id($bidang);
									$m->set_nm_kejuaraan($nm_kejuaraan);
									$m->set_penyelenggara($penyelenggara);
									$m->set_no_sertifikat($no_sertifikat);
									$m->set_peringkat($peringkat);
									$m->set_thn_kejuaraan($thn_kejuaraan);
									$m->set_status('0');
									
									$result = $dao->insert($m);

									if(!$result){
										$this->db->trans_rollback();
										die('failed');
									}
								}

							}

						}
					}
				}
				//insert registration log
				$m = $this->log_status_pendaftaran_model;
				$m->set_id_pendaftaran($no_peserta);
				$m->set_thn_pelajaran($this->_SYS_PARAMS[0]);
				$m->set_status('0');
				$m->set_jalur_id($jalur_pendaftaran);
				$m->set_created_time($wkt_pendaftaran);
				$m->set_user($this->session->userdata('nopes'));

				$result = $dao->insert($m);
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$this->db->trans_commit();
				//end of transaction

				$data = $this->prepare_registration_data($no_peserta);
				
				$this->load->view($this->active_controller.'/'.$this->tabs_view[8],$data);

			}catch(Exception $e){
				die('failed');
			}

		}

		function upload_photo(){

			$this->load->helper('mix_helper');

			$upload_dir = FCPATH.'upload'.DIRECTORY_SEPARATOR.'registration';
			$error = false;

			if(isset($_GET['files']))
			{				
				$FILES = $_FILES[0];

				if($FILES['error']==0)
				{
					$tmp_name = $FILES['tmp_name'];	
					$ext = get_extension($FILES['name']);
					
					$file_name = $this->session->userdata('nopes').'_'.date('ymdHi').'.'.$ext;
					$fullpath_name = $upload_dir.DIRECTORY_SEPARATOR.$file_name;
					
					move_uploaded_file($tmp_name, $fullpath_name);

					$files = array($file_name);
					
				}else{
					$error = true;
					$err_msg = 'Terjadi kesalahan saat mengupload file';
				}
				
				$data = ($error) ? array('error' => $err_msg) : array('files' => $files);

				echo json_encode($data);

			}else{
				
				$this->load->model(array('pendaftaran_model'));

				$filename = $_POST['filename'];				

				$dao = $this->global_model->get_dao();
				$m = $this->pendaftaran_model;
				$m->set_gambar($filename);
				$result = $dao->update($m,array('id_pendaftaran'=>$this->session->userdata('nopes')));

				if(!$result){
					die('ERROR: terjadi kesalahan saat mengunggah foto!');
				}

				$dt_session = array('gambar'=>$filename);
				$this->session->set_userdata($dt_session);

				$this->load->view($this->active_controller.'/registration_photo');

			}

		}

		private function generate_regnumb($dt2_id,$stage,$path){
			
			$new_numb = date('y').'.'.substr($dt2_id,2,2).'.'.$stage.$path;

			$dao = $this->global_model->get_dao();
			$row = $dao->execute(0,"SELECT MAX(no_pendaftaran) last_numb FROM pendaftaran WHERE no_pendaftaran LIKE '".$new_numb."%'")->row_array();

			$new_order = 1;
			if(!empty($row['last_numb'])){
				$last_order = substr($row['last_numb'],9,4);
				$new_order += $last_order;
			}

			$new_numb .= '.'.sprintf('%04s',$new_order);

			return $new_numb;
		}

		private function get_registration_data($nopes){
			
			$dao = $this->global_model->get_dao();

			$sql = "SELECT a.id_pendaftaran,a.nama,a.jk,a.sekolah_asal,
					a.alamat,b.nama_kecamatan,c.nama_dt2,d.nama_jalur,a.passphrase,a.show_passphrase,
					d.nama_tipe_sekolah,d.akronim,a.no_pendaftaran ,d.tipe_sekolah_id,
					DATE_FORMAT(a.waktu_pendaftaran,'%Y-%m-%d') as tgl_pendaftaran,a.no_seri
					FROM pendaftaran as a 
					LEFT JOIN ref_kecamatan as b ON (a.kecamatan_id=b.kecamatan_id) 
					LEFT JOIN ref_dt2 as c ON (a.dt2_kk=c.dt2_id) 
					LEFT JOIN (SELECT w.id_pendaftaran,x.nama_jalur,y.nama_tipe_sekolah,y.akronim,w.tipe_sekolah_id FROM pendaftaran_jalur_pilihan as w 
						LEFT JOIN ref_jalur_pendaftaran as x ON (w.jalur_id=x.ref_jalur_id) 
						LEFT JOIN ref_tipe_sekolah as y ON (w.tipe_sekolah_id=y.ref_tipe_sklh_id)) as d ON (a.id_pendaftaran=d.id_pendaftaran)
					WHERE a.id_pendaftaran='".$nopes."';";

			$registrasi_row = $dao->execute(0,$sql)->row_array();

			if($registrasi_row['tipe_sekolah_id']=='1' or $registrasi_row['tipe_sekolah_id']=='3')
			{
				$sql = "SELECT b.nama_sekolah FROM pendaftaran_sekolah_pilihan as a LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
						WHERE id_pendaftaran='".$nopes."' ORDER BY pilihan_ke ASC";
			}else{
				$sql = "SELECT b.nama_sekolah,c.nama_kompetensi FROM pendaftaran_kompetensi_pilihan as a 
						LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
						LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id)
						WHERE a.id_pendaftaran='".$nopes."' ORDER BY pilihan_ke ASC";


			}
			
			$sekolah_pilihan_rows = $dao->execute(0,$sql)->result_array();
			$sekolah_pilihan_arr = array();

			foreach($sekolah_pilihan_rows as $row)
			{
					$sekolah_pilihan_arr[] = $row['nama_sekolah'].($registrasi_row['tipe_sekolah_id']=='2'?" (".$row['nama_kompetensi'].")":"");
			}

			return array($registrasi_row,$sekolah_pilihan_arr);
		}

		private function prepare_registration_data($nopes){			

			$registration_data = $this->get_registration_data($nopes);

			$registrasi_row = $registration_data[0];
			$sekolah_pilihan_arr = $registration_data[1];
			
			$encoded_nopes = base64_encode($nopes);
			
			$data['no_peserta'] = $registrasi_row['id_pendaftaran'];
			$data['nama'] = $registrasi_row['nama'];
			$data['jk'] = $registrasi_row['jk'];
			$data['sekolah_asal'] = $registrasi_row['sekolah_asal'];;
			$data['alamat'] = $registrasi_row['alamat'];;
			$data['kecamatan'] = $registrasi_row['nama_kecamatan'];;
			$data['nm_dt2'] = $registrasi_row['nama_dt2'];
			$data['sekolah_pilihan_arr'] = $sekolah_pilihan_arr;
			$data['jalur_pendaftaran']['nama_jalur'] = $registrasi_row['nama_jalur'];
			$data['tipe_sekolah'] = array('nama_tipe_sekolah'=>$registrasi_row['nama_tipe_sekolah'],'akronim'=>$registrasi_row['akronim']);
			$data['no_registrasi'] = $registrasi_row['no_pendaftaran'];
			$data['tgl_pendaftaran'] = $registrasi_row['tgl_pendaftaran'];
			$data['no_seri'] = $registrasi_row['no_seri'];
			$data['encoded_nopes'] = $encoded_nopes;
			$data['passphrase'] = $registrasi_row['passphrase'];
			$data['show_passphrase'] = $registrasi_row['show_passphrase'];

			return $data;
		}

		function reg_data_pdf($encoded_nopes){			
			$this->load->helper('date_helper');
			
			$decoded_nopes = base64_decode(urldecode($encoded_nopes));
			
			$mpdf = new \Mpdf\Mpdf();

			$data = $this->prepare_registration_data($decoded_nopes);
			$data['mpdf'] = $mpdf;

			$this->load->view($this->active_controller.'/reg_data_pdf',$data);

		}

		function reg_data_print($encoded_nopes){
			$this->load->helper('date_helper');

			$decoded_nopes = base64_decode(urldecode($encoded_nopes));
			
			$data = $this->prepare_registration_data($decoded_nopes);			
            $data['sys_params'] = $this->_SYS_PARAMS;
			$this->load->view($this->active_controller.'/reg_data_print',$data);
		}

		function check_regid(){

			$this->load->model(array('global_model'));
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			$regid = $this->input->post('regid');

			$row = $dao->execute(0,"SELECT count(1) n FROM pendaftaran WHERE id_pendaftaran='".$regid."'")->row_array();				

			if($row['n']>0){
				echo 'exists';
			}else{
				echo 'empty';
			}

		}

	}
?>

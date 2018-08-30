<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	require_once APPPATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'Backoffice_parent.php';

	class administration extends Backoffice_parent{

		public $active_controller;

		function __construct(){
			parent::__construct();
			$this->active_controller = __CLASS__;
		}


		//SCHOOL FUNCTION PAKET
		function school(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/school');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);

			if($read_access){
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['dt2_rows'] = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'")->result_array();
				$data['form_id'] = "search-school-form";
				$data['active_controller'] = $this->active_controller;
				$data['containsTable'] = true;
				$data['add_access'] = $add_access;

				$this->backoffice_template->render($this->active_controller.'/school/index',$data);
			}else{
				$this->error_403();
			}
		}

		function search_school_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/school');			
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			$search_dt2 = $this->input->post('search_dt2');

			$data2['update_access'] = $update_access;
			$data2['delete_access'] = $delete_access;
			$data2['rows'] = $this->get_school_data($search_dt2);
			$data2['search_dt2'] = $search_dt2;

			$data['list_of_data'] = $this->load->view($this->active_controller.'/school/list_of_data',$data2,true);

			$this->load->view($this->active_controller.'/school/data_view',$data);
		}

		function get_school_data($dt2){
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$cond = "";
			if(!empty($dt2))
				$cond = "WHERE a.dt2_id='".$dt2."'";

			$sql = "SELECT a.*,b.nama_dt2,c.akronim as jenjang FROM sekolah as a 
					LEFT JOIN ref_dt2 as b ON (a.dt2_id=b.dt2_id) 
					LEFT JOIN ref_tipe_sekolah as c ON (a.tipe_sekolah_id=c.ref_tipe_sklh_id)
					".$cond;
			
			$rows = $dao->execute(0,$sql)->result_array();
			return $rows;
		}

		function load_school_form(){
			$this->aah->check_access();

			$this->load->model(array('sekolah_model'));
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$act = $this->input->post('act');
			$search_dt2 = $this->input->post('search_dt2');

			$id_name = 'sekolah_id';

		    $m = $this->sekolah_model;
		    $id_value = ($act=='edit'?$this->input->post('id'):'');
		    $curr_data = $dao->get_data_by_id($act,$m,$id_value);
		    
		    $new_schoolId = $this->global_model->get_incrementID('sekolah_id','sekolah');

		    $data['sekolah_id'] = ($act=='add'?$new_schoolId:$id_value);
		    $data['jenjang_opts'] = $dao->execute(0,"SELECT * FROM ref_tipe_sekolah")->result_array();
		    $data['dt2_opts'] = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'")->result_array();
		    $data['curr_data'] = $curr_data;
		    $data['active_controller'] = $this->active_controller;
		    $data['form_id'] = 'school-form';
		    $data['id_value'] = $id_value;
		    $data['act'] = $act;
		    $data['search_dt2'] = $search_dt2;

			$this->load->view($this->active_controller.'/school/form_content',$data);
		}

		function submit_school_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/school');
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);
			$act = $this->input->post('act');

			if(($act=='add' and $add_access) or ($act=='edit' and $update_access))
			{
				$this->load->model(array('sekolah_model'));

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				
				$search_dt2 = $this->input->post('search_dt2');

				$tipe_sekolah_id = $this->security->xss_clean($this->input->post('input_tipe_sekolah_id'));
				$nama_sekolah = $this->security->xss_clean($this->input->post('input_nama_sekolah'));
				$dt2_id = $this->security->xss_clean($this->input->post('input_dt2_id'));
				$alamat = $this->security->xss_clean($this->input->post('input_alamat'));
				$telepon = $this->security->xss_clean($this->input->post('input_telepon'));
				$email = $this->security->xss_clean($this->input->post('input_email'));				

				$m = $this->sekolah_model;

				$m->set_tipe_sekolah_id($tipe_sekolah_id);
				$m->set_nama_sekolah($nama_sekolah);
				$m->set_dt2_id($dt2_id);
				$m->set_alamat($alamat);
				$m->set_telepon($telepon);
				$m->set_email($email);

				if($act=='add')
				{
					$sekolah_id = $this->security->xss_clean($this->input->post('input_sekolah_id'));
					
					$m->set_sekolah_id($sekolah_id);

					$result = $dao->insert($m);
					$label = 'menyimpan';
				}
				else
				{
					$id = $this->input->post('id');
					$result = $dao->update($m,array('sekolah_id'=>$id));
					$label = 'merubah';
				}

				if(!$result)
				{
					die('ERROR: gagal '.$label.' data');
				}

				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_school_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/school/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk '.($act=='add'?'menambah':'merubah').' data!';
			}

		}

		function delete_school_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/school');
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($delete_access)
			{
				$this->load->model(array('sekolah_model'));

				$id = $this->input->post('id');
				$search_dt2 = $this->input->post('search_dt2');

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$m = $this->sekolah_model;
				$result = $dao->delete($m,array('sekolah_id'=>$id));
				if(!$result){
					die('ERROR: gagal menghapus data');
				}
				
				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_school_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/school/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk menghapus data!';
			}
		}

		//END OF SCHOOL FUNCTION PACKET

		//FIELD FUNCTION PAKET
		function field(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/field');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);

			if($read_access)
			{
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['dt2_rows'] = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'")->result_array();
				$data['form_id'] = "search-school-form";
				$data['active_controller'] = $this->active_controller;
				$data['containsTable'] = true;
				$data['add_access'] = $add_access;
				$this->backoffice_template->render($this->active_controller.'/field/index',$data);
			}else{
				$this->error_403();
			}
		}

		function search_field_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/field');			
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			$search_dt2 = $this->input->post('search_dt2');

			$data2['update_access'] = $update_access;
			$data2['delete_access'] = $delete_access;
			$data2['rows'] = $this->get_field_data($search_dt2);
			$data2['search_dt2'] = $search_dt2;

			$data['list_of_data'] = $this->load->view($this->active_controller.'/field/list_of_data',$data2,true);

			$this->load->view($this->active_controller.'/field/data_view',$data);
		}

		function get_field_data($dt2){
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$cond = "";
			if(!empty($dt2))
				$cond = "WHERE dt2_id='".$dt2."'";

			$sql = "SELECT a.*,b.nama_sekolah FROM kompetensi_smk as a 
					INNER JOIN (SELECT sekolah_id,nama_sekolah FROM sekolah ".$cond.") as b ON (a.sekolah_id=b.sekolah_id)";
			
			$rows = $dao->execute(0,$sql)->result_array();
			return $rows;
		}

		function load_field_form(){
			$this->aah->check_access();

			$this->load->model(array('kompetensi_smk_model'));
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$act = $this->input->post('act');
			$search_dt2 = $this->input->post('search_dt2');

			$id_name = 'kompetensi_id';

		    $m = $this->kompetensi_smk_model;
		    $id_value = ($act=='edit'?$this->input->post('id'):'');
		    $curr_data = $dao->get_data_by_id($act,$m,$id_value);

		    $dt2_opts = array();
		    $sekolah_opts = array();		    

		    $new_fieldId = $this->global_model->get_incrementID('kompetensi_id','kompetensi_smk');

		    $sekolah_opts = array();
		    $dt2_id = '';
		    if($act=='edit'){
		    	$row = $dao->execute(0,"SELECT dt2_id FROM sekolah WHERE sekolah_id='".$curr_data['sekolah_id']."'")->row_array();
	    		$sql = "SELECT sekolah_id,nama_sekolah FROM sekolah 
	    				WHERE dt2_id='".$row['dt2_id']."'";
	    		$sekolah_opts = $dao->execute(0,$sql)->result_array();
	    		$dt2_id = $row['dt2_id'];
		    }

		    $data['kompetensi_id'] = ($act=='add'?$new_fieldId:$id_value);
		    $data['dt2_opts'] = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'")->result_array();
		    $data['sekolah_opts'] = $sekolah_opts;
		    $data['dt2_id'] = $dt2_id;
		    $data['curr_data'] = $curr_data;
		    $data['active_controller'] = $this->active_controller;
		    $data['form_id'] = 'field-form';
		    $data['id_value'] = $id_value;
		    $data['act'] = $act;
		    $data['search_dt2'] = $search_dt2;

			$this->load->view($this->active_controller.'/field/form_content',$data);
		}

		function submit_field_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/field');
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);
			$act = $this->input->post('act');

			if(($act=='add' and $add_access) or ($act=='edit' and $update_access))
			{
				$this->load->model(array('kompetensi_smk_model'));

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				
				$search_dt2 = $this->input->post('search_dt2');

				$nama_kompetensi = $this->security->xss_clean($this->input->post('input_nama_kompetensi'));
				$sekolah_id = $this->security->xss_clean($this->input->post('input_sekolah_id'));

				$m = $this->kompetensi_smk_model;

				$m->set_nama_kompetensi($nama_kompetensi);
				$m->set_sekolah_id($sekolah_id);

				if($act=='add')
				{
					$kompetensi_id = $this->security->xss_clean($this->input->post('input_kompetensi_id'));
					
					$m->set_kompetensi_id($kompetensi_id);

					$result = $dao->insert($m);				
					$label = 'menyimpan';
				}
				else
				{
					$id = $this->input->post('id');
					$result = $dao->update($m,array('kompetensi_id'=>$id));
					$label = 'merubah';
				}

				if(!$result)
				{
					die('ERROR: gagal '.$label.' data');
				}

				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_field_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/field/list_of_data',$data);

			}else{
				echo 'ERROR: anda tidak diijinkan untuk '.($act=='add'?'menambah':'merubah').' data!';
			}

		}

		function delete_field_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/field');
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($delete_access)
			{
				$this->load->model(array('kompetensi_smk_model'));

				$id = $this->input->post('id');
				$search_dt2 = $this->input->post('search_dt2');

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$m = $this->kompetensi_smk_model;
				$result = $dao->delete($m,array('kompetensi_id'=>$id));
				if(!$result){
					die('ERROR: gagal menghapus data');
				}

				$data['rows'] = $this->get_field_data($search_dt2);
				$data['search_dt2'] = $search_dt2;
				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;

				$this->load->view($this->active_controller.'/field/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk menghapus data!';
			}
		}

		//END OF FIELD FUNCTION PACKET

		function reset_registration(){
			$this->aah->check_access();
			
			$data['form_id'] = 'search_form';
			$data['active_url'] = str_replace('::','/',__METHOD__);
			$this->backoffice_template->render($this->active_controller.'/reset_registration/index',$data);
		}


		function search_registration_for_reset(){
			$this->aah->check_access();

			$error = 0;
			$dao = $this->global_model->get_dao();

			$this->load->helper(array('date_helper','mix_helper'));
			$id_pendaftaran = $this->security->xss_clean($this->input->post('src_registrasi'));

			$sql = "SELECT a.*,b.nama_jalur,b.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim
					FROM pendaftaran as a 
					LEFT JOIN (SELECT x.id_pendaftaran,x.tipe_sekolah_id,y.nama_jalur,z.nama_tipe_sekolah,z.akronim
									FROM pendaftaran_jalur_pilihan as x 
									LEFT JOIN ref_jalur_pendaftaran as y ON (x.jalur_id=y.ref_jalur_id)
									LEFT JOIN ref_tipe_sekolah as z ON (x.tipe_sekolah_id=z.ref_tipe_sklh_id)
									) as b 
					ON (a.id_pendaftaran=b.id_pendaftaran)
					WHERE a.id_pendaftaran='".$id_pendaftaran."';";
						
			$pendaftaran_row = $dao->execute(0,$sql)->row_array();
			
			$sekolah_pilihan_rows = array();
			$sekolah_id = $this->session->userdata('sekolah_id');
			$tipe_sekolah = $this->session->userdata('tipe_sekolah_id');

			if(count($pendaftaran_row)>0){

				$sql = "SELECT COUNT(1) as n FROM pendaftaran_sekolah_pilihan as a 
						WHERE a.id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."' AND a.status!='0'";
				
				$row = $dao->execute(0,$sql)->row_array();
				
				if($row['n']==0){
					if($tipe_sekolah=='1')
					{
						$sql = "SELECT b.nama_sekolah FROM pendaftaran_sekolah_pilihan as a LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)";
					}else{
						$sql = "SELECT b.nama_kompetensi,c.nama_sekolah FROM pendaftaran_kompetensi_pilihan as a LEFT JOIN kompetensi_smk as b ON (a.kompetensi_id=b.kompetensi_id) 
								LEFT JOIN sekolah as c ON (a.sekolah_id=c.sekolah_id)";
					}
					$sql .= " WHERE a.id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."'";
					$sekolah_pilihan_rows = $dao->execute(0,$sql)->result_array();
				}else{
					$error = 2;
				}

			}else{
				$error = 1;
			}

			$data['error'] = $error;
			$data['pendaftaran_row'] = $pendaftaran_row;
			$data['sekolah_pilihan_rows'] = $sekolah_pilihan_rows;

			$this->load->view($this->active_controller.'/reset_registration/form',$data);
		}



		function submit_reset_registration(){
			$this->aah->check_access();


			$this->load->model(array('pendaftaran_model','pendaftaran_jalur_pilihan_model','pendaftaran_sekolah_pilihan_model',
									 'pendaftaran_kompetensi_pilihan_model','pendaftaran_nilai_un_model','pendaftaran_prestasi_model',
									 'pendaftaran_dokumen_kelengkapan_model'));

			$id_pendaftaran = $this->input->post('reset_id_pendaftaran');
			$no_pendaftaran = $this->input->post('reset_no_pendaftaran');
			$jalur_id = $this->input->post('reset_jalur_id');
			$nama_jalur = $this->input->post('reset_nama_jalur');			
			$nama = $this->input->post('reset_nama');
			$jk = $this->input->post('reset_jk');
			$alamat = $this->input->post('reset_alamat');
			$sekolah_asal = $this->input->post('reset_sekolah_asal');			
			$tipe_sekolah = $this->input->post('reset_tipe_sekolah');
			$act = $this->input->post('act');

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$m1 = $this->pendaftaran_model;
			$arr_m = array();
			$arr_m[] = $this->pendaftaran_jalur_pilihan_model;
			$arr_m[] = $this->pendaftaran_sekolah_pilihan_model;
			$arr_m[] = $this->pendaftaran_kompetensi_pilihan_model;
			$arr_m[] = $this->pendaftaran_nilai_un_model;
			$arr_m[] = $this->pendaftaran_prestasi_model;
			$arr_m[] = $this->pendaftaran_dokumen_kelengkapan_model;

			$this->db->trans_begin();
			
			if($act=='reset'){
				$params = array('id_pendaftaran'=>$id_pendaftaran);

				$m1->set_status('0');
				$m1->set_no_pendaftaran('');
				$m1->set_no_seri('');
				$m1->set_passphrase('0');
				$m1->set_show_passphrase('0');

				$result = $dao->update($m1,$params);
				
				
				foreach($arr_m as $m){
					
					$result = $dao->delete($m,$params);
					if(!$result){
						$this->db->trans_rollback();
						die('ERROR: gagal mereset pendaftaran');
					}

				}				
				
				$data['id_pendaftaran'] = $id_pendaftaran;
				$data['no_pendaftaran'] = $no_pendaftaran;
				$data['nama'] = $nama;
				$data['jk'] = $jk;
				$data['sekolah_asal'] = $sekolah_asal;			
				$data['tipe_sekolah'] = $tipe_sekolah;
				$data['nama_jalur'] = $nama_jalur;
				$data['alamat'] = $alamat;				
			}else{
				$params = array('id_pendaftaran'=>$id_pendaftaran);
				$arr_m[count($arr_m)] = $this->pendaftaran_model;
				foreach($arr_m as $m){
					
					$result = $dao->delete($m,$params);
					if(!$result){
						$this->db->trans_rollback();
						die('ERROR: gagal mereset pendaftaran');
					}

				}
				$data['id_pendaftaran'] = $id_pendaftaran;
				$data['nama'] = $nama;
				$data['jk'] = $jk;
				$data['sekolah_asal'] = $sekolah_asal;			
				$data['tipe_sekolah'] = $tipe_sekolah;
				$data['alamat'] = $alamat;				

			}

			$this->db->trans_commit();
			//end of transaction

			$data['act'] = $act;
			$this->load->view($this->active_controller.'/reset_registration/reset_result',$data);

		}

		function cancel_settlement(){
			$this->aah->check_access();
						
			$data['form_id'] = 'search_form';
			$data['active_url'] = str_replace('::','/',__METHOD__);
			$this->backoffice_template->render($this->active_controller.'/cancel_settlement/index',$data);
		}

		function search_settlement_for_cancel(){
			$this->aah->check_access();

			$error = 0;
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$no_peserta = $this->input->post('src_settlement');
			$tipe_sekolah = $this->session->userdata('tipe_sekolah');

			$daftar_ulang_row = $dao->execute(0,"SELECT a.*,b.nama,b.sekolah_asal,b.alamat,b.jk FROM daftar_ulang as a 
												 LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
												 WHERE a.id_pendaftaran='".$no_peserta."'")->row_array();			
			$pilihan_row = array();
			if(count($daftar_ulang_row)>0){

				$tipe_sekolah = $daftar_ulang_row['tipe_sekolah_id'];

				if($tipe_sekolah=='1'){
					$sql = "SELECT nama_sekolah FROM sekolah WHERE sekolah_id='".$daftar_ulang_row['sekolah_id']."'";
				}else{
					$sql = "SELECT a.nama_kompetensi,b.nama_sekolah FROM kompetensi_smk as a LEFT JOIN 
							sekolah as b ON (a.sekolah_id=b.sekolah_id) WHERE kompetensi_id='".$daftar_ulang_row['kompetensi_id']."'";
				}
				
				$pilihan_row = $dao->execute(0,$sql)->row_array();
			}

			if(count($daftar_ulang_row)==0){
				$error = 1;
			}

			$data['error'] = $error;
			$data['daftar_ulang_row'] = $daftar_ulang_row;
			$data['pilihan_row'] = $pilihan_row;
			$data['form_id'] = 'cancel-form';

			$this->load->view($this->active_controller.'/cancel_settlement/form',$data);

		}

		function submit_cancel_settlement(){
			
			$this->aah->check_access();


			$this->load->model(array('pendaftaran_model','pendaftaran_dokumen_kelengkapan_model','pendaftaran_nilai_un_model',
									 'pendaftaran_jalur_pilihan_model','pendaftaran_kompetensi_pilihan_model','pendaftaran_sekolah_pilihan_model',
									 'pendaftaran_prestasi_model','daftar_ulang_model','verifikasi_model'));

			$id_pendaftaran = $this->input->post('cancel_id_pendaftaran');
			$nama = $this->input->post('cancel_nama');
			$sekolah_asal = $this->input->post('cancel_sekolah_asal');
			$alamat = $this->input->post('cancel_alamat');
			$tipe_sekolah_id = $this->input->post('cancel_tipe_sekolah_id');
			$hapus_pendaftaran = $this->input->post('cancel_hapus_pendaftaran');

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$this->db->trans_begin();

			$m = $this->daftar_ulang_model;
			$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			$m = $this->pendaftaran_model;
			$m->set_status('1');
			$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			if($tipe_sekolah_id=='1'){
				$m = $this->pendaftaran_sekolah_pilihan_model;
			}else{
				$m = $this->pendaftaran_kompetensi_pilihan_model;
			}

			$m->set_status('3');
			$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			if(!is_null($hapus_pendaftaran)){
				
				$m = $this->pendaftaran_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->pendaftaran_dokumen_kelengkapan_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->pendaftaran_nilai_un_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->pendaftaran_jalur_pilihan_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->pendaftaran_kompetensi_pilihan_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->pendaftaran_sekolah_pilihan_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->pendaftaran_prestasi_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}

				$m = $this->verifikasi_model;
				$result = $dao->delete($m,array('id_pendaftaran'=>$id_pendaftaran));
				if(!$result){
					$this->db->trans_rollback();
					die('failed');
				}
			}			

			$this->db->trans_commit();
			$data['id_pendaftaran'] = $id_pendaftaran;			
			$data['nama'] = $nama;
			$data['sekolah_asal'] = $sekolah_asal;
			$data['alamat'] = $alamat;
			$data['hapus_pendaftaran'] = $hapus_pendaftaran;

			$this->load->view($this->active_controller.'/cancel_settlement/cancel_result',$data);

		}

		function cancel_verification(){
			$this->aah->check_access();			
			
			$data['form_id'] = 'search_form';
			$data['active_url'] = str_replace('::','/',__METHOD__);
			$this->backoffice_template->render($this->active_controller.'/cancel_verification/index',$data);
		}

		function search_verification_for_cancel(){
			$this->aah->check_access();

			$error = 0;
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$no_verifikasi = $this->input->post('src_verifikasi');			

			$verifikasi_row = $dao->execute(0,"SELECT tipe_sekolah_id FROM verifikasi WHERE no_verifikasi='".$no_verifikasi."'")->row_array();
			$pilihan_row = array();

			if(count($verifikasi_row)>0){
				$pilihan_row = array(); 
				$tipe_sekolah = $verifikasi_row['tipe_sekolah_id'];

				if($tipe_sekolah=='1'){
					$sql = "SELECT a.*,b.nama_sekolah,c.no_pendaftaran,
							c.nama,c.jk,c.sekolah_asal,c.alamat,d.pilihan_ke
							FROM verifikasi as a 
							LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id) 
							LEFT JOIN pendaftaran as c ON (a.id_pendaftaran=c.id_pendaftaran)
							LEFT JOIN pendaftaran_sekolah_pilihan as d 
							ON (a.id_pendaftaran=d.id_pendaftaran AND a.jalur_id=d.jalur_id AND a.sekolah_id=d.sekolah_id)
							WHERE no_verifikasi='".$no_verifikasi."'";
				}else{
					$sql = "SELECT a.*,b.nama_sekolah,c.nama_kompetensi,d.no_pendaftaran,
							d.nama,d.jk,d.sekolah_asal,d.alamat,e.pilihan_ke
							FROM verifikasi as a 
							LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)
							LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id) 
							LEFT JOIN pendaftaran as d ON (a.id_pendaftaran=d.id_pendaftaran)
							LEFT JOIN pendaftaran_kompetensi_pilihan as e 
							ON (a.id_pendaftaran=e.id_pendaftaran AND a.jalur_id=e.jalur_id AND a.kompetensi_id=e.kompetensi_id)
							WHERE no_verifikasi='".$no_verifikasi."'";
				}
				
				$pilihan_row = $dao->execute(0,$sql)->row_array();
			}

			if(count($verifikasi_row)==0){
				$error = 1;
			}

			$data['error'] = $error;
			$data['pilihan_row'] = $pilihan_row;
			$data['form_id'] = 'cancel-form';

			$this->load->view($this->active_controller.'/cancel_verification/form',$data);

		}

		function submit_cancel_verification(){
			$this->aah->check_access();


			$this->load->model(array('pendaftaran_sekolah_pilihan_model','pendaftaran_kompetensi_pilihan_model','verifikasi_model'));

			$id_pendaftaran = $this->input->post('cancel_id_pendaftaran');
			$no_registrasi = $this->input->post('cancel_no_registrasi');
			$no_verifikasi = $this->input->post('cancel_no_verifikasi');
			$tipe_sekolah_id = $this->input->post('cancel_tipe_sekolah_id');
			$sekolah_id = $this->input->post('cancel_sekolah_id');
			$kompetensi_id = $this->input->post('cancel_kompetensi_id');
			$jalur_id = $this->input->post('cancel_jalur_id');
			$nama = $this->input->post('cancel_nama');
			$nama_sekolah = $this->input->post('cancel_nama_sekolah');
			$alamat = $this->input->post('cancel_alamat');
			$nama_kompetensi = $this->input->post('cancel_nama_kompetensi');
			$pilihan_ke = $this->input->post('cancel_pilihan_ke');
			$score = $this->input->post('cancel_score');

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$this->db->trans_begin();

			$m = $this->verifikasi_model;
			$result = $dao->delete($m,array('no_verifikasi'=>$no_verifikasi));
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			if($tipe_sekolah_id=='1'){
				$m = $this->pendaftaran_sekolah_pilihan_model;
				$m->set_status("0");
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'jalur_id'=>$jalur_id,'sekolah_id'=>$sekolah_id);
			}else{
				$m = $this->pendaftaran_kompetensi_pilihan_model;
				$m->set_status("0");
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'jalur_id'=>$jalur_id,'kompetensi_id'=>$kompetensi_id);
			}

			$result = $dao->update($m,$cond);
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			$this->db->trans_commit();
			$data['id_pendaftaran'] = $id_pendaftaran;
			$data['no_verifikasi'] = $no_verifikasi;
			$data['no_registrasi'] = $no_registrasi;
			$data['nama'] = $nama;
			$data['nama_sekolah'] = $nama_sekolah;
			$data['nama_kompetensi'] = $nama_kompetensi;
			$data['pilihan_ke'] = $pilihan_ke;
			$data['tipe_sekolah_id'] = $tipe_sekolah_id;
			$data['score'] = $score;

			$this->load->view($this->active_controller.'/cancel_verification/cancel_result',$data);

		}

		function verification(){
			$this->aah->check_access();
			
			$nav_id = $this->aah->get_nav_id(__CLASS__.'/verification');
			$read_access = $this->aah->check_privilege('read',$nav_id);

			if($read_access)
			{
				$kompetensi_rows = array();

				$dao = $this->global_model->get_dao();

				if($this->session->userdata('tipe_sekolah')=='2'){
					
					$sql = "SELECT * FROM kompetensi_smk WHERE sekolah_id='".$this->session->userdata('sekolah_id')."'";
					$kompetensi_rows = $dao->execute(0,$sql)->result_array();
				}
			

				$data['form_id'] = 'search_form';
				$data['api_key'] = $this->_SYS_PARAMS[3];
				$data['kompetensi_rows'] = $kompetensi_rows;
				
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['active_controller'] = $this->active_controller;

				$data['status_open'] = ($this->_SYS_PARAMS[8]=='yes'?true:false);

				$this->backoffice_template->render($this->active_controller.'/verification/index',$data);
			}else{
				$this->error_403();
			}
		}


		function s3ttl3m3nt__(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/settlement');
			$read_access = $this->aah->check_privilege('read',$nav_id);

			if($read_access)
			{
				$kompetensi_rows = array();
				$dao = $this->global_model->get_dao();
				if($this->session->userdata('tipe_sekolah')=='2'){
					
					$sql = "SELECT * FROM kompetensi_smk WHERE sekolah_id='".$this->session->userdata('sekolah_id')."'";
					$kompetensi_rows = $dao->execute(0,$sql)->result_array();
				}

				$jalur_rows = $dao->execute(0,"SELECT * FROM ref_jalur_pendaftaran")->result_array();

				$data['form_id'] = 'search_form';
				$data['kompetensi_rows'] = $kompetensi_rows;
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['jalur_rows'] = $jalur_rows;
				$data['active_controller'] = $this->active_controller;
				$data['settlement_time_input'] = true;

				$this->backoffice_template->render($this->active_controller.'/settlement/index',$data);
			}else{
				$this->error_403();
			}
		}



		function settlement(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/settlement');
			$read_access = $this->aah->check_privilege('read',$nav_id);

			if($read_access)
			{
				$kompetensi_rows = array();
				$dao = $this->global_model->get_dao();
				if($this->session->userdata('tipe_sekolah')=='2'){
					
					$sql = "SELECT * FROM kompetensi_smk WHERE sekolah_id='".$this->session->userdata('sekolah_id')."'";
					$kompetensi_rows = $dao->execute(0,$sql)->result_array();
				}

				$jalur_rows = $dao->execute(0,"SELECT * FROM ref_jalur_pendaftaran")->result_array();

				$data['form_id'] = 'search_form';
				$data['kompetensi_rows'] = $kompetensi_rows;
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['jalur_rows'] = $jalur_rows;
				$data['active_controller'] = $this->active_controller;

				$this->backoffice_template->render($this->active_controller.'/settlement/index',$data);
			}else{
				$this->error_403();
			}
		}
		
		function submit_settlement(){
			$this->aah->check_access();
			$this->load->helper('date_helper');

			$this->load->model(array('pendaftaran_model','pendaftaran_sekolah_pilihan_model',
									 'pendaftaran_kompetensi_pilihan_model','log_status_pendaftaran_model',
									 'daftar_ulang_model'));
			
			$this->load->helper('date_helper');

			$id_pendaftaran = $this->input->post('verifikasi_id_pendaftaran');
			$no_pendaftaran = $this->input->post('verifikasi_no_pendaftaran');
			$jalur_id = $this->input->post('verifikasi_jalur_id');
			$nama_jalur = $this->input->post('verifikasi_nama_jalur');
			$nama_sekolah = $this->input->post('verifikasi_nama_sekolah');
			$kompetensi_id = $this->input->post('verifikasi_kompetensi_id');
			$nama_kompetensi = $this->input->post('verifikasi_nama_kompetensi');
			$nama = $this->input->post('verifikasi_nama');
			$jk = $this->input->post('verifikasi_jk');
			$alamat = $this->input->post('verifikasi_alamat');
			$nama_kecamatan = $this->input->post('verifikasi_nama_kecamatan');
			$nama_dt2 = $this->input->post('verifikasi_nama_dt2');
			$sekolah_asal = $this->input->post('verifikasi_sekolah_asal');
			$tipe_jalur = $this->input->post('verifikasi_tipe_jalur');
			$settlement_time_input = $this->input->post('verifikasi_settlement_time_input');

			if($settlement_time_input=='1'){
				$wkt_daftar_ulang = us_date_format($this->input->post('verifikasi_tgl_daftar_ulang'));
			}else{
				$wkt_daftar_ulang = date('Y-m-d H:i:s');
			}			

			$sekolah_id = $this->session->userdata('sekolah_id');
			$tipe_sekolah_id = $this->session->userdata('tipe_sekolah');
			$nama_tipe_sekolah = $this->input->post('verifikasi_nama_tipe_sekolah');

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$this->db->trans_begin();

			$m = $this->pendaftaran_model;
			$m->set_status('2');
			$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal mendaftar ulang');
			}

			if($tipe_sekolah_id=='1')
			{
				$m = $this->pendaftaran_sekolah_pilihan_model;
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'sekolah_id'=>$sekolah_id,'jalur_id'=>$jalur_id);
			}
			else
			{
				$m = $this->pendaftaran_kompetensi_pilihan_model;
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'kompetensi_id'=>$kompetensi_id,'jalur_id'=>$jalur_id);
			}

			$m->set_status('5');
			$result = $dao->update($m,$cond);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal mendaftar ulang');
			}


			$m = $this->daftar_ulang_model;

			$m->set_id_pendaftaran($id_pendaftaran);
			$m->set_jalur_id($jalur_id);
			$m->set_sekolah_id($sekolah_id);
			$m->set_tipe_sekolah_id($tipe_sekolah_id);
			$m->set_kompetensi_id($kompetensi_id);
			$m->set_user($this->session->userdata('username'));
			$m->set_created_time($wkt_daftar_ulang);

			$result = $dao->insert($m);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal mendaftar ulang');
			}

			//insert registration log
			$m = $this->log_status_pendaftaran_model;			

			$m->set_id_pendaftaran($id_pendaftaran);
			$m->set_thn_pelajaran($this->_SYS_PARAMS[0]);
			$m->set_status('3');
			$m->set_jalur_id($jalur_id);
			$m->set_sekolah_id($sekolah_id);
			$m->set_created_time($wkt_daftar_ulang);
			$m->set_user($this->session->userdata('username'));
			$result = $dao->insert($m);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			$this->db->trans_commit();
			//end of transaction
			
			$encoded_regid = base64_encode($id_pendaftaran);
			$encoded_fieldid = base64_encode($kompetensi_id);

			$data['id_pendaftaran'] = $id_pendaftaran;
			$data['no_pendaftaran'] = $no_pendaftaran;
			$data['nama'] = $nama;
			$data['jk'] = $jk;
			$data['sekolah_asal'] = $sekolah_asal;
			$data['sekolah_id'] = $sekolah_id;
			$data['nama_sekolah'] = $nama_sekolah;
			$data['kompetensi_id'] = $kompetensi_id;
			$data['nama_kompetensi'] = $nama_kompetensi;
			$data['tipe_sekolah_id'] = $tipe_sekolah_id;
			$data['nama_tipe_sekolah'] = $nama_tipe_sekolah;
			$data['jalur_id'] = $jalur_id;
			$data['nama_jalur'] = $nama_jalur;
			$data['alamat'] = $alamat;
			$data['nama_kecamatan'] = $nama_kecamatan;
			$data['nama_dt2'] = $nama_dt2;
			$data['encoded_regid'] = $encoded_regid;
			$data['encoded_fieldid'] = $encoded_fieldid;
			$data['active_controller'] = $this->active_controller;

			$x_wkt_daftar_ulang = explode(' ',$wkt_daftar_ulang);
			$tgl_daftar_ulang = $x_wkt_daftar_ulang[0];
			$jam_daftar_ulang = "";
			if(count($x_wkt_daftar_ulang)>1){
				$jam_daftar_ulang = $x_wkt_daftar_ulang[1];
			}
			
			$data['tgl_daftar_ulang'] = $tgl_daftar_ulang;
			$data['jam_daftar_ulang'] = $jam_daftar_ulang;

			$this->load->view($this->active_controller.'/settlement/settlement_result',$data);
		}

		function get_distanceWeight($distance){

			if($distance<=4000){
				$sql = "SELECT bobot FROM pengaturan_bobot_jarak WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' 
						AND (jarak_min<='".$distance."' AND jarak_max>='".$distance."')";
				
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$distance_weight_row = $dao->execute(0,$sql)->row_array();
				return (!is_null($distance_weight_row['bobot'])?$distance_weight_row['bobot']:0);
			}else{
				return 10;
			}
		}

		function get_achievementWeight($level,$rate){
			
			$sql = "SELECT bobot_juara1,bobot_juara2,bobot_juara3 FROM pengaturan_bobot_prestasi WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."'
					AND tkt_kejuaraan_id='".$level."'";
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
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


		function submit_verification(){
			$this->aah->check_access();
			$this->load->helper('date_helper');
			$this->load->model(array('pendaftaran_model','pendaftaran_dokumen_kelengkapan_model','pendaftaran_sekolah_pilihan_model',
									 'pendaftaran_kompetensi_pilihan_model','pendaftaran_nilai_un_model','pendaftaran_prestasi_model',
									 'log_status_pendaftaran_model','verifikasi_model'));

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$id_pendaftaran = $this->input->post('verifikasi_id_pendaftaran');
			$no_pendaftaran = $this->input->post('verifikasi_no_pendaftaran');
			$jalur_id = $this->input->post('verifikasi_jalur_id');
			$sekolah_id = $this->input->post('verifikasi_sekolah_id');
			$kompetensi_id = $this->input->post('verifikasi_kompetensi_id');
			$tipe_sekolah_id = $this->input->post('verifikasi_tipe_sekolah_id');
			$mode_un = strtolower($this->input->post('verifikasi_mode_un'));

			$nil_bhs_indonesia = (!is_null($this->input->post('verifikasi_nil_bhs_indonesia'))?'1':'0');
			$nil_bhs_inggris = (!is_null($this->input->post('verifikasi_nil_bhs_inggris'))?'1':'0');
			$nil_matematika = (!is_null($this->input->post('verifikasi_nil_matematika'))?'1':'0');
			$nil_ipa = (!is_null($this->input->post('verifikasi_nil_ipa'))?'1':'0');			
			$tot_nilai = (!is_null($this->input->post('verifikasi_tot_nilai'))?'1':'0');

			$nil_total = $this->input->post('verifikasi_nilai_total');
			$n_berkas = $this->input->post('verifikasi_n_berkas');
			
			$this->db->trans_begin();

			//update pendaftaran
			$m = $this->pendaftaran_model;
			$m->set_mode_un(strtoupper($mode_un));
			$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			//insert pendaftaran_nilai_un
			$status1 = ($tot_nilai==='1');
			$m = $this->pendaftaran_nilai_un_model;
			$m->set_status_nil_bhs_indonesia($nil_bhs_indonesia);			
			$m->set_status_nil_bhs_inggris($nil_bhs_inggris);
			$m->set_status_nil_matematika($nil_matematika);
			$m->set_status_nil_ipa($nil_ipa);

			$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			//insert pendaftaran_dokumen_kelengkapan
			$m = $this->pendaftaran_dokumen_kelengkapan_model;			
			$status2 = true;
			for($i=1;$i<=$n_berkas;$i++){

				$berkas_id = $this->input->post('verifikasi_berkas_id'.$i);
				$status = !is_null($this->input->post('verifikasi_berkas'.$i));
				$_status = (!is_null($this->input->post('verifikasi_berkas'.$i))?'1':'2');
				$m->set_status($_status);
				$result = $dao->update($m,array('dokel_id'=>$berkas_id));
				if(!$result){
					$this->db->trans_rollback();
					die('ERROR: gagal menyimpan verifikasi');
				}

				if($status2)
				{
					$status2 = $status2 && $status;
				}
			}

			$status3 = true;
			if($jalur_id=='4')
			{
				$m = $this->pendaftaran_prestasi_model;
				$n_prestasi = $this->input->post('verifikasi_n_prestasi');
				$status3 = false;

				for($i=1;$i<=$n_prestasi;$i++){

					$prestasi_id = $this->input->post('verifikasi_prestasi_id'.$i);

					$status = !is_null($this->input->post('verifikasi_prestasi'.$i));
					$_status = (!is_null($this->input->post('verifikasi_prestasi'.$i))?'1':'2');
					$m->set_status($_status);
					$result = $dao->update($m,array('prestasi_id'=>$prestasi_id));
					if(!$result){
						$this->db->trans_rollback();
						die('ERROR: gagal menyimpan verifikasi');
					}

					if($status && !$status3)
					{
						$status3 = true;
					}
				}
			}

			//perhitungan score
			$score = 0;
			$jarak = 0;
			$latitude = 0;
			$longitude = 0;

			if($tipe_sekolah_id=='1')
			{
				if($jalur_id=='1' or $jalur_id=='2' or $jalur_id=='3')
				{
					$x_jarak = explode(' ',$this->security->xss_clean($this->input->post('verifikasi_jarak')));
					$jarak =  str_replace(",","",$x_jarak[0]);

					$x_latlang = explode(',',$this->security->xss_clean($this->input->post('reg_latLng')));
					$latitude = $x_latlang[0];
					$longitude = $x_latlang[1];

					$score = $jarak;
					if($jalur_id=='3'){
						$radiusWeight = $this->get_distanceWeight($jarak);
						$score = $nil_total + ($mode_un=='unbk'? (20*$nil_total)/100 :0) + $radiusWeight;
					}
				}else if($jalur_id=='4'){

					$sql =  "SELECT tkt_kejuaraan_id,peringkat FROM pendaftaran_prestasi WHERE id_pendaftaran='".$id_pendaftaran."' ORDER BY tkt_kejuaraan_id,peringkat DESC";
					$row = $dao->execute(0,$sql)->row_array();
					$score = $this->get_achievementWeight($row['tkt_kejuaraan_id'],$row['peringkat']);

				}else{
					$score = $nil_total;
				}
			}
			else
			{
				if($jalur_id=='3')
				{
					$score = $nil_total + ($mode_un=='unbk'? (20*$nil_total)/100 :0);
				}
				else if($jalur_id=='4')
				{
					$sql =  "SELECT tkt_kejuaraan_id,peringkat FROM pendaftaran_prestasi WHERE id_pendaftaran='".$id_pendaftaran."' ORDER BY tkt_kejuaraan_id,peringkat DESC";
					$row = $dao->execute(0,$sql)->row_array();
					$score = $this->get_achievementWeight($row['tkt_kejuaraan_id'],$row['peringkat']);
				}else{
					$score = $nil_total;
				}
			}

			$status_pendaftaran = (!$status1 || !$status2 || !$status3?'2':'3');			

			if($tipe_sekolah_id=='1' or $tipe_sekolah_id=='3')
			{
				$m = $this->pendaftaran_sekolah_pilihan_model;
				$m->set_status($status_pendaftaran);

				$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran,'sekolah_id'=>$sekolah_id,'jalur_id'=>$jalur_id));				
			}
			else
			{
				$m = $this->pendaftaran_kompetensi_pilihan_model;				
				$m->set_status($status_pendaftaran);
				$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran,'kompetensi_id'=>$kompetensi_id,'jalur_id'=>$jalur_id));				
			}

			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			$m = $this->verifikasi_model;

			$dt2_id = substr($no_pendaftaran,3,2);

			$no_verifikasi = $this->generate_verification_numb($dt2_id,$this->session->userdata('sekolah_id'),$jalur_id);

			$m->set_id_pendaftaran($id_pendaftaran);
			$m->set_jalur_id($jalur_id);
			$m->set_sekolah_id($sekolah_id);
			$m->set_tipe_sekolah_id($tipe_sekolah_id);
			$m->set_kompetensi_id($kompetensi_id);
			$m->set_no_verifikasi($no_verifikasi);
			$m->set_jarak($jarak);
			$m->set_latitude($latitude);
			$m->set_longitude($longitude);
			$m->set_score($score);
			$m->set_user($this->session->userdata('username'));
			$m->set_created_time(date('Y-m-d H:i:s'));
			
			$result = $dao->insert($m);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			//insert registration log
			$m = $this->log_status_pendaftaran_model;
			$wkt_verifikasi = date('Y-m-d H:i:s');
			$m->set_id_pendaftaran($id_pendaftaran);
			$m->set_thn_pelajaran($this->_SYS_PARAMS[0]);
			$m->set_status('1');
			$m->set_jalur_id($jalur_id);
			$m->set_sekolah_id($sekolah_id);
			$m->set_created_time($wkt_verifikasi);
			$m->set_user($this->session->userdata('username'));
			$result = $dao->insert($m);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			$this->db->trans_commit();
			//end of transaction
			
			$encoded_regid = base64_encode($id_pendaftaran);
			$encoded_fieldid = base64_encode($kompetensi_id);

			$administration_result = $this->get_administration_result($id_pendaftaran,$tipe_sekolah_id,$kompetensi_id);
			$data['active_controller'] = $this->active_controller;
			$data['pendaftaran_row'] = $administration_result['pendaftaran_row'];
			$data['sekolah_pilihan_row'] = $administration_result['sekolah_pilihan_row'];
			$data['encoded_regid'] = $encoded_regid;
			$data['encoded_fieldid'] = $encoded_fieldid;

			$this->load->view($this->active_controller.'/verification/ranking_result',$data);
		}

		private function generate_verification_numb($dt2_id,$school_id,$path){
			
			$new_numb = 'V'.date('y').'-'.$dt2_id.'-'.sprintf('%03s',$school_id).$path;

			$dao = $this->global_model->get_dao();
			$row = $dao->execute(0,"SELECT MAX(no_verifikasi) last_numb FROM verifikasi WHERE no_verifikasi LIKE '".$new_numb."%'")->row_array();

			$new_order = 1;
			if(!empty($row['last_numb'])){
				$last_order = substr($row['last_numb'],12,4);
				$new_order += $last_order;
			}
			$new_numb .= '.'.sprintf('%04s',$new_order);

			return $new_numb;
		}

		function get_quota($jalur_id,$tipe_sekolah_id,$sekolah_id,$kompetensi_id,$dao){

			if($tipe_sekolah_id=='1')
			{
				$sql = "SELECT * FROM pengaturan_kuota_sma
						WHERE sekolah_id='".$sekolah_id."' AND thn_pelajaran='".$this->_SYS_PARAMS[0]."'";
			}else{
				$sql = "SELECT * FROM pengaturan_kuota_smk
						WHERE sekolah_id='".$sekolah_id."' AND kompetensi_id='".$kompetensi_id."' AND thn_pelajaran='".$this->_SYS_PARAMS[0]."'";
			}
			$kuota_row = $dao->execute(0,$sql)->row_array();

			switch($jalur_id){
				case '1':$kuota = $kuota_row['kuota_domisili'];break;
				case '2':$kuota = $kuota_row['kuota_afirmasi'];break;
				case '3':$kuota = $kuota_row['kuota_akademik'];break;
				case '4':$kuota = $kuota_row['kuota_prestasi'];break;
				case '5':$kuota = $kuota_row['kuota_khusus'];break;
				default:$kuota=0;
			}

			return $kuota;
		}

		function ranking_process(){			

			$this->load->helper(array('mix_helper','date_helper'));
			$this->load->model(array('pendaftaran_model','pendaftaran_sekolah_pilihan_model','pendaftaran_kompetensi_pilihan_model',
									 'log_status_pendaftaran_model','hasil_seleksi_model'));
			

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$id_pendaftaran = $this->input->post('ranking_id_pendaftaran');
			$sekolah_id = $this->input->post('ranking_sekolah_id');
			$kompetensi_id = $this->input->post('ranking_kompetensi_id');
			$nama_kompetensi = $this->input->post('ranking_nama_kompetensi');
			$tipe_sekolah_id = $this->input->post('ranking_tipe_sekolah_id');
			$nama_tipe_sekolah = $this->input->post('ranking_nama_tipe_sekolah');
			$jalur_id = $this->input->post('ranking_jalur_id');
			$nama_jalur = $this->input->post('ranking_nama_jalur');
			$no_pendaftaran = $this->input->post('ranking_no_pendaftaran');
			$nama = $this->input->post('ranking_nama');
			$jk = $this->input->post('ranking_jk');
			$sekolah_asal = $this->input->post('ranking_sekolah_asal');
			$alamat = $this->input->post('ranking_alamat');
			$nama_kecamatan = $this->input->post('ranking_nama_kecamatan');
			$nama_dt2 = $this->input->post('ranking_nama_dt2');
			$nama_sekolah = $this->input->post('ranking_nama_sekolah');
			$sekolah_pilihan_ke = $this->input->post('ranking_sekolah_pilihan_ke');
			$tgl_verifikasi = $this->input->post('ranking_tgl_verifikasi');
			$score = $this->input->post('ranking_score');

			//start transaction
			$this->db->trans_begin();			

			$created_time = date('Y-m-d H:i:s');

			$m1 = $this->hasil_seleksi_model;

			if($tipe_sekolah_id=='1' or $tipe_sekolah_id=='3')
			{
				$m2 = $this->pendaftaran_sekolah_pilihan_model;
				$condM2 = array('sekolah_id'=>$sekolah_id,'id_pendaftaran'=>$id_pendaftaran);
			}else{
				$m2 = $this->pendaftaran_kompetensi_pilihan_model;
				$condM2 = array('kompetensi_id'=>$kompetensi_id,'id_pendaftaran'=>$id_pendaftaran);
			}

			$result = $dao->delete($m1,$condM2);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menetapkan peringkat');
			}

			$m1->set_id_pendaftaran($id_pendaftaran);
			$m1->set_jalur_id($jalur_id);
			$m1->set_sekolah_id($sekolah_id);
			$m1->set_tipe_sekolah_id($tipe_sekolah_id);
			$m1->set_kompetensi_id($kompetensi_id);
			$m1->set_pilihan_ke($sekolah_pilihan_ke);
			$m1->set_thn_pelajaran($this->_SYS_PARAMS[0]);
			$m1->set_score($score);
			$m1->set_created_time($created_time);

			$result = $dao->insert($m1);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menetapkan peringkat');
			}

			$m2->set_status('3');
			$result = $dao->update($m2,$condM2);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menetapkan peringkat');
			}

			//insert registration log
			$m = $this->log_status_pendaftaran_model;
			$wkt_ranking = date('Y-m-d H:i:s');

			$m->set_id_pendaftaran($id_pendaftaran);
			$m->set_thn_pelajaran($this->_SYS_PARAMS[0]);
			$m->set_status('2');
			$m->set_jalur_id($jalur_id);
			$m->set_sekolah_id($sekolah_id);
			$m->set_created_time($wkt_ranking);
			$m->set_user($this->session->userdata('username'));
			$result = $dao->insert($m);
			if(!$result){
				$this->db->trans_rollback();
				die('ERROR: gagal menyimpan verifikasi');
			}

			$this->db->trans_commit();
			//end of transaction

			$encoded_regid = base64_encode($id_pendaftaran);
			$encoded_fieldid = base64_encode($kompetensi_id);
			
			$data['id_pendaftaran'] = $id_pendaftaran;
			$data['no_pendaftaran'] = $no_pendaftaran;
			$data['nama'] = $nama;
			$data['jk'] = $jk;
			$data['sekolah_asal'] = $sekolah_asal;			
			$data['alamat'] = $alamat;
			$data['nama_kecamatan'] = $nama_kecamatan;
			$data['nama_dt2'] = $nama_dt2;
			$data['nama_sekolah'] = $nama_sekolah;
			$data['kompetensi_id'] = $kompetensi_id;
			$data['nama_kompetensi'] = $nama_kompetensi;
			$data['sekolah_pilihan_ke'] = $sekolah_pilihan_ke;
			$data['tipe_sekolah_id'] = $tipe_sekolah_id;
			$data['nama_tipe_sekolah'] = $nama_tipe_sekolah;
			$data['tgl_verifikasi'] = $tgl_verifikasi;
			$data['jalur'] = $nama_jalur;
			$data['peringkat'] = '-';
			$data['score'] = $score;
			$data['active_controller'] = $this->active_controller;
			$data['encoded_regid'] = $encoded_regid;
			$data['encoded_fieldid'] = $encoded_fieldid;

			$this->load->view($this->active_controller.'/verification/ranking_result',$data);
		}

		function set_reg_status($regid,$tipe_sekolah_id,$sekolah_id,$kompetensi_id){
			$this->load->model(array('pendaftaran_model'));

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$table = ($tipe_sekolah_id=='1'?'pendaftaran_sekolah_pilihan':'pendaftaran_kompetensi_pilihan');
			$field = ($tipe_sekolah_id=='1'?'sekolah_id':'kompetensi_id');
			$value = ($tipe_sekolah_id=='1'?$sekolah_id:$kompetensi_id);

			$sql = "SELECT status FROM ".$table." WHERE id_pendaftaran='".$regid."' AND ".$field."<>'".$value."'";

			$rows = $dao->execute(0,$sql)->result_array();

			$occurrences = array_count_values(array_column($rows,'status'));
			$result = true;
			if(isset($occurrences[4]) && count($rows)==$occurrences[4]){
				$m = $this->pendaftaran_model;
				$m->set_status('3');
				$result = $dao->update($m,array('id_pendaftaran'=>$regid));
			}
			return $result;
		}

		function path_name($id){
			$path_name = '';
			switch($id){
				case '1':$path_name='domisili';break;
				case '2':$path_name='afirmasi';break;
				case '3':$path_name='akademik';break;
				case '4':$path_name='prestasi';break;
				case '5':$path_name='khusus';break;
			}
			return $path_name;
		}

		function search_verification(){
			
			$this->load->library(array('settlement'));
			$this->aah->check_access();

			$error = 0;

			$this->load->helper(array('date_helper','mix_helper'));
			$no_peserta = $this->security->xss_clean($this->input->post('src_no_peserta'));
			$jalur_id = $this->security->xss_clean($this->input->post('src_jalur_id'));
			$jenis_kompetensi = $this->security->xss_clean($this->input->post('src_kompetensi'));
			$settlement_time_input = $this->input->post('settlement_time_input');

			$dao = $this->global_model->get_dao();

			$pendaftaran_row = array();
			$sekolah_pilihan_row = array();
			$jalur_row = array();

			if($this->_SYS_PARAMS[9]=='yes')
			{


				$sql = "SELECT a.*,b.nama_kecamatan,c.nama_dt2,d.nama_jalur,d.jalur_id
						FROM ".($jalur_id<>'5'?'ppdb_sulsel_tahap1':'ppdb_sulsel').".pendaftaran as a 
						LEFT JOIN ref_kecamatan as b ON (a.kecamatan_id=b.kecamatan_id) 
						LEFT JOIN ref_dt2 as c ON (a.dt2_id=c.dt2_id)
						LEFT JOIN (SELECT x.id_pendaftaran,x.jalur_id,y.nama_jalur FROM pendaftaran_jalur_pilihan as x 
							LEFT JOIN ref_jalur_pendaftaran as y ON (x.jalur_id=y.ref_jalur_id)) as d 
						ON (a.id_pendaftaran=d.id_pendaftaran)
						WHERE a.id_pendaftaran='".$no_peserta."'";
				

				$pendaftaran_row = $dao->execute(0,$sql)->row_array();
				
				if(count($pendaftaran_row)>0)
				{

					$sql = "SELECT a.id_pendaftaran,a.sekolah_id,a.kompetensi_id,a.tipe_sekolah_id,b.nama_jalur,
							DATE_FORMAT(a.created_time,'%Y-%m-%d') as tgl_daftar_ulang FROM daftar_ulang as a 
							LEFT JOIN ref_jalur_pendaftaran as b ON (a.jalur_id=b.ref_jalur_id) 
							WHERE a.id_pendaftaran='".$no_peserta."'";

					$daftar_ulang_row = $dao->execute(0,$sql)->row_array();

					if(count($daftar_ulang_row)==0)
					{

						// $field_cond = "";
						// if($this->session->userdata('tipe_sekolah')=='2'){
						// 	$field_cond = "AND kompetensi_id='".$jenis_kompetensi."'";
						// }

						// $sql = "SELECT *,b.nama_jalur FROM pengumuman_seleksi_sulsel2 as a 
						// 		LEFT JOIN ref_jalur_pendaftaran as b ON (a.jalur_id=b.ref_jalur_id) 
						// 		WHERE a.id_pendaftaran='".$no_peserta."' 
						// 		AND a.sekolah_id='".$this->session->userdata('sekolah_id')."' ".$field_cond;

						// $pengumuman2_row = $dao->execute(0,$sql)->row_array();


						// if(count($pengumuman2_row)>0)
						// {

						$sql = "SELECT * FROM ref_jalur_pendaftaran WHERE ref_jalur_id='".$jalur_id."'";						
						
						$jalur_row = $dao->execute(0,$sql)->row_array();
						

						if($this->session->userdata('tipe_sekolah')=='1')
						{
							$sql = "SELECT b.nama_tipe_sekolah,b.akronim,a.sekolah_id,b.tipe_sekolah_id,b.akronim,
									'' as kompetensi_id,b.nama_sekolah,'' as nama_kompetensi
									FROM verifikasi as a 
									LEFT JOIN (SELECT a.sekolah_id,a.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim,a.nama_sekolah FROM sekolah as a 
												LEFT JOIN ref_tipe_sekolah as b ON (b.ref_tipe_sklh_id=a.tipe_sekolah_id)) as b ON (a.sekolah_id=b.sekolah_id) 
									WHERE a.id_pendaftaran='".$no_peserta."' AND a.sekolah_id='".$this->session->userdata('sekolah_id')."' 
									AND jalur_id='".$jalur_id."'";
							

							$sekolah_pilihan_row = $dao->execute(0,$sql)->row_array();

						
						}
						else
						{
						
							$sql = "SELECT b.nama_tipe_sekolah,b.akronim,a.sekolah_id,b.tipe_sekolah_id,b.akronim,
									a.kompetensi_id,b.nama_sekolah,c.nama_kompetensi
									FROM verifikasi as a 
									LEFT JOIN (SELECT a.sekolah_id,a.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim,a.nama_sekolah FROM sekolah as a 
												LEFT JOIN ref_tipe_sekolah as b ON (b.ref_tipe_sklh_id=a.tipe_sekolah_id)) as b ON (a.sekolah_id=b.sekolah_id) 
									LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id) 
									WHERE a.id_pendaftaran='".$no_peserta."' AND a.sekolah_id='".$this->session->userdata('sekolah_id')."' 
									AND a.kompetensi_id='".$jenis_kompetensi."'
									AND a.jalur_id='".$jalur_id."'";

							$sekolah_pilihan_row = $dao->execute(0,$sql)->row_array();
						}

						// $data['pengumuman_row'] = $pengumuman2_row;
						
						// }
						// else
						// {

						// 	$sql = "SELECT *,b.nama_jalur FROM pengumuman_seleksi_sulsel1 as a LEFT JOIN ref_jalur_pendaftaran as b ON (a.jalur_id=b.ref_jalur_id) 
						// 			WHERE a.id_pendaftaran='".$no_peserta."' AND a.sekolah_id='".$this->session->userdata('sekolah_id')."' ".$field_cond;
						// 	$pengumuman1_row = $dao->execute(0,$sql)->row_array();

						// 	if(count($pengumuman1_row)>0)
						// 	{
						// 		if($this->session->userdata('tipe_sekolah')=='1')
						// 		{
						// 			$sql = "SELECT b.nama_tipe_sekolah,b.akronim,a.sekolah_id,b.tipe_sekolah_id,b.akronim,
						// 					'' as kompetensi_id,b.nama_sekolah,'' as nama_kompetensi,a.pilihan_ke
						// 					FROM ppdb_sulsel_tahap1.pendaftaran_sekolah_pilihan as a 
						// 					LEFT JOIN (SELECT a.sekolah_id,a.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim,a.nama_sekolah FROM sekolah as a 
						// 								LEFT JOIN ref_tipe_sekolah as b ON (b.ref_tipe_sklh_id=a.tipe_sekolah_id)) as b ON (a.sekolah_id=b.sekolah_id) 
						// 					WHERE a.id_pendaftaran='".$no_peserta."' AND a.sekolah_id='".$this->session->userdata('sekolah_id')."'";
								
						// 			$sekolah_pilihan_row = $dao->execute(0,$sql)->row_array();
								
						// 		}
						// 		else
						// 		{
								
						// 			$sql = "SELECT b.nama_tipe_sekolah,b.akronim,a.sekolah_id,b.tipe_sekolah_id,b.akronim,
						// 					a.kompetensi_id,b.nama_sekolah,c.nama_kompetensi,a.pilihan_ke
						// 					FROM ppdb_sulsel_tahap1.pendaftaran_kompetensi_pilihan as a 
						// 					LEFT JOIN (SELECT a.sekolah_id,a.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim,a.nama_sekolah FROM sekolah as a 
						// 								LEFT JOIN ref_tipe_sekolah as b ON (b.ref_tipe_sklh_id=a.tipe_sekolah_id)) as b ON (a.sekolah_id=b.sekolah_id) 
						// 					LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id) 
						// 					WHERE a.id_pendaftaran='".$no_peserta."' AND a.sekolah_id='".$this->session->userdata('sekolah_id')."' 
						// 					AND a.kompetensi_id='".$jenis_kompetensi."'";

						// 			$sekolah_pilihan_row = $dao->execute(0,$sql)->row_array();

						// 			if(count($sekolah_pilihan_row)==0){
						// 				$error = 1;
						// 			}
						// 		}
						// 	}else{
						// 		$error = 3;
						// 	}
						// 	$data['pengumuman_row'] = $pengumuman1_row;
						// }

					}else{

						$kompetensi_row = array();
						if($daftar_ulang_row['tipe_sekolah_id']=='1'){
							$sql = "SELECT a.nama_sekolah,b.nama_tipe_sekolah FROM sekolah as a 
									LEFT JOIN ref_tipe_sekolah as b ON (a.tipe_sekolah_id=b.ref_tipe_sklh_id) WHERE a.sekolah_id='".$daftar_ulang_row['sekolah_id']."'";

							$sekolah_row = $dao->execute(0,$sql)->row_array();
						}else{

							$sql = "SELECT a.nama_sekolah,b.nama_tipe_sekolah FROM sekolah as a 
									LEFT JOIN ref_tipe_sekolah as b ON (a.tipe_sekolah_id=b.ref_tipe_sklh_id) 
									WHERE a.sekolah_id='".$daftar_ulang_row['sekolah_id']."'";
							$sekolah_row = $dao->execute(0,$sql)->row_array();

							$sql = "SELECT nama_kompetensi FROM kompetensi_smk WHERE kompetensi_id='".$daftar_ulang_row['kompetensi_id']."'";
							$kompetensi_row = $dao->execute(0,$sql)->row_array();

						}
						$data['daftar_ulang_row'] = $daftar_ulang_row;
						$data['sekolah_row'] = $sekolah_row;
						$data['kompetensi_row'] = $kompetensi_row;

						$error = 2;
					}

				}else{
					$error = 1;
				}
			}else{
				$error = 4;
			}

			
			$data['pendaftaran_row']=$pendaftaran_row;
			$data['sekolah_pilihan_row']=$sekolah_pilihan_row;
			$data['jalur_row']=$jalur_row;
			$data['form_id'] = 'settlement-form';
			$data['active_controller'] = $this->active_controller;
			$data['settlement_time_input'] = $settlement_time_input;
			$data['error'] = $error;

			$this->load->view($this->active_controller.'/settlement/form',$data);
		}

		function check_ranking($regid,$path,$school_id,$school_type,$field_id,$quota){

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$order = "";
			switch($path){
				case '1': $order = 'a.score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '2': $order = 'a.score '.($school_type=='1'?'ASC':'DESC').',b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '3': $order = "a.score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";break;
				case '4': $order = 'a.score DESC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '5': $order = 'b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
			}

			$sql = "SELECT a.id_pendaftaran FROM hasil_seleksi as a LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
					WHERE a.jalur_id='".$path."' AND a.sekolah_id='".$school_id."' AND 
					a.kompetensi_id='".$field_id."' ORDER BY ".$order." LIMIT 0,".number_format($quota);

		
			$rows = $dao->execute(0,$sql)->result_array();
			$result = false;

			foreach($rows as $row){
				if($row['id_pendaftaran']==$regid){
					$result = true;
					break;
				}
			}

			return $result;
		}

		function search_registration()
		{
			$this->aah->check_access();
			$pendaftaran_row=array();
			$sekolah_pilihan_row=array();
			$form_debug = false;

			if($this->_SYS_PARAMS[8]=='yes')
			{

				$school_type = $this->session->userdata('tipe_sekolah');

				$error = 0;
				$this->load->helper(array('date_helper','mix_helper'));
				$no_pendaftaran = $this->security->xss_clean($this->input->post('src_registrasi'));
				$jenis_kompetensi = $this->security->xss_clean($this->input->post('src_kompetensi'));

				$dao = $this->global_model->get_dao();
				
				$sql = "SELECT a.*,b.nama_kecamatan,c.nama_dt2,
						d.nama_jalur,d.jalur_id,d.ktg_jalur_id as tipe_jalur,
						e.nil_bhs_indonesia,e.nil_bhs_inggris,e.nil_matematika,e.nil_ipa,e.tot_nilai
						FROM pendaftaran as a 
						LEFT JOIN ref_kecamatan as b ON (a.kecamatan_id=b.kecamatan_id) 
						LEFT JOIN ref_dt2 as c ON (a.dt2_kk=c.dt2_id)
						LEFT JOIN (SELECT w.id_pendaftaran,w.jalur_id,y.nama_jalur,z.ktg_jalur_id
							FROM pendaftaran_jalur_pilihan as w						
							LEFT JOIN ref_jalur_pendaftaran as y ON (w.jalur_id=y.ref_jalur_id)
							LEFT JOIN (SELECT jalur_id,ktg_jalur_id FROM pengaturan_kuota_jalur 
									   WHERE thn_pelajaran='".$this->_SYS_PARAMS[0]."' 
									   AND tipe_sekolah_id='".$this->session->userdata('tipe_sekolah')."') as z ON (w.jalur_id=z.jalur_id)) as d 
						ON (a.id_pendaftaran=d.id_pendaftaran)
						LEFT JOIN pendaftaran_nilai_un as e ON (a.id_pendaftaran=e.id_pendaftaran) 
						WHERE a.no_pendaftaran='".$no_pendaftaran."'";
				
				$pendaftaran_row = $dao->execute(0,$sql)->row_array();

				$sekolah_pilihan_row = array();
				$dokumen_rows = array();
				$prestasi_rows = array();
				$status_urutan = true;

				if(count($pendaftaran_row)>0)
				{
					$sql = "SELECT a.*,b.nama_dokumen FROM pendaftaran_dokumen_kelengkapan as a LEFT JOIN ref_dokumen_persyaratan as b ON (a.dokumen=b.ref_dokumen_id) 
							WHERE a.id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."'";
					$dokumen_rows = $dao->execute(0,$sql)->result_array();

					if($school_type=='1' or $school_type=='3')
					{
						$sql = "SELECT a.id_pendaftaran,'0' as kompetensi_id,b.nama_sekolah,
								b.latitude,b.longitude,b.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim,
								b.alamat as alamat_sekolah,a.sekolah_id,a.pilihan_ke,a.status,'' as nama_kompetensi
								FROM pendaftaran_sekolah_pilihan as a 
								LEFT JOIN (SELECT x.sekolah_id,x.nama_sekolah,x.latitude,x.longitude,x.alamat,x.tipe_sekolah_id,y.nama_tipe_sekolah,y.akronim 
									FROM sekolah as x LEFT JOIN ref_tipe_sekolah as y ON (x.tipe_sekolah_id=y.ref_tipe_sklh_id)) as b ON (a.sekolah_id=b.sekolah_id) 
								WHERE a.id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."' AND 
								a.sekolah_id='".$this->session->userdata('sekolah_id')."'";
					}else{
						$sql = "SELECT a.id_pendaftaran,a.kompetensi_id,b.nama_sekolah,b.latitude,b.longitude,
								b.alamat as alamat_sekolah,a.sekolah_id,b.tipe_sekolah_id,b.nama_tipe_sekolah,b.akronim,
								c.nama_kompetensi,a.pilihan_ke,a.status
								FROM pendaftaran_kompetensi_pilihan as a 
								LEFT JOIN (SELECT x.sekolah_id,x.nama_sekolah,x.latitude,x.longitude,x.alamat,x.tipe_sekolah_id,y.nama_tipe_sekolah,y.akronim 
											FROM sekolah as x LEFT JOIN ref_tipe_sekolah as y ON (x.tipe_sekolah_id=y.ref_tipe_sklh_id)) as b ON (a.sekolah_id=b.sekolah_id)  
								LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id)
								WHERE a.id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."' AND 
								a.kompetensi_id='".$jenis_kompetensi."'";
					}
					
					$sekolah_pilihan_row = $dao->execute(0,$sql)->row_array();				
					
					if(count($sekolah_pilihan_row)>0)
					{

						if($sekolah_pilihan_row['status']=='3'){
							$error = 2;//verified
						}else{

							$sql = "SELECT count(1) as n_settlement FROM daftar_ulang WHERE id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."'";
							$row = $dao->execute(0,$sql)->row_array();
							if($row['n_settlement']==0){

								if($pendaftaran_row['jalur_id']=='4')
								{
									$sql = "SELECT a.*,b.tingkat_kejuaraan,c.bidang_kejuaraan FROM pendaftaran_prestasi as a 
											LEFT JOIN ref_tingkat_kejuaraan as b ON (a.tkt_kejuaraan_id=b.ref_tkt_kejuaraan_id)
											LEFT JOIN ref_bidang_kejuaraan as c ON (a.bdg_kejuaraan_id=c.ref_bdg_kejuaraan_id) 
											WHERE a.id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."'";
									$prestasi_rows = $dao->execute(0,$sql)->result_array();

								}

								$sql = "SELECT count(1) as n FROM debug_procedure 
											WHERE id_pendaftaran='".$pendaftaran_row['id_pendaftaran']."' AND verifikasi='1' AND status='1'";
								
								$row = $dao->execute(0,$sql)->row_array();
								
								$form_debug = ($row['n']>0);

							}else{
								$error = 3;
							}
						}
					}
					else
					{
						$error = 1; //not found
					}
				}
				else{
					$error = 1; //not found;
				}

				$data['dokumen_rows']=$dokumen_rows;
				$data['prestasi_rows']=$prestasi_rows;
				$data['status_urutan']=$status_urutan;
				$data['provinsi'] = $this->_SYS_PARAMS[2];			
				$data['form_id'] = 'verification-form';
				$data['pendaftaran_row']=$pendaftaran_row;
				$data['sekolah_pilihan_row']=$sekolah_pilihan_row;
				
			}else{
				$error = 4;
			}

			$data['error'] = $error;
			$data['form_debug'] = $form_debug;
			$data['active_controller'] = $this->active_controller;

			$this->load->view($this->active_controller.'/verification/form_wizard',$data);
		}

		function get_verification_data($type){

			$this->load->helper(array('scoring_helper'));

			$dao = $this->global_model->get_dao();
			
			$cond = " WHERE a.sekolah_id='".$this->session->userdata('sekolah_id')."'";
			if($type==1)
				$cond .= " AND (a.status='2' or a.status='3')";
			else
				$cond .= " ";

			if($this->session->userdata('tipe_sekolah')=='1' or $this->session->userdata('tipe_sekolah')=='3')
			{

				if($type==1){

					$sql = "SELECT a.id_pendaftaran,'' as kompetensi_id,d.score,
							b.nama,b.sekolah_asal,b.no_pendaftaran,c.ref_jalur_id as jalur_id,
							c.nama_jalur,d.tipe_sekolah_id,
							b.tot_nilai,b.mode_un,d.no_verifikasi 
							FROM pendaftaran_sekolah_pilihan as a 
							LEFT JOIN pendaftaran AS b ON a.id_pendaftaran=b.id_pendaftaran
							LEFT JOIN ref_jalur_pendaftaran as c ON a.jalur_id=c.ref_jalur_id
							LEFT JOIN verifikasi as d ON (a.id_pendaftaran=d.id_pendaftaran AND a.sekolah_id=d.sekolah_id AND a.jalur_id=d.jalur_id)";
				}
				else{
					$sql = "SELECT 
							a.id_pendaftaran,'' as kompetensi_id,
							b.nama,b.sekolah_asal,b.no_pendaftaran,c.ref_jalur_id as jalur_id,
							c.nama_jalur,a.tipe_sekolah_id,
							b.tot_nilai,b.mode_un 
							FROM daftar_ulang as a 
							LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
							LEFT JOIN ref_jalur_pendaftaran as c ON (a.jalur_id=c.ref_jalur_id)";
					
				}

			}else{

				if($type==1){
					
					$sql = "SELECT a.id_pendaftaran,a.kompetensi_id,b.nama,b.sekolah_asal,
							b.no_pendaftaran,c.nama_kompetensi,d.jalur_id,d.nama_jalur,
							b.tot_nilai,b.mode_un,e.score,e.no_verifikasi,e.tipe_sekolah_id
							FROM pendaftaran_kompetensi_pilihan as a 
							LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran)
							LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id) 
							LEFT JOIN (SELECT x.id_pendaftaran,x.jalur_id,y.nama_jalur FROM pendaftaran_jalur_pilihan as x 
								LEFT JOIN ref_jalur_pendaftaran as y ON (x.jalur_id=y.ref_jalur_id)) as d ON (a.id_pendaftaran=d.id_pendaftaran) 
							LEFT JOIN verifikasi as e ON (a.id_pendaftaran=e.id_pendaftaran AND a.sekolah_id=e.sekolah_id AND a.kompetensi_id=e.kompetensi_id AND a.jalur_id=e.jalur_id)";
				
				}else{

					$sql = "SELECT a.id_pendaftaran,a.kompetensi_id,b.nama,b.sekolah_asal, b.no_pendaftaran,c.nama_kompetensi,
							a.jalur_id,d.nama_jalur, b.tot_nilai,b.mode_un,a.tipe_sekolah_id FROM daftar_ulang as a 
							LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
							LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id) 
							LEFT JOIN ref_jalur_pendaftaran as d ON (a.jalur_id=d.ref_jalur_id)";
				}
			}			
			
			$sql .= $cond."  ORDER BY a.id_pendaftaran DESC";
			
			$rows = $dao->execute(0,$sql)->result_array();			
			

			return $rows;
		}

		function load_verification_list(){

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/verification');
			$delete_access = $this->aah->check_privilege('delete',$nav_id);
			$print_access = $this->aah->check_privilege('print',$nav_id);

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$data['tipe_sekolah'] = $this->session->userdata('tipe_sekolah');
			$data['rows'] = $this->get_verification_data(1);
			$data['delete_access'] = $delete_access;
			$data['print_access'] = $print_access;
			$data['active_controller'] = $this->active_controller;
			$data['dao'] = $dao;

			$this->load->view($this->active_controller.'/verification/list_of_data',$data);
		}

		function delete_verification(){
			$this->aah->check_access();

			$this->load->model(array('pendaftaran_kompetensi_pilihan_model','pendaftaran_sekolah_pilihan_model',
									 'log_status_pendaftaran_model','verifikasi_model'));
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$id_pendaftaran = $this->input->post('id_pendaftaran');
			$kompetensi_id = $this->input->post('kompetensi_id');
			$jalur_id = $this->input->post('jalur_id');
			$sekolah_id = $this->session->userdata('sekolah_id');
			$tipe_sekolah_id = $this->session->userdata('tipe_sekolah');
			
			//start transcation
			$this->db->trans_begin();

			//model hasil_seleksi_model
			$m1 = $this->verifikasi_model;

			$tipe_sekolah = $this->session->userdata('tipe_sekolah');
			if($tipe_sekolah=='1' or $tipe_sekolah=='3')
			{	
				$m2 = $this->pendaftaran_sekolah_pilihan_model;
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'sekolah_id'=>$this->session->userdata('sekolah_id'));
			}else{
				$m2 = $this->pendaftaran_kompetensi_pilihan_model;
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'kompetensi_id'=>$kompetensi_id);				
			}

			$m2->set_status('0');
			$result = $dao->update($m2,$cond);
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			//delete current hasil_seleksi
			$result = $dao->delete($m1,array('id_pendaftaran'=>$id_pendaftaran,'sekolah_id'=>$sekolah_id,'kompetensi_id'=>$kompetensi_id,'jalur_id'=>$jalur_id));
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			//insert registration log
			$m3 = $this->log_status_pendaftaran_model;
			$wkt_hapus = date('Y-m-d H:i:s');

			$m3->set_id_pendaftaran($id_pendaftaran);
			$m3->set_thn_pelajaran($this->_SYS_PARAMS[0]);
			$m3->set_status('4');
			$m3->set_jalur_id($jalur_id);
			$m3->set_sekolah_id($sekolah_id);
			$m3->set_created_time($wkt_hapus);
			$m3->set_user($this->session->userdata('username'));
			$result = $dao->insert($m3);
			if(!$result){
				$this->db->trans_rollback();
			}
			$this->db->trans_commit();
			//end of transaction 

			$this->load_verification_list();
		}		

		function load_settlement_list(){
			$nav_id = $this->aah->get_nav_id(__CLASS__.'/settlement');
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			$data['tipe_sekolah'] = $this->session->userdata('tipe_sekolah');
			$data['rows'] = $this->get_verification_data(2);

			$data['delete_access'] = $delete_access;
			$data['active_controller'] = $this->active_controller;

			$this->load->view($this->active_controller.'/settlement/list_of_data',$data);
		}

		function delete_settlement(){
			$this->aah->check_access();
			
			$this->load->model(array('pendaftaran_model','pendaftaran_kompetensi_pilihan_model','pendaftaran_sekolah_pilihan_model',
									'log_status_pendaftaran_model','daftar_ulang_model'));
			$id_pendaftaran = $this->input->post('id_pendaftaran');
			$kompetensi_id = $this->input->post('kompetensi_id');
			$jalur_id = $this->input->post('jalur_id');

			$dao = $this->global_model->get_dao();

			$this->db->trans_begin();

			$m = $this->pendaftaran_model;
			$m->set_status('1');
			$result = $dao->update($m,array('id_pendaftaran'=>$id_pendaftaran));
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}


			if($this->session->userdata('tipe_sekolah')=='1')
			{	
				$m = $this->pendaftaran_sekolah_pilihan_model;							
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'sekolah_id'=>$this->session->userdata('sekolah_id'),'jalur_id'=>$jalur_id);
			}else{
				$m = $this->pendaftaran_kompetensi_pilihan_model;
				$cond = array('id_pendaftaran'=>$id_pendaftaran,'kompetensi_id'=>$kompetensi_id,'jalur_id'=>$jalur_id);				
			}
			$m->set_status('3');
			$result = $dao->update($m,$cond);
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}


			$m = $this->daftar_ulang_model;
			$result = $dao->delete($m,$cond);
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}


			//insert registration log
			$m = $this->log_status_pendaftaran_model;

			$wkt_hapus = date('Y-m-d H:i:s');

			$m->set_id_pendaftaran($id_pendaftaran);
			$m->set_thn_pelajaran($this->_SYS_PARAMS[0]);
			$m->set_status('5');
			$m->set_jalur_id($jalur_id);
			$m->set_sekolah_id($this->session->userdata('sekolah_id'));
			$m->set_created_time($wkt_hapus);
			$m->set_user($this->session->userdata('username'));
			$result = $dao->insert($m);
			if(!$result){
				$this->db->trans_rollback();
				die('failed');
			}

			$this->db->trans_commit();

			$this->load_settlement_list();
		}		

		function get_administration_result($id_pendaftaran,$jalur_id,$tipe_sekolah,$kompetensi_id=''){

			$this->load->helper('scoring_helper');
						
			$sql = "SELECT a.no_pendaftaran,a.id_pendaftaran,
					a.nama,a.jk,a.sekolah_asal,a.alamat,
					b.nama_kecamatan,c.nama_dt2,d.nama_jalur,
					d.jalur_id,d.tipe_sekolah_id,d.ktg_jalur_id,a.tot_nilai,a.mode_un
					FROM ".($jalur_id=='' || $jalur_id!='5'?'ppdb_sulsel':'ppdb_sulsel_tahap1').".pendaftaran as a 
					LEFT JOIN ref_kecamatan as b ON (a.kecamatan_id=b.kecamatan_id) 
					LEFT JOIN ref_dt2 as c ON (a.dt2_id=c.dt2_id)
					LEFT JOIN (SELECT w.id_pendaftaran,w.jalur_id,y.nama_jalur,z.ktg_jalur_id,w.tipe_sekolah_id
						FROM pendaftaran_jalur_pilihan as w	
						LEFT JOIN ref_jalur_pendaftaran as y ON (w.jalur_id=y.ref_jalur_id)
						LEFT JOIN pengaturan_kuota_jalur as z ON (w.jalur_id=z.jalur_id) AND (w.tipe_sekolah_id=z.tipe_sekolah_id)) as d 
					ON (a.id_pendaftaran=d.id_pendaftaran)
					WHERE a.id_pendaftaran='".$id_pendaftaran."'";
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$pendaftaran_row = $dao->execute(0,$sql)->row_array();

			if($tipe_sekolah=='1' or $tipe_sekolah=='3')
			{

				$sql = "SELECT a.id_pendaftaran,'0' as kompetensi_id,b.nama_sekolah,b.tipe_sekolah_id,
						b.nama_tipe_sekolah,b.akronim,a.pilihan_ke,c.jarak,c.score,
						DATE_FORMAT(c.created_time,'%Y-%m-%d') as tgl_verifikasi,c.no_verifikasi
						FROM pendaftaran_sekolah_pilihan as a 
						LEFT JOIN (SELECT x.sekolah_id,x.nama_sekolah,x.tipe_sekolah_id,y.nama_tipe_sekolah,y.akronim 
							FROM sekolah as x LEFT JOIN ref_tipe_sekolah as y ON (x.tipe_sekolah_id=y.ref_tipe_sklh_id)) as b ON (a.sekolah_id=b.sekolah_id) 
						LEFT JOIN verifikasi as c ON (a.id_pendaftaran=c.id_pendaftaran AND a.sekolah_id=c.sekolah_id AND a.jalur_id=c.jalur_id)						
						WHERE a.id_pendaftaran='".$id_pendaftaran."' AND 
						a.sekolah_id='".$this->session->userdata('sekolah_id')."'";
			}else{


				$sql = "SELECT a.id_pendaftaran,a.kompetensi_id,b.nama_sekolah,b.tipe_sekolah_id,
						b.nama_tipe_sekolah,b.akronim,
						c.nama_kompetensi,a.pilihan_ke,d.score,
						DATE_FORMAT(d.created_time,'%Y-%m-%d') as tgl_verifikasi,d.no_verifikasi
						FROM pendaftaran_kompetensi_pilihan as a 
						LEFT JOIN (SELECT x.sekolah_id,x.nama_sekolah,x.tipe_sekolah_id,y.nama_tipe_sekolah,y.akronim 
									FROM sekolah as x LEFT JOIN ref_tipe_sekolah as y ON (x.tipe_sekolah_id=y.ref_tipe_sklh_id)) as b ON (a.sekolah_id=b.sekolah_id)  
						LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id)
						LEFT JOIN verifikasi as d ON (a.id_pendaftaran=d.id_pendaftaran AND a.sekolah_id=d.sekolah_id AND a.jalur_id=d.jalur_id)						
						WHERE a.id_pendaftaran='".$id_pendaftaran."' AND a.kompetensi_id='".$kompetensi_id."'";
			}

			$sekolah_pilihan_row = $dao->execute(0,$sql)->row_array();
			
			return array('pendaftaran_row'=>$pendaftaran_row,'sekolah_pilihan_row'=>$sekolah_pilihan_row,);
		}

		function print_verification($school_type,$encoded_regid,$encoded_fieldid=''){
			$this->aah->check_access();

			$this->load->helper('date_helper');

			$decoded_regid = base64_decode(urldecode($encoded_regid));
			$decoded_fieldid = base64_decode(urldecode($encoded_fieldid));

			$data = $this->get_administration_result($decoded_regid,'',$school_type,$decoded_fieldid);

			$this->load->view($this->active_controller.'/verification/print_verification',$data);
		}

		function print_settlement($school_type,$encoded_regid,$encoded_fieldid=''){

			$this->aah->check_access();

			$this->load->helper('date_helper');
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$decoded_regid = base64_decode(urldecode($encoded_regid));
			$decoded_fieldid = base64_decode(urldecode($encoded_fieldid));


			if($school_type=='1'  or $school_type=='3'){
				$sql = "SELECT DATE_FORMAT(created_time,'%Y-%m-%d') as tgl_daftar_ulang,a.tipe_sekolah_id,
						b.nama_sekolah,b.nama_tipe_sekolah,a.jalur_id,c.nama_jalur FROM daftar_ulang as a 
						LEFT JOIN (SELECT a.sekolah_id,a.nama_sekolah,b.nama_tipe_sekolah 
							FROM sekolah as a 
							LEFT JOIN ref_tipe_sekolah as b ON (a.tipe_sekolah_id=b.ref_tipe_sklh_id)) as b ON (a.sekolah_id=b.sekolah_id)
						LEFT JOIN ref_jalur_pendaftaran as c ON (a.jalur_id=c.ref_jalur_id) 
						WHERE a.id_pendaftaran='".$decoded_regid."'";
			}else{
				$sql = "SELECT DATE_FORMAT(created_time,'%Y-%m-%d') as tgl_daftar_ulang,a.tipe_sekolah_id,
						b.nama_sekolah,b.nama_tipe_sekolah,c.nama_kompetensi,a.jalur_id,d.nama_jalur FROM daftar_ulang as a 
						LEFT JOIN (SELECT a.sekolah_id,a.nama_sekolah,b.nama_tipe_sekolah 
							FROM sekolah as a 
							LEFT JOIN ref_tipe_sekolah as b ON (a.tipe_sekolah_id=b.ref_tipe_sklh_id)) as b ON (a.sekolah_id=b.sekolah_id)
						LEFT JOIN kompetensi_smk as c ON (a.kompetensi_id=c.kompetensi_id)
						LEFT JOIN ref_jalur_pendaftaran as d ON (a.jalur_id=d.ref_jalur_id) 
						WHERE a.id_pendaftaran='".$decoded_regid."'";
			}

			$daftar_ulang_row = $dao->execute(0,$sql)->row_array();

			$data = $this->get_administration_result($decoded_regid,$daftar_ulang_row['jalur_id'],$school_type,$decoded_fieldid);
			$data['daftar_ulang_row'] = $daftar_ulang_row;
			$this->load->view($this->active_controller.'/settlement/print_settlement',$data);
		}
		

		// DOCUMENTS FUNCTION PACKET
		function documents(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/documents');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($read_access){
				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['containsTable'] = true;
				
				$data['add_access'] = $add_access;
				
				$data2['update_access'] = $update_access;
				$data2['delete_access'] = $delete_access;
				$data2['rows'] = $this->get_document_data();

				$data['list_of_data'] = $this->load->view($this->active_controller.'/documents/list_of_data',$data2,true);
				$this->backoffice_template->render($this->active_controller.'/documents/index',$data);
			}else{
				$this->error_403();
			}
		}

		function get_document_data(){
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();			

			$sql = "SELECT * FROM ref_dokumen_persyaratan";
			
			$rows = $dao->execute(0,$sql)->result_array();
			return $rows;
		}

		function load_document_form(){
			$this->aah->check_access();

			$this->load->model(array('ref_dokumen_persyaratan_model'));

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$act = $this->input->post('act');
			$id_name = 'admin_id';		    

		    $m = $this->ref_dokumen_persyaratan_model;
		    $id_value = ($act=='edit'?$this->input->post('id'):'');
		    $curr_data = $dao->get_data_by_id($act,$m,$id_value);		    

		    $data['curr_data'] = $curr_data;
		    $data['form_id'] = 'user-form';
		    $data['id_value'] = $id_value;		    
		    $data['act'] = $act;		 
		    $data['active_controller'] = $this->active_controller;

			$this->load->view($this->active_controller.'/documents/form_content',$data);
		}		

		function submit_document_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/documents');
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);
			$act = $this->input->post('act');

			if(($act=='add' and $add_access) or ($act=='edit' and $update_access))
			{

				$this->load->model(array('ref_dokumen_persyaratan_model'));

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				
				$id = $this->input->post('id');
				$nama_dokumen = $this->security->xss_clean($this->input->post('input_nama_dokumen'));				

				$m = $this->ref_dokumen_persyaratan_model;

				$m->set_nama_dokumen($nama_dokumen);				

				if($act=='add')
				{
					$id = $this->global_model->get_incrementID('ref_dokumen_id','ref_dokumen_persyaratan');
					$m->set_ref_dokumen_id($id);
					$result = $dao->insert($m);
					$label = 'menyimpan';
				}
				else
				{
					$result = $dao->update($m,array('ref_dokumen_id'=>$id));
					$label = 'merubah';
				}

				if(!$result)
				{
					die('ERROR: gagal '.$label.' data');
				}

				$data2['update_access'] = $update_access;
				$data2['delete_access'] = $delete_access;
				$data2['rows'] = $this->get_document_data();

				$this->load->view($this->active_controller.'/documents/list_of_data',$data2);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk '.($act=='add'?'menambah':'merubah').' data!';
			}
		}

		function delete_document_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/documents');
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($delete_access)
			{
				$this->load->model(array('ref_dokumen_persyaratan_model'));

				$id = $this->input->post('id');

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$m = $this->ref_dokumen_persyaratan_model;
				$result = $dao->delete($m,array('ref_dokumen_id'=>$id));
				if(!$result){
					die('ERROR: gagal menghapus data');
				}

				$data2['update_access'] = $update_access;
				$data2['delete_access'] = $delete_access;
				$data2['rows'] = $this->get_document_data();

				$this->load->view($this->active_controller.'/documents/list_of_data',$data2);

			}else{
				echo 'ERROR: anda tidak diijinkan untuk menghapus data!';
			}
		}

		//END OF DOCUMENTS FUNCTION PACKET


		//BORDER REGENCY FUNCTION PACKET
		function border_regency(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_regency');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);

			if($read_access){
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				$data['active_url'] = str_replace('::','/',__METHOD__);

				$sql = "SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'";
				if($this->session->userdata('admin_type_id')=='3'){
					$sql .= " AND dt2_id='".$this->session->userdata('dt2_id')."'";
				}

				$data['dt2_rows'] = $dao->execute(0,$sql)->result_array();
				$data['form_id'] = "search-border-regency-form";
				$data['active_controller'] = $this->active_controller;
				$data['containsTable'] = true;
				$data['add_access'] = $add_access;

				$data_view = '';
				if($this->session->userdata('admin_type_id')=='3'){
					$data_view = $this->search_border_regency_data(true);
				}
				$data['data_view'] = $data_view;

				$this->backoffice_template->render($this->active_controller.'/border_regency/index',$data);
			}else{
				$this->error_403();
			}
		}

		function search_border_regency_data($return=false){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_regency');			
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($this->session->userdata('admin_type_id')=='3')
				$search_dt2 = $this->session->userdata('dt2_id');
			else
				$search_dt2 = $this->input->post('search_dt2');

			$data2['update_access'] = $update_access;
			$data2['delete_access'] = $delete_access;
			$data2['rows'] = $this->get_border_regency_data($search_dt2);
			$data2['search_dt2'] = $search_dt2;

			$data['list_of_data'] = $this->load->view($this->active_controller.'/border_regency/list_of_data',$data2,true);

			if($return){				
				return $this->load->view($this->active_controller.'/border_regency/data_view',$data,true);
			}else{
				$this->load->view($this->active_controller.'/border_regency/data_view',$data);
			}
		}

		function get_border_regency_data($dt2){
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$cond = "WHERE status='perbatasan'";
			if(!empty($dt2))
				$cond .= " AND a.dt2_id='".$dt2."'";			

			$sql = "SELECT a.*,b.nama_dt2,c.nama_dt2 as nama_dt2_perbatasan FROM pengaturan_dt2_sekolah as a 
					LEFT JOIN ref_dt2 as b ON (a.dt2_id=b.dt2_id) 
					LEFT JOIN ref_dt2 as c ON (a.dt2_sekolah_id=c.dt2_id) 
					".$cond;
			
			$rows = $dao->execute(0,$sql)->result_array();
			return $rows;
		}

		function load_border_regency_form(){
			$this->aah->check_access();

			$this->load->model(array('pengaturan_dt2_sekolah_model'));
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$act = $this->input->post('act');
			$search_dt2 = $this->input->post('search_dt2');

		    $m = $this->pengaturan_dt2_sekolah_model;
		   		    
		    $dt2_id = ($act=='edit'?$this->input->post('dt2_id'):'');
		    $dt2_sekolah_id = ($act=='edit'?$this->input->post('dt2_sekolah_id'):'');

		    $id_value = array('dt2_id'=>'','dt2_sekolah_id'=>'');
		    if($act=='edit'){
		    	$id_value['dt2_id'] = $dt2_id;
		    	$id_value['dt2_sekolah_id'] = $dt2_sekolah_id;
		    }
		    $curr_data = $dao->get_data_by_id($act,$m,$id_value);

		    $dt2_sekolah_opts = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'")->result_array();
		    if($this->session->userdata('admin_type_id')=='3'){
				$sql = "SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."' AND dt2_id='".$this->session->userdata('dt2_id')."'";
				$dt2_opts = $dao->execute(0,$sql)->result_array();
		    }
		    else
		    	$dt2_opts = $dt2_sekolah_opts;

		    $data['dt2_opts'] = $dt2_opts;
		    $data['dt2_sekolah_opts'] = $dt2_sekolah_opts;
		    $data['curr_data'] = $curr_data;
		    $data['active_controller'] = $this->active_controller;
		    $data['form_id'] = 'border-regency-form';
		    $data['dt2_id'] = $dt2_id;
		    $data['dt2_sekolah_id'] = $dt2_sekolah_id;
		    $data['act'] = $act;
		    $data['search_dt2'] = $search_dt2;

			$this->load->view($this->active_controller.'/border_regency/form_content',$data);
		}

		function submit_border_regency_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_regency');
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);
			$act = $this->input->post('act');

			if(($act=='add' and $add_access) or ($act=='edit' and $update_access))
			{
				$this->load->model(array('pengaturan_dt2_sekolah_model'));

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$_dt2_id = $this->input->post("dt2_id");
				$_dt2_sekolah_id = $this->input->post("dt2_sekolah_id");

				if($this->session->userdata('admin_type_id')=='3'){
					$search_dt2 = $this->session->userdata('dt2_id');
					$dt2_id = $this->session->userdata('dt2_id');
				}
				else{
					$search_dt2 = $this->input->post('search_dt2');
					$dt2_id = $this->security->xss_clean($this->input->post('input_dt2_id'));
				}

				$dt2_sekolah_id = $this->security->xss_clean($this->input->post('input_dt2_sekolah_id'));

				$m = $this->pengaturan_dt2_sekolah_model;

				$m->set_dt2_id($dt2_id);
				$m->set_dt2_sekolah_id($dt2_sekolah_id);
				$m->set_status('perbatasan');

				$this->db->trans_begin();
				if($act=='add')
				{
					//check domicile
					$sql = "SELECT COUNT(1) as n_domisili FROM pengaturan_dt2_sekolah WHERE dt2_id='".$dt2_id."' AND status='domisili'";
					$row = $dao->execute(0,$sql)->row_array();
					if($row['n_domisili']==0){
						$m2 = $this->pengaturan_dt2_sekolah_model;
						$m2->set_dt2_id($dt2_id);
						$m2->set_dt2_sekolah_id($dt2_id);
						$m2->set_status('domisili');
						$result = $dao->insert($m2);
						if(!$result){
							$this->db->trans_rollback();
							die('ERROR: gagal menyimpan data');
						}
					}

					$result = $dao->insert($m);
					$label = 'menyimpan';
				}
				else
				{
					$result = $dao->update($m,array('dt2_id'=>$_dt2_id,'dt2_sekolah_id'=>$_dt2_sekolah_id));
				
					$label = 'merubah';
				}

				if(!$result)
				{
					$this->db->trans_rollback();
					die('ERROR: gagal '.$label.' data');
				}
				$this->db->trans_commit();

				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_border_regency_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/border_regency/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk '.($act=='add'?'menambah':'merubah').' data!';
			}

		}

		function delete_border_regency_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_regency');
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($delete_access)
			{
				$this->load->model(array('pengaturan_dt2_sekolah_model'));

				$dt2_id = $this->input->post('dt2_id');
				$dt2_sekolah_id = $this->input->post('dt2_sekolah_id');

				if($this->session->userdata('admin_type_id')=='3')
					$search_dt2 = $this->session->userdata('dt2_id');
				else
					$search_dt2 = $this->input->post('search_dt2');
				
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$m = $this->pengaturan_dt2_sekolah_model;
				$result = $dao->delete($m,array('dt2_id'=>$dt2_id,'dt2_sekolah_id'=>$dt2_sekolah_id));
				if(!$result){
					die('ERROR: gagal menghapus data');
				}
				
				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_border_regency_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/border_regency/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk menghapus data!';
			}
		}
		//END OF BORDER REGENCY FUNCTION PACKET



		//BORDER SCHOOL FUNCTION PACKET
		function border_school(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_school');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);

			if($read_access){
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				$data['active_url'] = str_replace('::','/',__METHOD__);

				$sql = "SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'";
				if($this->session->userdata('admin_type_id')=='3'){
					$sql .= " AND dt2_id='".$this->session->userdata('dt2_id')."'";
				}

				$data['dt2_rows'] = $dao->execute(0,$sql)->result_array();
				$data['form_id'] = "search-border-school-form";
				$data['active_controller'] = $this->active_controller;
				$data['containsTable'] = true;
				$data['add_access'] = $add_access;

				$data_view = '';
				if($this->session->userdata('admin_type_id')=='3'){
					$data_view = $this->search_border_school_data(true);
				}
				$data['data_view'] = $data_view;

				$this->backoffice_template->render($this->active_controller.'/border_school/index',$data);
			}else{
				$this->error_403();
			}
		}

		function search_border_school_data($return=false){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_school');			
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($this->session->userdata('admin_type_id')=='3')
				$search_dt2 = $this->session->userdata('dt2_id');
			else
				$search_dt2 = $this->input->post('search_dt2');

			$data2['update_access'] = $update_access;
			$data2['delete_access'] = $delete_access;
			$data2['rows'] = $this->get_border_school_data($search_dt2);
			$data2['search_dt2'] = $search_dt2;

			$data['list_of_data'] = $this->load->view($this->active_controller.'/border_school/list_of_data',$data2,true);

			if($return){
				return $this->load->view($this->active_controller.'/border_school/data_view',$data,true);
			}else{
				$this->load->view($this->active_controller.'/border_school/data_view',$data);
			}
		}

		function get_border_school_data($dt2){
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$cond = "";
			if(!empty($dt2))
				$cond .= " WHERE a.dt2_id='".$dt2."'";

			$sql = "SELECT a.sekper_id,b.nama_dt2,c.nama_sekolah FROM pengaturan_sekolah_perbatasan as a 
					LEFT JOIN ref_dt2 as b ON (a.dt2_perbatasan_id=b.dt2_id)
					LEFT JOIN sekolah as c ON (a.sekolah_id=c.sekolah_id)
					".$cond;

			$rows = $dao->execute(0,$sql)->result_array();
			return $rows;
		}

		function load_border_school_form(){
			$this->aah->check_access();

			$this->load->model(array('pengaturan_sekolah_perbatasan_model'));
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			
			$act = $this->input->post('act');
			$search_dt2 = $this->input->post('search_dt2');

		    $m = $this->pengaturan_sekolah_perbatasan_model;
		   		    
		    $id_name = 'sekper_id';
		    $id_value = ($act=='edit'?$this->input->post('id'):'');

		    $curr_data = $dao->get_data_by_id($act,$m,$id_value);

		    if($this->session->userdata('admin_type_id')=='3'){
				$sql = "SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."' AND dt2_id='".$this->session->userdata('dt2_id')."'";
				$dt2_opts = $dao->execute(0,$sql)->result_array();
		    }
		    else
		    	$dt2_opts = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."'")->result_array();

		    $sekolah_opts = array();
		    $dt2_sekolah_opts = array();
		    if($this->session->userdata('admin_type_id')=='3' or $act=='edit'){
		    	$sql = "SELECT dt2_sekolah_id as dt2_id,b.nama_dt2 FROM pengaturan_dt2_sekolah as a LEFT JOIN ref_dt2 as b ON (a.dt2_sekolah_id=b.dt2_id) 
		   				WHERE a.dt2_id='".($this->session->userdata('admin_type_id')=='3'?$this->session->userdata('dt2_id'):$curr_data['dt2_id'])."' 
		   				AND status='perbatasan'";

		   		$dt2_sekolah_opts = $dao->execute(0,$sql)->result_array();
			   	if($act=='edit'){
		    		$sql = "SELECT sekolah_id,nama_sekolah FROM sekolah WHERE dt2_id='".$curr_data['dt2_perbatasan_id']."'";
		    		$sekolah_opts = $dao->execute(0,$sql)->result_array();

			    }
			}

		    $data['dt2_opts'] = $dt2_opts;
		    $data['dt2_sekolah_opts'] = $dt2_sekolah_opts;
		    $data['sekolah_opts'] = $sekolah_opts;
		    $data['curr_data'] = $curr_data;
		    $data['active_controller'] = $this->active_controller;
		    $data['form_id'] = 'border-school-form';
		    $data['id_value'] = $id_value;
		    $data['act'] = $act;
		    $data['search_dt2'] = $search_dt2;

			$this->load->view($this->active_controller.'/border_school/form_content',$data);
		}

		function submit_border_school_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_school');
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);
			$act = $this->input->post('act');

			if(($act=='add' and $add_access) or ($act=='edit' and $update_access))
			{
				$this->load->model(array('pengaturan_sekolah_perbatasan_model'));

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();				

				if($this->session->userdata('admin_type_id')=='3'){
					$search_dt2 = $this->session->userdata('dt2_id');
					$dt2_id = $this->session->userdata('dt2_id');
				}
				else{
					$search_dt2 = $this->input->post('search_dt2');
					$dt2_id = $this->security->xss_clean($this->input->post('input_dt2_id'));
				}

				$dt2_perbatasan_id = $this->security->xss_clean($this->input->post('input_dt2_perbatasan_id'));
				$sekolah_id = $this->security->xss_clean($this->input->post('input_sekolah_id'));

				$m = $this->pengaturan_sekolah_perbatasan_model;

				$m->set_dt2_id($dt2_id);
				$m->set_dt2_perbatasan_id($dt2_perbatasan_id);
				$m->set_sekolah_id($sekolah_id);

				$this->db->trans_begin();
				if($act=='add')
				{
					$id = $this->global_model->get_incrementID('sekper_id','pengaturan_sekolah_perbatasan');
					$m->set_sekper_id($id);
					$result = $dao->insert($m);					
					$label = 'menyimpan';
				}
				else
				{
					$id = $this->input->post('id');
					$result = $dao->update($m,array('sekper_id'=>$id));
					$label = 'merubah';
				}

				if(!$result)
				{
					$this->db->trans_rollback();
					die('ERROR: gagal '.$label.' data');
				}
				$this->db->trans_commit();

				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_border_school_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/border_school/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk '.($act=='add'?'menambah':'merubah').' data!';
			}

		}

		function delete_border_school_data(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/border_regency');
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			if($delete_access)
			{
				$this->load->model(array('pengaturan_sekolah_perbatasan_model'));				

				if($this->session->userdata('admin_type_id')=='3')
					$search_dt2 = $this->session->userdata('dt2_id');
				else
					$search_dt2 = $this->input->post('search_dt2');
				
				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$id = $this->input->post('id');

				$m = $this->pengaturan_sekolah_perbatasan_model;
				$result = $dao->delete($m,array('sekper_id'=>$id));
				if(!$result){
					die('ERROR: gagal menghapus data');
				}
				
				$data['update_access'] = $update_access;
				$data['delete_access'] = $delete_access;
				$data['rows'] = $this->get_border_school_data($search_dt2);
				$data['search_dt2'] = $search_dt2;

				$this->load->view($this->active_controller.'/border_school/list_of_data',$data);
			}else{
				echo 'ERROR: anda tidak diijinkan untuk menghapus data!';
			}
		}
		//END OF BORDER SCHOOL FUNCTION PACKET


		function submit_registration_data(){
			$this->aah->check_access();

			$this->load->helper('date_helper');
			$this->load->model(array('pendaftaran_model'));

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/input_registration');
			$add_access = $this->aah->check_privilege('add',$nav_id);
			$act = $this->input->post('act');

			if($act=='add')
			{

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();

				$id_pendaftaran = $this->security->xss_clean($this->input->post('input_id_pendaftaran'));

				$sql = "SELECT COUNT(1) as n FROM pendaftaran WHERE id_pendaftaran='".$id_pendaftaran."'";
				$row = $dao->execute(0,$sql)->row_array();
				if($row['n']>0){
					die('ERROR: ID Pendaftaran sudah digunakan!');
				}

				$nama = $this->security->xss_clean($this->input->post('input_nama'));
				$jk = $this->security->xss_clean($this->input->post('input_jk'));
				$tpt_lahir = $this->security->xss_clean($this->input->post('input_tpt_lahir'));
				$tgl_lahir = us_date_format($this->security->xss_clean($this->input->post('input_tgl_lahir')));
				$sekolah_asal = $this->security->xss_clean($this->input->post('input_sekolah_asal'));
				$nm_orang_tua = $this->security->xss_clean($this->input->post('input_nm_orang_tua'));
				
				$mode_un = $this->security->xss_clean($this->input->post('input_mode_un'));
				$nil_bhs_indonesia = $this->security->xss_clean($this->input->post('input_nil_bhs_indonesia'));
				$nil_bhs_inggris = $this->security->xss_clean($this->input->post('input_nil_bhs_inggris'));
				$nil_matematika = $this->security->xss_clean($this->input->post('input_nil_matematika'));
				$nil_ipa = $this->security->xss_clean($this->input->post('input_nil_ipa'));
				$tot_nilai = $this->security->xss_clean($this->input->post('input_tot_nilai'));

				$m = $this->pendaftaran_model;

				$m->set_id_pendaftaran($id_pendaftaran);
				$m->set_nama($nama);
				$m->set_jk($jk);
				$m->set_tpt_lahir($tpt_lahir);
				$m->set_tgl_lahir($tgl_lahir);
				$m->set_sekolah_asal($sekolah_asal);
				$m->set_nm_orang_tua($nm_orang_tua);
				$m->set_mode_un($mode_un);
				$m->set_nil_bhs_indonesia($nil_bhs_indonesia);
				$m->set_nil_bhs_inggris($nil_bhs_inggris);
				$m->set_nil_matematika($nil_matematika);
				$m->set_nil_ipa($nil_ipa);
				$m->set_tot_nilai($tot_nilai);
				$m->set_status('0');
				
				$result = $dao->insert($m);
				$label = 'menyimpan';				

				if(!$result)
				{
					die('ERROR: gagal '.$label.' data');
				}
								
			}else{
				echo 'ERROR: anda tidak diijinkan untuk '.($act=='add'?'menambah':'merubah').' data!';
			}
		}

		function input_registration(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/input_registration');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);

			$data['active_url'] = str_replace('::','/',__METHOD__);
			$data['form_id'] = "input-registration-form";
			$data['active_controller'] = $this->active_controller;
			$data['containsTable'] = false;
			$data['add_access'] = $add_access;

			$this->backoffice_template->render($this->active_controller.'/input_registration/reg_form',$data);
		}



		//RECON FUNCTION PACKET
		function recon(){
			$this->aah->check_access();

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/recon');
			$read_access = $this->aah->check_privilege('read',$nav_id);
			$add_access = $this->aah->check_privilege('add',$nav_id);

			if($read_access){

				$this->global_model->reinitialize_dao();
				$dao = $this->global_model->get_dao();
				
				$sql = "SELECT * FROM ref_jalur_pendaftaran";

				$kompetensi_rows = array();
				if($this->session->userdata('tipe_sekolah')=='2'){
					$kompetensi_rows = $dao->execute(0,"SELECT * FROM kompetensi_smk WHERE sekolah_id='".$this->session->userdata('sekolah_id')."'")->result_array();
				}

				$data['active_url'] = str_replace('::','/',__METHOD__);
				$data['jalur_rows'] = $dao->execute(0,$sql)->result_array();
				$data['form_id'] = "search-recon-form";
				$data['active_controller'] = $this->active_controller;
				$data['add_access'] = $add_access;
				$data['kompetensi_rows'] = $kompetensi_rows;

				$this->backoffice_template->render($this->active_controller.'/recon/index',$data);
			}else{
				$this->error_403();
			}
		}

		function search_recon_data(){
			$this->load->helper('scoring_helper');
			$jalur_id = $this->input->post('search_jalur');
			$sekolah_id = $this->session->userdata('sekolah_id');
			$tipe_sekolah = $this->session->userdata('tipe_sekolah');
			$kompetensi_id = $this->input->post('search_kompetensi');

			$nav_id = $this->aah->get_nav_id(__CLASS__.'/recon');			
			$update_access = $this->aah->check_privilege('update',$nav_id);
			$delete_access = $this->aah->check_privilege('delete',$nav_id);

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			if($tipe_sekolah=='1')
			{

				$extCond = "";
				if($jalur_id=='1' or $jalur_id=='2' or $jalur_id=='3'){
					$extCond = " AND a.jarak_sekolah is not null";
				}else if($jalur_id=='4'){
					$extCond = "";
				}else{
					$extCond = "";
				}

				$sql = "SELECT a.id_pendaftaran,a.jalur_id,a.sekolah_id,a.pilihan_ke,c.no_pendaftaran,c.nama,c.sekolah_asal,c.alamat,
						b.tipe_sekolah_id,a.jarak_sekolah,c.tot_nilai,c.mode_un, 
						(SELECT count(1) FROM rekonsilidasi_sekolah_pilihan as x WHERE x.id_pendaftaran=a.id_pendaftaran AND
							x.sekolah_id=a.sekolah_id AND x.jalur_id=a.jalur_id) as n_rekon,
						(SELECT count(1) FROM rekonsilidasi_sekolah_pilihan as x WHERE x.id_pendaftaran=a.id_pendaftaran AND
							x.sekolah_id=a.sekolah_id AND x.jalur_id=a.jalur_id AND x.koreksi='1') as n_correction
						FROM pendaftaran_sekolah_pilihan as a 
						LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)
						LEFT JOIN pendaftaran as c ON (a.id_pendaftaran=c.id_pendaftaran) 
						WHERE a.jalur_id='".$jalur_id."' AND a.sekolah_id='".$sekolah_id."' ".$extCond;
				$sql .= " ORDER BY c.no_pendaftaran ASC";
			
				$rows = $dao->execute(0,$sql)->result_array();

				$sql = "SELECT COUNT(1) as n_data FROM rekonsilidasi_sekolah_pilihan WHERE jalur_id='".$jalur_id."' AND sekolah_id='".$sekolah_id."'";
				$row = $dao->execute(0,$sql)->row_array();
				$n_data = $row['n_data'];

				$sql = "SELECT COUNT(1) as n_data FROM rekonsilidasi_sekolah_pilihan WHERE jalur_id='".$jalur_id."' AND sekolah_id='".$sekolah_id."' AND koreksi='1'";
				$row = $dao->execute(0,$sql)->row_array();
				$n_koreksi = $row['n_data'];


			}else{

				$extCond = "";
				
				$sql = "SELECT a.id_pendaftaran,a.jalur_id,a.sekolah_id,a.kompetensi_id,a.pilihan_ke,c.no_pendaftaran,c.nama,c.sekolah_asal,c.alamat,
						b.tipe_sekolah_id,c.tot_nilai,c.mode_un,'' as jarak_sekolah,
						(SELECT count(1) FROM rekonsilidasi_kompetensi_pilihan as x WHERE x.id_pendaftaran=a.id_pendaftaran AND
							x.sekolah_id=a.sekolah_id AND x.kompetensi_id=a.kompetensi_id AND x.jalur_id=a.jalur_id) as n_rekon,
						(SELECT count(1) FROM rekonsilidasi_kompetensi_pilihan as x WHERE x.id_pendaftaran=a.id_pendaftaran AND
							x.sekolah_id=a.sekolah_id AND x.kompetensi_id=a.kompetensi_id AND x.jalur_id=a.jalur_id AND x.koreksi='1') as n_correction
						FROM pendaftaran_kompetensi_pilihan as a 
						LEFT JOIN sekolah as b ON (a.sekolah_id=b.sekolah_id)
						LEFT JOIN pendaftaran as c ON (a.id_pendaftaran=c.id_pendaftaran) 
						WHERE a.jalur_id='".$jalur_id."' AND a.sekolah_id='".$sekolah_id."' AND a.kompetensi_id='".$kompetensi_id."'";
				$sql .= " ORDER BY c.no_pendaftaran ASC";
			
				$rows = $dao->execute(0,$sql)->result_array();
				
				$sql = "SELECT COUNT(1) as n_data FROM rekonsilidasi_kompetensi_pilihan WHERE jalur_id='".$jalur_id."' AND sekolah_id='".$sekolah_id."' AND kompetensi_id='".$kompetensi_id."'";
				$row = $dao->execute(0,$sql)->row_array();
				$n_data = $row['n_data'];

				$sql = "SELECT COUNT(1) as n_data FROM rekonsilidasi_kompetensi_pilihan WHERE jalur_id='".$jalur_id."' AND kompetensi_id='".$kompetensi_id."' AND sekolah_id='".$sekolah_id."' AND koreksi='1'";
				$row = $dao->execute(0,$sql)->row_array();
				$n_koreksi = $row['n_data'];
			}

			$data2['update_access'] = $update_access;
			$data2['delete_access'] = $delete_access;
			$data2['rows'] = $rows;
			$data2['jalur_id'] = $jalur_id;
			$data2['dao'] = $dao;
			$data2['active_controller'] = $this->active_controller;
			$data2['form_id'] = "submit-recon-form";
			$data2['tipe_sekolah'] = $tipe_sekolah;
			$data2['sekolah_id'] = $sekolah_id;
			$data2['kompetensi_id'] = $kompetensi_id;
			$data2['n_data'] = $n_data;
			$data2['n_koreksi'] = $n_koreksi;
			$data['list_of_data'] = $this->load->view($this->active_controller.'/recon/list_of_data',$data2,true);
			
			$data['active_url'] = str_replace('::','/',__METHOD__);
			
			$this->load->view($this->active_controller.'/recon/data_view',$data);
		}

		function submit_recon_data(){

			$this->load->model(array('rekonsilidasi_sekolah_pilihan_model','rekonsilidasi_kompetensi_pilihan_model'));

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$n_rows = $this->input->post('n_rows');
			$sekolah_id = $this->input->post('sekolah_id');
			$tipe_sekolah = $this->input->post('tipe_sekolah');
			$kompetensi_id = $this->input->post('kompetensi_id');
			$jalur_id = $this->input->post('jalur_id');
			
			
			if($tipe_sekolah=='1'){
				$m = $this->rekonsilidasi_sekolah_pilihan_model;
				// $result = $dao->delete($m,array('sekolah_id'=>$sekolah_id,'jalur_id'=>$jalur_id));
			}else{
				$m = $this->rekonsilidasi_kompetensi_pilihan_model;
				// $result = $dao->delete($m,array('sekolah_id'=>$sekolah_id,'kompetensi_id'=>$kompetensi_id,'jalur_id'=>$jalur_id));
			}

			// if(!$result){
			// 	$this->db->trans_rollback();
			// 	die('failed');
			// }

			if($tipe_sekolah=='1'){
				for($i=1;$i<=$n_rows;$i++){

					if(!is_null($this->input->post('verified'.$i))){
						$id_pendaftaran = $this->input->post('id_pendaftaran'.$i);
						$pilihan_ke = $this->input->post('pilihan_ke'.$i);
						$correction = (!is_null($this->input->post('correction'.$i))?'1':'0');

						$sql = "SELECT COUNT(1) as n FROM rekonsilidasi_sekolah_pilihan WHERE 
								sekolah_id='".$sekolah_id."' AND jalur_id='".$jalur_id."' AND id_pendaftaran='".$id_pendaftaran."'";
					
						$row = $dao->execute(0,$sql)->row_array();
					
						if($row['n']==0)
						{
							$m->set_id_pendaftaran($id_pendaftaran);
							$m->set_jalur_id($jalur_id);
							$m->set_sekolah_id($sekolah_id);
							$m->set_pilihan_ke($pilihan_ke);
							$m->set_koreksi($correction);

							$result = $dao->insert($m);
							if(!$result){
								die('failed');
							}		
						}
					}
				}
			}else{
				for($i=1;$i<=$n_rows;$i++){

					if(!is_null($this->input->post('verified'.$i))){
						$id_pendaftaran = $this->input->post('id_pendaftaran'.$i);
						$pilihan_ke = $this->input->post('pilihan_ke'.$i);
						$correction = (!is_null($this->input->post('correction'.$i))?'1':'0');

						$sql = "SELECT COUNT(1) as n FROM rekonsilidasi_kompetensi_pilihan WHERE sekolah_id='".$sekolah_id."' 
								AND jalur_id='".$jalur_id." AND kompetensi_id='".$kompetensi_id."' AND id_pendaftaran='".$id_pendaftaran."'";
						$row = $dao->execute(0,$sql)->row_array();
						if($row['n']==0){
							$m->set_id_pendaftaran($id_pendaftaran);
							$m->set_jalur_id($jalur_id);
							$m->set_sekolah_id($sekolah_id);
							$m->set_kompetensi_id($kompetensi_id);
							$m->set_pilihan_ke($pilihan_ke);
							$m->set_koreksi($correction);

							$result = $dao->insert($m);
							if(!$result){
								die('failed');
							}
						}		
					}
				}

			}

			// $this->db->trans_commit();
		}

	}
?>


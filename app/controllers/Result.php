 <?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class result extends CI_Controller{

		public $active_controller;

		function __construct(){
			
			parent::__construct();

			$this->load->library(array('public_template','DAO','session'));
			$this->load->helper('url');
			$this->load->model(array('ketentuan_umum_model','ref_tipe_sekolah_model','global_model','pendaftaran_model'));
			$this->active_controller = __CLASS__;
			$this->_SYS_PARAMS = $this->global_model->get_system_params();

			if($this->_SYS_PARAMS[11]=='no'){
				die("<center><font color='red'>You don't have permission to access this pagessss!!!</font></center>");
			}


		}

		function generate_breadcrumbs($submenu){

			$breadcrumbs = array(
							array('url'=>base_url(),'text'=>'Home','active'=>false),
							array('url'=>base_url().'result/'.$submenu[0],'text'=>$submenu[1],'active'=>false),
						);
			
			return $breadcrumbs;
		}

		function selection(){

			$data['_SYS_PARAMS'] = $this->_SYS_PARAMS;
			$data['active_controller'] = $this->active_controller;
			$data['breadcrumbs'] = $this->generate_breadcrumbs(array('selection','Hasil Seleksi'));
			$data['submenu'] = 'selection';

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();


			$data2['dt2_opts'] = $dao->execute(0,"SELECT * FROM ref_dt2 WHERE provinsi_id='".$this->_SYS_PARAMS[1]."' and dt2_id!=1900")->result_array();
			$data2['jalur_opts'] = $dao->execute(0,"SELECT * FROM ref_jalur_pendaftaran")->result_array();
			$data2['tipe_sekolah_opts'] = $dao->execute(0,"SELECT * FROM ref_tipe_sekolah")->result_array();
			$data2['form_id'] = 'selection-result-form';	
			$data2['active_controller'] = $this->active_controller;
			$data['subcontent'] = $this->load->view($this->active_controller.'/selection',$data2,true);
			$this->public_template->render($this->active_controller.'/index',$data);

		}

		function statistic(){

			$data['_SYS_PARAMS'] = $this->_SYS_PARAMS;
			$data['active_controller'] = $this->active_controller;
			$data['breadcrumbs'] = $this->generate_breadcrumbs(array('statistic','Statistik'));
			$data['submenu'] = 'statistic';
			$this->public_template->render($this->active_controller.'/index',$data);

		}
		function index(){
			$this->selection();
		}

		function get_schools(){
			$dt2_id = $this->input->post('dt2_id');
			$tipe_sekolah = $this->input->post('tipe_sekolah');

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			$cond = "WHERE a.dt2_id='".$dt2_id."' AND a.tipe_sekolah_id='".$tipe_sekolah."' AND b.tot_kuota>0";

			$sql = "SELECT a.sekolah_id,a.nama_sekolah,a.tipe_sekolah_id FROM sekolah as a 
					LEFT JOIN (SELECT sekolah_id,SUM(grand_kuota) as tot_kuota 
						FROM pengaturan_kuota_".($tipe_sekolah=='1' || $tipe_sekolah=='3'?'sma':'smk')." GROUP BY sekolah_id) as b ON (a.sekolah_id=b.sekolah_id) ".$cond;

			$rows = $dao->execute(0,$sql)->result_array();
			$this->load->view($this->active_controller.'/school_opts.php',array('rows'=>$rows));
		}

		function get_fields(){
			$x = explode('_',$this->input->post('sekolah_id'));
			$sekolah_id = $x[0];
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();
			$cond = "WHERE a.sekolah_id='".$sekolah_id."' AND b.grand_kuota>0";
			$sql = "SELECT a.kompetensi_id,a.nama_kompetensi FROM kompetensi_smk as a 
					LEFT JOIN pengaturan_kuota_smk as b ON (a.kompetensi_id=b.kompetensi_id) ".$cond;			
			$rows = $dao->execute(0,$sql)->result_array();
			$this->load->view($this->active_controller.'/field_opts.php',array('rows'=>$rows));
		}		

		function print_result($school,$path,$school_type,$_field=''){

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$sql = "SELECT * FROM ref_jalur_pendaftaran WHERE ref_jalur_id='".$path."'";
			$jalur_row = $dao->execute(0,$sql)->row_array();
			$sql = "SELECT nama_sekolah FROM sekolah WHERE sekolah_id='".$school."'";
			$sekolah_row = $dao->execute(0,$sql)->row_array();
			$nama_kompetensi = "";

			if($school_type=='2'){

				$x_field = explode('_',$_field);
				$field = $x_field[0];

				$sql = "SELECT a.nama_kompetensi,b.* FROM kompetensi_smk as a LEFT JOIN 
						pengaturan_kuota_smk as b ON (a.kompetensi_id=b.kompetensi_id) 
						WHERE a.kompetensi_id='".$field."'";
				$kuota_rows = $dao->execute(0,$sql)->row_array();
				$nama_kompetensi = $kuota_rows['nama_kompetensi'];
				
			}else{
				$sql = "SELECT * FROM pengaturan_kuota_sma WHERE sekolah_id='".$school."'";
				$kuota_rows = $dao->execute(0,$sql)->row_array();
				
			
			}

			$hasil_seleksi = array();
			$order = "";

			switch($path){
				case '1': $order = 'a.score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '2': $order = 'a.score '.($school_type=='1'?'ASC':'DESC').',b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '3': $order = "a.score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";break;
				case '4': $order = 'a.score DESC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '5': $order = 'b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
			}

			$kuota = $kuota_rows['grand_kuota'];
			$tbl = "pengumuman_seleksi_sulsel3";

			$sql = "SELECT a.id_pendaftaran,
		 			b.nama,
		 			b.sekolah_asal,
		 			a.score,
		 			b.tot_nilai,
		 			b.nil_matematika,
		 			b.nil_bhs_inggris,
		 			b.nil_bhs_indonesia,
		 			b.waktu_pendaftaran 
		 			FROM ".$tbl." as a 
		 			LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
		 			WHERE a.jalur_id='".$path."' 
		 			AND a.tipe_sekolah_id='".$school_type."' 
		 			AND a.".($school_type=='1' || $school_type=='3'?'sekolah_id':'kompetensi_id')."='".($school_type=='1' || $school_type=='3'?$school:$field)."'";

			$sql .= " ORDER BY ".$order." LIMIT 0,".number_format($kuota);
			$rows = $dao->execute(0,$sql)->result_array();

			$data['rows'] = $rows;
			$data['tipe_sekolah'] = $school_type;
			$data['nama_jalur'] = $jalur_row['nama_jalur'];
			$data['nama_sekolah'] = $sekolah_row['nama_sekolah'];
			$data['nama_kompetensi'] = $nama_kompetensi;
			$data['jalur_id'] = $path;
			$data['active_controller'] = $this->active_controller;
			$data['dao'] = $dao;
			$data['sekolah_id'] = $school;
			$data['kuota'] = $kuota;
			$data['_ci'] = $this;
			$this->load->view($this->active_controller.'/print_result',$data);
		}

		function search_result_selection(){

			$x_school = explode('_',$this->security->xss_clean($this->input->post('search_school')));
			

			$school = $x_school[0];
			$school_type = $x_school[1];

			$x_path = explode('_',$this->security->xss_clean($this->input->post('search_path')));
			$path = $x_path[0];
			$path_name = $x_path[1];

			$dt2_id = $this->input->post('search_dt2');

			$field = '';
			$nama_kompetensi = '';

			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			if($school_type=='1' or $school_type=='3'){
				
				$sql = "SELECT * FROM pengaturan_kuota_sma WHERE sekolah_id='".$school."'";

				$kuota_rows = $dao->execute(0,$sql)->row_array();

			}
			else{
				$field = $this->input->post('search_field');
				$sql = "SELECT a.nama_kompetensi,b.* FROM kompetensi_smk as a LEFT JOIN 
						pengaturan_kuota_smk as b ON (a.kompetensi_id=b.kompetensi_id) 
						WHERE a.kompetensi_id='".$field."'";
				$kuota_rows = $dao->execute(0,$sql)->row_array();
				$nama_kompetensi = $kuota_rows['nama_kompetensi'];
			}


			$hasil_seleksi = array();
			$order = "";

			switch($path){
				case '1': $order = 'a.score ASC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '2': $order = 'a.score '.($school_type=='1'?'ASC':'DESC').',b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '3': $order = "a.score DESC,b.nil_matematika DESC,b.nil_bhs_inggris DESC,b.nil_bhs_indonesia DESC,b.waktu_pendaftaran ASC";break;
				case '4': $order = 'a.score DESC,b.tot_nilai DESC,b.waktu_pendaftaran ASC';break;
				case '5': $order = 'a.score DESC,b.waktu_pendaftaran ASC';break;
			}

			
			$kuota = $kuota_rows['grand_kuota'];
			$tbl = "pengumuman_seleksi_sulsel3";

			$sql = "SELECT a.id_pendaftaran,
		 			b.nama,
		 			b.sekolah_asal,
		 			a.score,
		 			b.tot_nilai,
		 			b.nil_matematika,
		 			b.nil_bhs_inggris,
		 			b.nil_bhs_indonesia,
		 			b.waktu_pendaftaran 
		 			FROM ".$tbl." as a 
		 			LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
		 			WHERE a.jalur_id='".$path."' 
		 			AND a.tipe_sekolah_id='".$school_type."' 
		 			AND a.".($school_type=='1' || $school_type=='3'?'sekolah_id':'kompetensi_id')."='".($school_type=='1' || $school_type=='3'?$school:$field)."'";


			$sql .= " ORDER BY ".$order." LIMIT 0,".number_format($kuota);
			$rows = $dao->execute(0,$sql)->result_array();
	
			$data['rows'] = $rows;			
			
			$data['kuota'] = $kuota;
			$data['nama_jalur'] = $path_name;
			$data['jalur_id'] = $path;
			$data['tipe_sekolah'] = $school_type;
			$data['dao'] = $dao;

			$data['sekolah_id'] = $school;
			$data['kompetensi_id'] = $field;
			$data['nama_kompetensi'] = $nama_kompetensi;
			$data['jalur_id'] = $path;
			$data['tipe_sekolah'] = $school_type;
			$data['_ci'] = $this;
			$data['active_controller'] = $this->active_controller;
			$this->load->view($this->active_controller.'/result_list.php',$data);

		}

	}
?>

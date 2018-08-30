<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class database_manipulation extends CI_Controller{

		function __construct(){
			parent::__construct();
		}

		function index(){
			echo "there is no any activity here";
		}


		function pengumuman_17_luwu_utara(){
			$this->load->library('DAO');
			$this->load->model(array('global_model'));

			$dao = $this->global_model->get_dao();

			$sql = "select * from pengumuman_seleksi_sulsel2 where sekolah_id=292";

			$rows = $dao->execute(0,$sql)->result_array();

			foreach($rows as $row1){

				$sql = "select * from daftar_ulang where id_pendaftaran='".$row1['id_pendaftaran']."'";
				$row2 = $dao->execute(0,$sql)->row_array();

				
					

			}

		}

		function pengumuman_18gowa(){

			$this->load->library('DAO');
			$this->load->model(array('global_model'));

			$dao = $this->global_model->get_dao();

			$sql = "SELECT a.*,b.nama,c.nama_jalur FROM daftar_ulang as a 
					LEFT JOIN pendaftaran as b ON (a.id_pendaftaran=b.id_pendaftaran) 
					LEFT JOIN ref_jalur_pendaftaran as c ON (a.jalur_id=c.ref_jalur_id)
					WHERE a.sekolah_id=85";

			$rows = $dao->execute(0,$sql)->result_array();

			// echo "<table border=1>";
			// echo "<tr><td>No.</td><td>Nam</td><td>Jalur Daftar Ulang</td><td>Jalur Pendaftaran</td></tr>";
			
			$no = 0;
			$x = 0;

			foreach($rows as $row1){
				$no++;

				$sql = "SELECT * FROM verifikasi as a WHERE a.id_pendaftaran='".$row1['id_pendaftaran']."' AND sekolah_id='".$row1['sekolah_id']."'";
				
				$row2 = $dao->execute(0,$sql)->row_array();

				echo $row1['id_pendaftaran'].' => '.count($row2).'<br />';

				echo "<pre>";
					print_r($row2);
				echo "</pre>";
				
				$x += count($row2);

				if(count($row2)>0){
					$sqlManipulating = "UPDATE daftar_ulang SET jalur_id='".$row2['jalur_id']."' WHERE id_pendaftaran='".$row1['id_pendaftaran']."' 
										AND sekolah_id='85'";
					$result = $dao->execute(0,$sqlManipulating);
				}

				// echo "<tr>
				// <td>".$no."</td>
				// <td>".$row1['nama']."</td>
				// <td>".$row1['nama_jalur']."</td>
				// <td>".count($row2)."</td>
				// </tr>";
			}
			echo "total : ".$x;

			// echo "</table>";


		}


		function test_show(){
			// $this->load->library('DAO');
			// $this->load->model(array('global_model','pendaftaran_model'));
			// $dao = $this->global_model->get_dao();

			$sql = "SELECT a.id_pendaftaran FROM hasil as a WHERE a.id_sekolah='2'";

			$query = $this->db->query($sql);
			$rows = $query->result_array();

			// $rows = $dao->execute(0,$sql)->result_array();
			echo "<pre>";
				print_r($rows);
		}
		function update_pengaturan_dt2_sekolah(){
			$this->load->library('DAO');
			$this->load->model(array('global_model','pendaftaran_model'));
			$dao = $this->global_model->get_dao();
			$rows = $dao->execute(0,"SELECT * FROM pengaturan_dt2_sekolah")->result_array();

			foreach($rows as $row){
				$sql = "UPDATE pengaturan_dt2_sekolah SET status='".trim($row['status'])."' WHERE dt2_id='".$row['dt2_id']."' AND dt2_sekolah_id='".$row['dt2_sekolah_id']."'";
				$result = $dao->execute(0,$sql);
			}
		}

		function change_status(){
			$this->load->library('DAO');
			$this->load->model(array('global_model','pendaftaran_model'));
			$dao = $this->global_model->get_dao();

			$sql = "SELECT * FROM pendaftaran_sekolah_pilihan WHERE jalur_id='5'";
			$rows = $dao->execute(0,$sql)->result_array();

			foreach($rows as $row){

				$sql = "SELECT count(1) as n FROM ppdb_temp.rekonsilidasi_sekolah_pilihan where sekolah_id='".$row['sekolah_id']."' 
						AND jalur_id='".$row['jalur_id']."' AND id_pendaftaran='".$row['id_pendaftaran']."'";

				$row = $dao->execute(0,$sql)->row_array();

				echo $row['n'].'<br />';
			}
		}

		function change_registrantName(){
			$this->load->library('DAO');
			$this->load->model(array('global_model','pendaftaran_model'));
			$dao = $this->global_model->get_dao();
			$rows = $dao->execute(0,"SELECT * FROM pendaftaran")->result_array();

			$this->db->trans_begin();

			$m = $this->pendaftaran_model;
			$i = date('Y');

			foreach($rows as $row){
				$new_name = 'DEMO PPDB '.$i;
				$m->set_nama($new_name);
				$result = $dao->update($m,array('id_pendaftaran'=>$row['id_pendaftaran']));
				if(!$result){
					$this->db->trans_rollback();
					die('Process terminated');
				}
				$i++;
			}

			$this->db->trans_commit();
		}

		function generate_randPass($unlength,$pslength){
			$this->load->library('DAO');
			$this->load->model(array('global_model','admins_model'));
			$this->load->helper('mix_helper');

			$dao = $this->global_model->get_dao();

			$m = $this->admins_model;

			$rows = $dao->execute(0,"SELECT a.sekolah_id,a.nama_sekolah,b.nama_dt2 FROM sekolah as a LEFT JOIN ref_dt2 as b 
									 ON (a.dt2_id=b.dt2_id)")->result_array();

			$this->db->trans_begin();
			
			$file = fopen(FCPATH.'school_accounts.txt', 'w');

			foreach($rows as $row){

				$username = generatePassword($unlength);
				$password = generatePassword($pslength);
				$admin_id = $this->global_model->get_incrementID('admin_id','admins');

				$m->set_admin_id($admin_id);
				$m->set_username($username);
				$m->set_password(md5($password));
				$m->set_type_fk('3');
				$m->set_fullname($row['nama_sekolah']);
				$m->set_status('1');
				$m->set_sekolah_id($row['sekolah_id']);
				$m->set_created_by('root');
				$m->set_created_time(date('Y-m-d H:i:s'));
				$m->set_modifiable('1');
				$result = $dao->insert($m);
				if(!$result){
					die('there is something wrong that killed the process!');
					$this->db->trans_rollback();
				}

				$text = $admin_id.";".$username.";".$password.";".$row['nama_sekolah'].";".$row['nama_dt2']."\n";
				fwrite($file,$text);

			}

			fclose($file);

			$this->db->trans_commit();

		}

		function fix_status(){
			$this->load->library('DAO');
			$this->load->model(array('global_model','pendaftaran_sekolah_pilihan_model'));

			$dao = $this->global_model->get_dao();
			$sql = "SELECT * FROM pendaftaran_sekolah_pilihan order by sepil_id asc";
			$rows = $dao->execute(0,$sql)->result_array();
			$m = $this->pendaftaran_sekolah_pilihan_model;

			foreach($rows as $row){
				$m->set_status(trim($row['status']));
				$result = $dao->update($m,array('sepil_id'=>$row['sepil_id']));

			}
		}

		function fix_status2(){
			$this->load->library('DAO');
			$this->load->model(array('global_model','pendaftaran_kompetensi_pilihan_model'));

			$dao = $this->global_model->get_dao();
			$sql = "SELECT * FROM pendaftaran_kompetensi_pilihan order by kompil_id asc";
			$rows = $dao->execute(0,$sql)->result_array();
			$m = $this->pendaftaran_kompetensi_pilihan_model;

			foreach($rows as $row){
				$m->set_status(trim($row['status']));
				$result = $dao->update($m,array('kompil_id'=>$row['kompil_id']));

			}
		}

	}
?>
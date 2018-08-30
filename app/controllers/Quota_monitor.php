<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class quota_monitor extends CI_Controller{

		function __construct(){
			parent::__construct();
		}

		function index(){
			$this->load->library('DAO');
			$this->load->model('global_model');
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$sql = "SELECT sekolah_id,nama_sekolah FROM sekolah WHERE tipe_sekolah_id='1'";
			$rows = $dao->execute(0,$sql)->result_array();
			
			echo "<table border=1 width='40%'>
			<tr><th>No.</th><th>ID</th><th>Sekolah</th><th>Daftar Ulang</th></tr>";
			$no = 0;
			
			foreach($rows as $row){
				$sql = "SELECT COUNT(1) as n FROM daftar_ulang1 WHERE sekolah_id='".$row['sekolah_id']."'";
				$row2 = $dao->execute(0,$sql)->row_array();

				$no++;
				echo "<tr><td align='center'>".$no."</td>
				<td>".$row['sekolah_id']."</td><td>".$row['nama_sekolah']."</td><td>".$row2['n']."</td></tr>";
			}
			echo "</table>";

		}

		function sma(){
			$this->index();
		}

		function smk(){
			$this->load->library('DAO');
			$this->load->model('global_model');
			
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			$sql = "SELECT sekolah_id,nama_sekolah FROM sekolah WHERE tipe_sekolah_id='2'";
			$rows = $dao->execute(0,$sql)->result_array();
			
			echo "<table border=1 width='40%'>
			<tr><th>No.</th><th>ID</th><th>Sekolah</th><th>Daftar Ulang</th></tr>";
			$no = 0;
			
			foreach($rows as $row){
				$sql = "SELECT COUNT(1) as n FROM daftar_ulang1 WHERE sekolah_id='".$row['sekolah_id']."'";
				$row2 = $dao->execute(0,$sql)->row_array();

				$no++;
				echo "<tr><td align='center'>".$no."</td>
				<td>".$row['sekolah_id']."</td><td>".$row['nama_sekolah']."</td><td>".$row2['n']."</td></tr>";
			}
			echo "</table>";

		}

	}
?>
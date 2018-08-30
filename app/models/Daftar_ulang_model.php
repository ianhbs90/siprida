<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class daftar_ulang_model extends CI_Model{

		private $hasil_id,$no_verifikasi,$id_pendaftaran,$jalur_id,$sekolah_id,$tipe_sekolah_id,$kompetensi_id,$user,$created_time;

		const pkey = "hasil_id";
		const tbl_name = "daftar_ulang";

		function get_pkey(){
			return self::pkey;
		}		

		function get_tbl_name(){
			return self::tbl_name;
		}

		function __construct(array $init_properties=array()){

			if(count($init_properties)>0){
				foreach($init_properties as $key=>$val){
					$this->$key = $val;
				}
			}
		}

		function get_id_pendaftaran() {
	        return $this->id_pendaftaran;
	    }

	    function get_no_verifikasi() {
	        return $this->no_verifikasi;
	    }

	    function get_jalur_id() {
	        return $this->jalur_id;
	    }

	    function get_sekolah_id() {
	        return $this->sekolah_id;
	    }

	    function get_tipe_sekolah_id() {
	        return $this->tipe_sekolah_id;
	    }

	    function get_kompetensi_id() {
	        return $this->kompetensi_id;
	    }

		function get_user() {
	        return $this->user;
	    }

	    function get_created_time() {
	        return $this->created_time;
	    }

		

		function set_id_pendaftaran($data) {
	        $this->id_pendaftaran=$data;
	    }

	    function set_no_verifikasi($data) {
	        $this->no_verifikasi=$data;
	    }

	    function set_jalur_id($data) {
	        $this->jalur_id=$data;
	    }

	    function set_sekolah_id($data) {
	        $this->sekolah_id=$data;
	    }

	    function set_tipe_sekolah_id($data) {
	        $this->tipe_sekolah_id=$data;
	    }

	    function set_kompetensi_id($data) {
	        $this->kompetensi_id=$data;
	    }

	    function set_user($data) {
	        $this->user=$data;
	    }

	    function set_created_time($data) {
	        $this->created_time=$data;
	    }



	    function get_field_list(){
			return get_object_vars($this);
		}

		function get_property_collection(){
			$field_list = get_object_vars($this);

			$collections = array();
			foreach($field_list as $key=>$val){
				if($val!='')
					$collections[$key]=$val;
			}

			return $collections;
		}

		function get_all_data(){
			$query = $this->db->query("SELECT * FROM ".$this->get_tbl_name()." ORDER BY ".$this->get_pkey()." ASC");
			return $query->result_array();
		}

		function get_by_id($id){
			$query = $this->db->query("SELECT * FROM ".$this->get_tbl_name()." WHERE ".$this->get_pkey()."='".$id."'");
			return $query->row_array();
		}
	}
?>
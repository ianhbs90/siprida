<?php
	
	if($error>0)
	{		
		switch($error){
			case 1:$warning="Data tidak ditemukan";break;
			case 2:$warning="Data sudah terdaftar ulang";break;
			case 3:$warning="Maaf, pendaftar tidak lulus pada sekolah ini!";break;
			case 4:$warning="Maaf, Form Daftar Ulang ditutup sementara!";break;
			default:$warning='kesalahan tidak diketahui';break;
		}
		echo "<br />
		<div class='alert alert-warning'>
			<strong>Perhatian!</strong> ".$warning."
		</div>";
		
		if($error!=2){
			die();
		}
	}

?>
	<hr></hr>

<?php
	if($error==2){
	
		echo "
		<div class='col-md-6 col-md-offset-3'>
			<table class='table table-bordered table-striped'>
			<tbody>
				<tr><td>No. Registrasi</td><td><b>".$pendaftaran_row['no_pendaftaran']."</b></td></tr>
				<tr><td>Nama</td><td>".$pendaftaran_row['nama']."</td></tr>
				<tr><td>Sekolah Asal</td><td>".$pendaftaran_row['sekolah_asal']."</td></tr>
				<tr><td>Alamat</td><td>".$pendaftaran_row['alamat']."</td></tr>
				<tr><td>Jalur Pendaftaran</td><td>".$daftar_ulang_row['nama_jalur']."</td></tr>
				<tr><td>Jenjang Sekolah Tujuan</td><td>".$sekolah_row['nama_tipe_sekolah']."</td></tr>";

				if($daftar_ulang_row['tipe_sekolah_id']=='1'){
					echo "<tr><td>Sekolah Tujuan</td><td>".$sekolah_row['nama_sekolah']."</td></tr>";
				}else{
					echo "
					<tr><td>Sekolah Tujuan</td><td>".$sekolah_row['nama_sekolah']."</td></tr>
					<tr><td>Kompetensi Tujuan</td><td>".$kompetensi_row['nama_kompetensi']."</td></tr>";
				}
				
				echo "<tr><td>Tgl. Pendaftaran Ulang</td><td>".indo_date_format($daftar_ulang_row['tgl_daftar_ulang'],'longDate')."</td></tr>
				<tr><td>Status</td><td><font color='green'><b>Terdaftar Ulang</b></font></td></tr>
				
			</tbody>
			</table>
		</div>";
	}else{
?>
				
	<div class="row" id="verification-container">
		<form id="settlement-form" novalidate="novalidate" method="POST" action="<?=base_url().$active_controller;?>/submit_settlement">
			<input type="hidden" id="img_path" value="<?=$this->config->item('img_path');?>"/>
			<input type="hidden" name="verifikasi_id_pendaftaran" value="<?=$pendaftaran_row['id_pendaftaran'];?>"/>
			<input type="hidden" name="verifikasi_no_pendaftaran" value="<?=$pendaftaran_row['no_pendaftaran'];?>"/>
			<input type="hidden" name="verifikasi_nama" value="<?=$pendaftaran_row['nama'];?>"/>
			<input type="hidden" name="verifikasi_jk" value="<?=$pendaftaran_row['jk'];?>"/>
			<input type="hidden" name="verifikasi_alamat" value="<?=$pendaftaran_row['alamat'];?>"/>
			<input type="hidden" name="verifikasi_nama_kecamatan" value="<?=$pendaftaran_row['nama_kecamatan'];?>"/>
			<input type="hidden" name="verifikasi_nama_dt2" value="<?=$pendaftaran_row['nama_dt2'];?>"/>
			<input type="hidden" name="verifikasi_sekolah_asal" value="<?=$pendaftaran_row['sekolah_asal'];?>"/>
			<input type="hidden" name="verifikasi_sekolah_id" value="<?=$this->session->userdata('sekolah_id');?>"/>
			<input type="hidden" name="verifikasi_nama_sekolah" value="<?=$sekolah_pilihan_row['nama_sekolah'];?>"/>			
			<input type="hidden" name="verifikasi_jalur_id" value="<?=$jalur_row['ref_jalur_id'];?>"/>
			<input type="hidden" name="verifikasi_nama_jalur" value="<?=$jalur_row['nama_jalur'];?>"/>			
			<input type="hidden" name="verifikasi_tipe_sekolah_id" value="<?=$this->session->userdata('tipe_sekolah');?>"/>
			<input type="hidden" name="verifikasi_nama_tipe_sekolah" value="<?=$sekolah_pilihan_row['nama_tipe_sekolah']." (".$sekolah_pilihan_row['akronim'].")";?>"/>
			<input type="hidden" name="verifikasi_kompetensi_id" value="<?=$sekolah_pilihan_row['kompetensi_id'];?>"/>
			<input type="hidden" name="verifikasi_nama_kompetensi" value="<?=$sekolah_pilihan_row['nama_kompetensi'];?>"/>
			<input type="hidden" name="verifikasi_settlement_time_input" value="<?=$settlement_time_input;?>"/>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<?php
						echo "
						<table class='table table-bordered table-striped'>
							<tbody>
								<tr><td>ID Peserta</td><td><b>".$pendaftaran_row['id_pendaftaran']."</b></td></tr>
								<tr><td>No. Registrasi</td><td><b>".$pendaftaran_row['no_pendaftaran']."</b></td></tr>
								<tr><td>Nama</td><td>".$pendaftaran_row['nama']."</td></tr>
								<tr><td>J. Kelamin</td><td>".($pendaftaran_row['jk']=='L'?'Laki-laki':'Perempuan')."</td></tr>
								<tr><td>Sekolah Asal</td><td>".$pendaftaran_row['sekolah_asal']."</td></tr>
								<tr><td>Alamat</td><td>".$pendaftaran_row['alamat']."</td></tr>
								<tr><td>Kecamatan</td><td>".$pendaftaran_row['nama_kecamatan']."</td></tr>
								<tr><td>Kab./Kota</td><td>".$pendaftaran_row['nama_dt2']."</td></tr>
								<tr><td>Jalur Pendaftaran</td><td>".$jalur_row['nama_jalur']."</td></tr>
								<tr><td>Jenjang Skolah Tujuan</td><td>".$sekolah_pilihan_row['nama_tipe_sekolah']." (".$sekolah_pilihan_row['akronim'].")</td></tr>";

								if($sekolah_pilihan_row['tipe_sekolah_id']=='1'){
									echo "<tr><td>Sekolah Tujuan (pilihan ke)</td><td>".$sekolah_pilihan_row['nama_sekolah']."</td></tr>";
								}else{
									echo "
									<tr><td>Sekolah Tujuan</td><td>".$sekolah_pilihan_row['nama_sekolah']."</td></tr>									
									<tr><td>Kompetensi Tujuan (pilihan ke)</td><td>".$sekolah_pilihan_row['nama_kompetensi']."</td></tr>";
								}

								if($settlement_time_input=='1'){
									echo "
									<tr><td>Tgl. Daftar Ulang</td><td><input type='text' class='form-control datepicker' name='verifikasi_tgl_daftar_ulang' id='verifikasi_tgl_daftar_ulang' required/></td></tr>";
								}
								
								echo "<tr><td></td><td><button type='submit' class='btn btn-primary'>Daftar Ulang</button></td></tr>
							</tbody>
							</table>";
					?>
				</div>
			</div>
		</form>
	</div>

<script src="<?=$this->config->item('js_path');?>plugins/masked-input/jquery.maskedinput.min.js"></script>

<script type="text/javascript">
	
	$(document).ready(function(){

		$("#verifikasi_tgl_daftar_ulang").mask('99-99-9999');		

		// START AND FINISH DATE
		$('#verifikasi_tgl_daftar_ulang').datepicker({
			dateFormat : 'dd-mm-yy',
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',			
		});		

	});

    var $settlement_form=$('#settlement-form');
    var settlement_stat=$settlement_form.validate();

    $settlement_form.submit(function(){
    	
        if(settlement_stat.checkForm())
        {

        	ajax_object.reset_object();
            ajax_object.set_content('#verification-container')
                           .set_loading('#preloadAnimation')
                           .enable_pnotify()
                           .set_form($settlement_form)
                           .submit_ajax('menyimpan data');
            return false;
        }

    });    
	
</script>

<?php } ?>
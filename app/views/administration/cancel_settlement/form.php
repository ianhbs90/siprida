<?php

	if($error>0)
	{
		switch($error){
			case 1:$warning="Data tidak ditemukan";break;
			default:$warning='kesalahan tidak diketahui';break;
		}		
			

		echo "<br />
		<div class='alert alert-warning'>
			<strong>Perhatian!</strong> ".$warning."
		</div>";

		die();
	}

?>

	<hr></hr>

	<div class="row" id="verification-container">
		<form id="<?=$form_id;?>" novalidate="novalidate" method="POST" action="<?=base_url();?>administration/submit_cancel_settlement">
			<input type="hidden" name="cancel_id_pendaftaran" value="<?=$daftar_ulang_row['id_pendaftaran'];?>"/>
			<input type="hidden" name="cancel_nama" value="<?=$daftar_ulang_row['nama'];?>"/>
			<input type="hidden" name="cancel_sekolah_asal" value="<?=$daftar_ulang_row['sekolah_asal'];?>"/>
			<input type="hidden" name="cancel_alamat" value="<?=$daftar_ulang_row['alamat'];?>"/>
			<input type="hidden" name="cancel_tipe_sekolah_id" value="<?=$daftar_ulang_row['tipe_sekolah_id'];?>"/>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<?php	

						echo "
						<table class='table table-bordered table-striped'>
							<tbody>
								<tr><td>No. Peserta</td><td>".$daftar_ulang_row['id_pendaftaran']."</td></tr>
								<tr><td>Nama</td><td>".$daftar_ulang_row['nama']."</td></tr>
								<tr><td>J. Kelamin</td><td>".($daftar_ulang_row['jk']=='L'?'Laki-laki':'Perempuan')."</td></tr>
								<tr><td>Sekolah Asal</td><td>".$daftar_ulang_row['sekolah_asal']."</td></tr>
								<tr><td>Alamat</td><td>".$daftar_ulang_row['alamat']."</td></tr>";
								if($daftar_ulang_row['tipe_sekolah_id']=='1'){
									echo "<tr><td>Sekolah Tujuan</td><td>".$pilihan_row['nama_sekolah']."</td></tr>";
								}
								else{
									echo "<tr><td>Sekolah Tujuan</td><td>".$pilihan_row['nama_sekolah']."</td></tr>";
									echo "<tr><td>Kompetensi Tujuan</td><td>".$pilihan_row['nama_kompetensi']."</td></tr>";
								}
							echo "
								<tr>
								<td></td><td><input type='checkbox' name='cancel_hapus_pendaftaran' id='cancel_hapus_pendaftaran' value='1'/> Hapus Data Pendaftaran
								</tr>								
								<tr><td></td>
								<td><button type='submit' class='btn btn-primary'>Submit</button>
								</tr>
							</tbody>
							</table>";
					?>
				</div>
			</div>
		</form>
	</div>

<script type="text/javascript">	

	var cancel_form_id = '<?=$form_id;?>';
    var $cancel_form=$('#'+cancel_form_id);
    var search_stat=$cancel_form.validate({
        // Do not change code below
        errorPlacement : function(error, element) {
            error.addClass('error');
            error.insertAfter(element.parent());
        }
    });

    $cancel_form.submit(function(){
        if(search_stat.checkForm())
        {        	
        	ajax_object.reset_object();
            ajax_object.set_content('#data-view')                           
                           .set_loading('#preloadAnimation')
                           .disable_pnotify()
                           .set_form($cancel_form)
                           .submit_ajax('');
            return false;
        }

    });	    

</script>

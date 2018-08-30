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
		<form id="<?=$form_id;?>" novalidate="novalidate" method="POST" action="<?=base_url();?>administration/submit_cancel_verification">
			<input type="hidden" name="cancel_id_pendaftaran" value="<?=$pilihan_row['id_pendaftaran'];?>"/>
			<input type="hidden" name="cancel_no_registrasi" value="<?=$pilihan_row['no_pendaftaran'];?>"/>
			<input type="hidden" name="cancel_no_verifikasi" value="<?=$pilihan_row['no_verifikasi'];?>"/>
			<input type="hidden" name="cancel_tipe_sekolah_id" value="<?=$pilihan_row['tipe_sekolah_id'];?>"/>
			<input type="hidden" name="cancel_sekolah_id" value="<?=$pilihan_row['sekolah_id'];?>"/>
			<input type="hidden" name="cancel_jalur_id" value="<?=$pilihan_row['jalur_id'];?>"/>
			<input type="hidden" name="cancel_nama" value="<?=$pilihan_row['nama'];?>"/>
			<input type="hidden" name="cancel_nama_sekolah" value="<?=$pilihan_row['nama_sekolah'];?>"/>
			<input type="hidden" name="cancel_alamat" value="<?=$pilihan_row['alamat'];?>"/>
			<?php
				if($pilihan_row['tipe_sekolah_id']=='2'){
					echo "<input type='hidden' name='cancel_kompetensi_id' value='".$pilihan_row['kompetensi_id']."'/>";
					echo "<input type='hidden' name='cancel_nama_kompetensi' value='".$pilihan_row['nama_kompetensi']."'/>";
				}else{
					echo "<input type='hidden' name='cancel_kompetensi_id' value='0'/>";
					echo "<input type='hidden' name='cancel_nama_kompetensi' value='0'/>";
				}
			?>
			<input type="hidden" name="cancel_pilihan_ke" value="<?=$pilihan_row['pilihan_ke'];?>"/>
			<input type="hidden" name="cancel_score" value="<?=$pilihan_row['score'];?>"/>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<?php	

						echo "
						<table class='table table-bordered table-striped'>
							<tbody>
								<tr><td>No. Peserta</td><td>".$pilihan_row['id_pendaftaran']."</td></tr>
								<tr><td>No. Registrasi</td><td>".$pilihan_row['no_pendaftaran']."</td></tr>
								<tr><td>No. Verifikasi</td><td>".$pilihan_row['no_verifikasi']."</td></tr>
								<tr><td>Nama</td><td>".$pilihan_row['nama']."</td></tr>
								<tr><td>J. Kelamin</td><td>".($pilihan_row['jk']=='L'?'Laki-laki':'Perempuan')."</td></tr>
								<tr><td>Sekolah Asal</td><td>".$pilihan_row['sekolah_asal']."</td></tr>
								<tr><td>Alamat</td><td>".$pilihan_row['alamat']."</td></tr>";
								if($pilihan_row['tipe_sekolah_id']=='1'){
									echo "<tr><td>Sekolah Tujuan (pilihan ke)</td><td>".$pilihan_row['nama_sekolah']." (".$pilihan_row['pilihan_ke'].")</td></tr>";
								}
								else{
									echo "<tr><td>Sekolah Tujuan</td><td>".$pilihan_row['nama_sekolah']."</td></tr>";
									echo "<tr><td>Kompetensi Tujuan (pilihan ke)</td><td>".$pilihan_row['nama_kompetensi']." (".$pilihan_row['pilihan_ke'].")</td></tr>";
								}
							echo "
								<tr><td>".($pilihan_row['tipe_sekolah_id']=='1'?'Jarak':'Skor')."</td>
								<td>".number_format($pilihan_row['score'])." ".($pilihan_row['tipe_sekolah_id']=='1'?'m':'')."</td></tr>
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

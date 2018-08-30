<?php

	if($error>0)
	{

		if($error!=1 and $error!=4)
			$prev = $sekolah_pilihan_row['pilihan_ke']-1;

		switch($error){
			case 1:$warning="Data tidak ditemukan";break;
			case 2:$warning="Data sudah diverifikasi";break;
			// case 3:$warning="Proses belum bisa dilanjutkan. Silahkan melakukan verifikasi berkas pada 
			// 			    ".($this->session->userdata('tipe_sekolah')=='1'?'Sekolah':'Kompetensi')." pilihan ke - ".$prev." terlebih dahulu!";break;
			case 3:$warning="Peserta telah terdaftar ulang pada sesi pendaftaran pertama";break;
			case 4:$warning="Maaf, Form Verifikasi ditutup sementara!";break;
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

		<form id="wizard-1" novalidate="novalidate" method="POST" action="<?=base_url().$active_controller;?>/submit_verification">
			<input type="hidden" id="img_path" value="<?=$this->config->item('img_path');?>"/>
			<input type="hidden" name="verifikasi_id_pendaftaran" value="<?=$pendaftaran_row['id_pendaftaran'];?>"/>
			<input type="hidden" name="verifikasi_no_pendaftaran" value="<?=$pendaftaran_row['no_pendaftaran'];?>"/>
			<input type="hidden" name="verifikasi_nama" value="<?=$pendaftaran_row['nama'];?>"/>
			<input type="hidden" name="verifikasi_jk" value="<?=$pendaftaran_row['jk'];?>"/>
			<input type="hidden" name="verifikasi_alamat" value="<?=$pendaftaran_row['alamat'];?>"/>
			
			<input type="hidden" name="verifikasi_nama_kecamatan" value="<?=$pendaftaran_row['nama_kecamatan'];?>"/>
			<input type="hidden" name="verifikasi_nama_dt2" value="<?=$pendaftaran_row['nama_dt2'];?>"/>
			<input type="hidden" name="verifikasi_tipe_sekolah_id" value="<?=$sekolah_pilihan_row['tipe_sekolah_id'];?>"/>
			<input type="hidden" name="verifikasi_nama_tipe_sekolah" value="<?=$sekolah_pilihan_row['nama_tipe_sekolah']." (".$sekolah_pilihan_row['akronim'].")";?>"/>

			<input type="hidden" name="verifikasi_sekolah_asal" value="<?=$pendaftaran_row['sekolah_asal'];?>"/>
			<input type="hidden" name="verifikasi_sekolah_id" value="<?=$sekolah_pilihan_row['sekolah_id'];?>"/>
			<input type="hidden" name="verifikasi_nama_sekolah" value="<?=$sekolah_pilihan_row['nama_sekolah'];?>"/>
			<input type="hidden" name="verifikasi_sekolah_pilihan_ke" value="<?=$sekolah_pilihan_row['pilihan_ke'];?>"/>

			<input type="hidden" name="verifikasi_jalur_id" id="verifikasi_jalur_id" value="<?=$pendaftaran_row['jalur_id'];?>"/>
			<input type="hidden" name="verifikasi_nama_jalur" value="<?=$pendaftaran_row['nama_jalur'];?>"/>
			<input type="hidden" name="verifikasi_tipe_jalur" value="<?=$pendaftaran_row['tipe_jalur'];?>"/>
			<input type="hidden" name="verifikasi_kompetensi_id" value="<?=$sekolah_pilihan_row['kompetensi_id'];?>"/>

			<input type="hidden" name="verification_error" value="<?=$error;?>"/>
			<input type="hidden" name="verifikasi_nama_kompetensi" value="<?=$sekolah_pilihan_row['nama_kompetensi'];?>"/>

			
			<input type="hidden" name="verifikasi_nilai_total" value="<?=$pendaftaran_row['tot_nilai'];?>"/>

			<?php 
				$locked = ($this->session->userdata('sekolah_id')=='18'?'1':'0');
			?>
			<input type="hidden" id="disable_marker1Locked" value="<?=$locked;?>"/>

			<div id="bootstrap-wizard-1" class="col-sm-12">
				<div class="form-bootstrapWizard">
					<ul class="bootstrapWizard form-wizard">
						<li class="active" data-target="#step1">
							<a href="#tab1" data-toggle="tab"> <span class="step">1</span> <span class="title">Data Diri & Nilai</span> </a>
						</li>
						
						<?php
						$nextStep = 1;
						if($sekolah_pilihan_row['tipe_sekolah_id']!='2' and $pendaftaran_row['tipe_jalur']=='1')
						{
							$nextStep++;
							echo "
							<li data-target='#step".$nextStep."'>
								<a href='#tab".$nextStep."' data-toggle='tab'> <span class='step'>".$nextStep."</span> <span class='title'>Zonasi</span> </a>
							</li>";
						}
						$nextStep++;
						?>
						<li data-target="#step<?=$nextStep;?>">
							<a href="#tab<?=$nextStep;?>" data-toggle="tab"> <span class="step"><?=$nextStep;?></span> <span class="title">Berkas</span> </a>
						</li>
						
						<?php
						if($pendaftaran_row['jalur_id']=='4')
						{
							$nextStep++;
							echo "
							<li data-target='#step".$nextStep."'>
								<a href='#tab".$nextStep."' data-toggle='tab'> <span class='step'>".$nextStep."</span> <span class='title'>Prestasi</span> </a>
							</li>";	
						}
						$nextStep++;
						?>

						<li data-target="#step<?=$nextStep;?>">
							<a href="#tab<?=$nextStep;?>" data-toggle="tab"> <span class="step"><?=$nextStep;?></span> <span class="title">Simpan</span> </a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<br>
						<h3><strong>Step 1 </strong> - Data Diri & Nilai</h3>
						<?php
						echo "
						<div class='row'>
							<div class='col-md-6'>
								<table class='table table-bordered'>
								<tbody>
									<tr>
										<td>Nama</td>
										<td>".$pendaftaran_row['nama']."</td>
									</tr>
									<tr>
										<td>J. Kelamin</td>
										<td>".($pendaftaran_row['jk']=='L'?'Laki-laki':'Perempuan')."</td>
									</tr>
									<tr>
										<td>Tempat/Tgl. Lahir</td>
										<td>".$pendaftaran_row['tpt_lahir'].", ".indo_date_format($pendaftaran_row['tgl_lahir'],'longDate')."</td>
									</tr>
									<tr>
										<td>Sekolah Asal</td>
										<td>".$pendaftaran_row['sekolah_asal']."</td>
									</tr>";
									if($sekolah_pilihan_row['tipe_sekolah_id']=='1' or $sekolah_pilihan_row['tipe_sekolah_id']=='3')
									{
										echo "
										<tr>
											<td>Sekolah Tujuan (pilihan ke)</td>
											<td>".$sekolah_pilihan_row['nama_sekolah']." (".$sekolah_pilihan_row['pilihan_ke'].")</td>
										</tr>";
									}else{
										echo "
										<tr><td>Sekolah Tujuan</td><td>".$sekolah_pilihan_row['nama_sekolah']."</td></tr>
										<tr><td>Kompetensi Tujuan (pilihan ke)</td>
										<td>".$sekolah_pilihan_row['nama_kompetensi']." (".$sekolah_pilihan_row['pilihan_ke'].")</td>
										</tr>";
									}
								echo "</tbody>
								</table>
							</div>

							<div class='col-md-6'>
								<table class='table table-bordered'>
								<tbody>
									<tr>
										<td>Nama Orang Tua</td>
										<td>".$pendaftaran_row['nm_orang_tua']."</td>
									</tr>
									<tr>
										<td>Alamat</td>
										<td>".$pendaftaran_row['alamat']."</td>
									</tr>
									<tr>
										<td>Kecamatan</td>
										<td>".$pendaftaran_row['nama_kecamatan']."</td>
									</tr>
									<tr>
										<td>Kab./Kota</td>
										<td>".$pendaftaran_row['nama_dt2']."</td>
									</tr>
									<tr>
										<td style='background:#adadad;color:white'><b>Jalur Pilihan</b></td>
										<td>".$pendaftaran_row['nama_jalur']."</td>
									</tr>
									<tr>
										<td style='background:#adadad;color:white'><b>Mode UN</b></td>
										<td>
											<select name='verifikasi_mode_un' id='verifikasi_mode_un' class='form-control'>";
												$opts = array('UNBK','UNKP');
												foreach($opts as $opt){
													$selected = (strtolower($opt)==strtolower($pendaftaran_row['mode_un'])?'selected':'');
													echo "<option value='".$opt."' ".$selected.">".$opt."</option>";
												}
											echo "</select>
										</td>
									</tr>";

									
								echo "</tbody>
								</table>
							</div>
						</div>
						
						<div class='row'>
							<div class='col-md-12'>
								<table class='table table-border table-hover'>
									<thead>
										<tr><th>Mata Pelajaran</th><th>Nilai</th><th></th></tr>
									</thead>
									<tbody>
										<tr><td>Bahasa Indonesia</td><td>".$pendaftaran_row['nil_bhs_indonesia']."</td>
										<td><input type='checkbox' name='verifikasi_nil_bhs_indonesia' value='1'/>&nbsp;Valid</td></tr>
										<tr><td>Bahasa Inggris</td><td>".$pendaftaran_row['nil_bhs_inggris']."</td>
										<td><input type='checkbox' name='verifikasi_nil_bhs_inggris' value='1'/>&nbsp;Valid</td></tr>
										<tr><td>Matematika</td><td>".$pendaftaran_row['nil_matematika']."</td>
										<td><input type='checkbox' name='verifikasi_nil_matematika' value='1'/>&nbsp;Valid</td></tr>
										<tr><td>IPA</td><td>".$pendaftaran_row['nil_ipa']."</td>
										<td><input type='checkbox' name='verifikasi_nil_ipa' value='1'/>&nbsp;Valid</td></tr>
										<tr><td>Total Nilai</td><td>".$pendaftaran_row['tot_nilai']."</td>
										<td><input type='checkbox' name='verifikasi_tot_nilai' value='1'/>&nbsp;Valid</td></tr>
									</tbody>
								</table>
							</div>
						</div>";
						?>

					</div>

					<?php
					$nextStep = 1;
					if($sekolah_pilihan_row['tipe_sekolah_id']!='2' and $pendaftaran_row['tipe_jalur']=='1')
					{
						$nextStep++;
						echo "
						<div class='tab-pane' id='tab".$nextStep."'>
							<input type='hidden' id='school_latitude' value='".$sekolah_pilihan_row['latitude']."'/>
							<input type='hidden' id='school_longitude' value='".$sekolah_pilihan_row['longitude']."'/>
							<input type='hidden' id='school_name' value='".$sekolah_pilihan_row['nama_sekolah']."'/>
							<input type='hidden' id='tipe_sekolah_id' name='tipe_sekolah_id' value='".$sekolah_pilihan_row['tipe_sekolah_id']."'/>
							<input type='hidden' id='tipe_jalur' name='tipe_jalur' value='".$pendaftaran_row['tipe_jalur']."'/>
							<input type='hidden' id='reg_address' value='".$pendaftaran_row['alamat'].", ".$pendaftaran_row['nama_kecamatan'].", ".$pendaftaran_row['nama_dt2']."' readonly/>
							<input type='hiddens' id='reg_latLng' name='reg_latLng'/>
							<br>
							<h3><strong>Step 2</strong> - Zonasi</h3>
							<div class='row'>
								<div class='col-md-12'>
									<div id='map' style='height:500px!important;width:100%;border;1px solid #cccccc;'></div><br />
									<div class='alert alert-warning'>
										<strong>Perhatian!</strong> Klik tombol '<b><font color='red'>Mulai Menghitung Jarak</font></b>' kemudian <b><font color='red'>tarik Marker (Penunjuk Lokasi)</font></b> ke titik domisili Peserta
									</div>
									<table class='table table-bordered'>
										<tbody>
											<tr><td colspan='2' align='center'>
												<button class='btn btn-default' id='startDistanceCalculation'><i class='fa fa-road'></i> Mulai Menghitung Jarak</button></td></tr>
											<tr><td width='30%' align='right'>Alamat Sekolah</td>
												<td><input type='text' class='form-control' value='".$sekolah_pilihan_row['alamat_sekolah']."' id='school_LatLang' readonly/></td></tr>
											<tr>
												<td align='right'>Alamat Peserta</td>
												<td>
													<input type='text' class='form-control' id='reg_address' value='".$pendaftaran_row['alamat']."' readonly/>
												</td>
											</tr>
											<tr>
												<td align='right'>Jarak</td>
												<td><input type='text' class='form-control' id='distance' name='verifikasi_jarak' readonly required/></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>";
					}
					$nextStep++;
					?>

					<div class="tab-pane" id="tab<?=$nextStep;?>">
						<br>
						<h3><strong>Step <?=$nextStep;?></strong> - Verifikasi Berkas
							
						</h3>
						<div class="row">
							<div class="col-md-12">
								<table class="table table-border table-hover">
									<thead>
										<tr>
											<th colspan="2">Nama Berkas</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$i = 0;
											foreach($dokumen_rows as $row){
												$i++;
												echo "<tr>
												<td>".$row['nama_dokumen']."</td>
												<td><input type='checkbox' name='verifikasi_berkas".$i."' value='1'/>
												<input type='hidden' name='verifikasi_berkas_id".$i."' value='".$row['dokel_id']."'/>
												&nbsp;Valid</td>
												</tr>";
											}
										?>

										<?php
										if($sekolah_pilihan_row['tipe_sekolah_id']=='1'){
										?>
										<tr>
											<td colspan="2" align="center" style="background:#ffe20a">
												<i class="fa fa-pencil"></i> <b>Silahkan minta Calon Peserta Didik menandatangani Surat Pernyataan Kesesuaian Jarak !!</b.
											</td>
										</tr>
										<tr>
											<td>Apakah Calon Peserta Didik menyetujui dan menandatangani <b>Surat Pernyataan Kesesuaian Jarak ?</b></td>
											<td>
												<select name="verifikasi_surat_pernyataan" id="verifikasi_surat_pernyataan" class="form-control">
													<option value="0">Tidak</option>
													<option value="1">Ya</option>
												</select>
											</td>
										</tr>
										<?php }?>
									</tbody>
								</table>
								<input type="hidden" name="verifikasi_n_berkas" value="<?=$i;?>"/>
							</div>										
						</div>									
					</div>

					<?php
					if($pendaftaran_row['jalur_id']=='4')
					{
						$nextStep++;
						echo "
						<div class='tab-pane' id='tab".$nextStep."'>										
							<br>
							<h3><strong>Step ".$nextStep."</strong> - Verifikasi Prestasi</h3>
							<div class='row'>
								<div class='col-md-12'>
									<table class='table table-border table-hover'>
										<thead>
											<tr>
												<th>Tingkat</th><th>Bidang</th><th>No. Sertifikat</th><th>Nama Kejuaraan</th><th>Tahun</th><th>Peringkat</th><th></th>
											</tr>
										</thead>
										<tbody>";
											
											$i = 0;
											foreach($prestasi_rows as $row){
												$i++;
												echo "<tr>
												<td>".$row['tingkat_kejuaraan']."</td>
												<td>".$row['bidang_kejuaraan']."</td>
												<td>".$row['no_sertifikat']."</td>
												<td>".$row['nm_kejuaraan']."</td>
												<td align='center'>".$row['thn_kejuaraan']."</td>
												<td align='center'>".$row['peringkat']."</td>
												<td>
												<input type='checkbox' name='verifikasi_prestasi".$i."' value='1'/>
												<input type='hidden' name='verifikasi_prestasi_id".$i."' value='".$row['prestasi_id']."'/>
												&nbsp;Valid</td>
												</tr>";
											}
											
										echo "</tbody>
									</table>
									<input type='hidden' name='verifikasi_n_prestasi' value='".$i."'/>
								</div>
							</div>
						</div>";
					}
					$nextStep++;
					?>

					<div class="tab-pane" id="tab<?=$nextStep;?>" align="center">
						<br /><br />

						<button type="submit" class="btn btn-primary">Submit Verifikasi</button>
					</div>

					<div class="form-actions">
						<div class="row">
							<div class="col-sm-12">
								<ul class="pager wizard no-margin">
									
									<!--<li class="previous first disabled">
									<a href="javascript:void(0);" class="btn btn-lg btn-default"> First </a>
									</li>-->
									
									<li class="previous disabled">
										<a href="javascript:void(0);" class="btn btn-lg btn-default"> Previous </a>
									</li>

									<!--<li class="next last">
									<a href="javascript:void(0);" class="btn btn-lg btn-primary"> Last </a>
									</li>-->

									<li class="next">
										<a href="javascript:void(0);" class="btn btn-lg txt-color-darken"> Next </a>
									</li>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>
		</form>
	</div>		

<?php 
if($sekolah_pilihan_row['tipe_sekolah_id']!='2' and $pendaftaran_row['tipe_jalur']=='1')
{
?>
	<script type="text/javascript" src="<?=$this->config->item('js_path');?>my_scripts/distance_measurement.js"></script>
<?php
}
?>

<script type="text/javascript">
	//Bootstrap Wizard Validations
	$(document).ready(function(){


		$('input').iCheck({
		    checkboxClass: 'icheckbox_square-blue',
		    radioClass: 'iradio_square-blue',
		    increaseArea: '20%' // optional
		  });



	  var $validator = $("#wizard-1").validate({	    
	    
	    highlight: function (element) {
	      $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	    },
	    unhighlight: function (element) {
	      $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
	    },
	    errorElement: 'span',
	    errorClass: 'help-block',
	    errorPlacement: function (error, element) {
	      if (element.parent('.input-group').length) {
	        error.insertAfter(element.parent());
	      } else {
	        error.insertAfter(element);
	      }
	    }
	  });
	  

	  $('#bootstrap-wizard-1').bootstrapWizard({
	    'tabClass': 'form-wizard',
	    'onNext': function (tab, navigation, index) {

	      var $valid = $("#wizard-1").valid();
	      
	      if(index==2){

	    		if($('#verifikasi_jalur_id').val()=='3'){
	    			if($('#distance').val()=='' || ($('#distance').val()=='0 m'))
	    			{
	    				alert('Silahkan hitung jarak domisili peserta!!');
	    				$valid = false;		
	    			}
	    		}
	    	}

	    	if(index==3){
	    	
	    		if($('#verifikasi_surat_pernyataan').val()=='0'){
	    			alert('Calon Peserta Didik wajib menyetujui Surat Pernyataan Kesesuaian Jarak');
	    			$valid = false;
	    		}
	    	}

	      if (!$valid) {
	        $validator.focusInvalid();
	        return false;
	      } else {
	        $('#bootstrap-wizard-1').find('.form-wizard').children('li').eq(index - 1).addClass(
	          'complete');
	        $('#bootstrap-wizard-1').find('.form-wizard').children('li').eq(index - 1).find('.step')
	        .html('<i class="fa fa-check"></i>');
	      }
	    }
	  });
	});

		
    var $verify_form=$('#wizard-1');
    var verify_stat=$verify_form.validate();

    $verify_form.submit(function(){
    	
        if(verify_stat.checkForm())
        {

        	ajax_object.reset_object();
            ajax_object.set_content('#verification-container')                           
                           .set_loading('#preloadAnimation')
                           .enable_pnotify()
                           .set_form($verify_form)
                           .submit_ajax('menyimpan verifikasi');
            return false;
        }

    });    
	
</script>

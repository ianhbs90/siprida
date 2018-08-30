<?php

	if($availability=='pre' or $availability=='finish'){

		$msg = "";
		if($availability=='pre'){
			$msg = "Jalur Pendaftaran belum terbuka! <br />
					Jadwal Pendaftaran Jalur ".$nama_jalur." terbuka pada tanggal ".mix_2Date($kuota_jalur_row['tgl_buka'],$kuota_jalur_row['tgl_tutup']);
		}else{
			$msg = "Pendaftaran Jalur ".$nama_jalur." sudah tertutup!";
		}

		
		// $msg = "Pendaftaran untuk Jalur Afirmasi, Akademik, Prestasi, & Khusus pada SMA & SMK sudah tertutup";
		echo "<div class='alert alert-warning'><strong>Perhatian !</strong> ".$msg."</div>";
		die();
	}

	$login = !is_null($this->session->userdata('nopes'));
?>

<div class="container-fluid box">
	<fieldset>
		<div class="alert alert-danger">
		  <strong>Perhatian!</strong> Lengkapi formulir di bawah ini dengan data yang benar. <br />
		  Perhatikan kembali data yang telah diisi sebelum menekan tombol Submit karena penginputan data hanya sekali saja!
		</div>

		<form action="<?=base_url();?>reg/submit_reg" id="reg-form" method="POST" class="form-horizontal">
			<input type="hidden" id="input_tipe_sekolah" name="input_tipe_sekolah" value="<?=$stage;?>"/>
			<input type="hidden" id="input_jalur_pendaftaran" name="input_jalur_pendaftaran" value="<?=$path;?>"/>
			<input type="hidden" id="input_tipe_ujian_smp" name="input_tipe_ujian_smp" value="<?=$peserta_row['tipe_ujian_smp'];?>"/>
			<input type="hidden" id="input_jml_sekolah" name="input_jml_sekolah" value="<?=$kuota_jalur_row['jml_sekolah'];?>"/>
			<input type="hidden" id="input_dt2_id" name="input_dt2_id" value="<?=$peserta_row['dt2_id'];?>"/>			
			<input type="hidden" id="base_url" value="<?=base_url();?>"/>
			<input type="hidden" id="stage" value="<?=$stage;?>"/>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label col-md-4" for="input_no_peserta">No. Peserta <?=($stage!='3'?($input_rule=='required'?"<font color='red'>*</font>":""):"<font color='red'>*</font>");?></label>
						<div class="col-md-8">
							<div class="input">
								<?php
									$onblur = ($stage=='3'?"check_regid($(this).val())":"");
								?>
								<input class="form-control" id="input_no_peserta" type="text" tabindex="0" name="input_no_peserta" value="<?=$input_arr['nopes'];?>" onblur="<?=$onblur;?>" <?=($stage!='3'?$input_rule:"required"); ?>/>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="input_nama">Nama <?=($input_rule=='required'?"<font color='red'>*</font>":"");?></label>
						<div class="col-md-8">
							<div class="input">
								<input class="form-control" id="input_nama" type="text" name="input_nama" tabindex="1" value="<?=$input_arr['nama'];?>" <?=$input_rule;?>/>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="input_jk">J. Kelamin <?=($input_rule=='required'?"<font color='red'>*</font>":"");?></label>
						<div class="col-md-8">
							<div class="input">
								<?php
									if(($path!='5' and $stage!='3') or ($stage=='3' and !is_null($this->session->userdata('nopes')))){
										echo "<input class='form-control' id='input_jk' type='text' tabindex='2' name='input_jk' value='".$input_arr['jk']."' readonly/>";
									}else{
										echo "
										<select name='input_jk' id='input_jk' class='form-control' tabindex='2' ".($stage=='2'?'required':'disabled').">
										<option value=''></option>";
										foreach(array('L'=>'Laki-laki','P'=>'Perempuan') as $key=>$val){
											echo "<option value='".$key."'>".$val."</option>";
										}
										echo "</select>";
									}
								?>
								
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="input_sekolah_asal">Sekolah Asal <?=($input_rule=='required'?"<font color='red'>*</font>":"");?></label>
						<div class="col-md-8">
							<div class="input">
								<input class="form-control" id="input_sekolah_asal" type="text" tabindex="3" name="input_sekolah_asal" value="<?=$input_arr['sklh_asal'];?>" <?=$input_rule;?>/>

							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4">Tpt, Tgl. Lahir <?=($input_rule=='required'?"<font color='red'>*</font>":"");?></label>
						<div class="col-md-4">
							<div class="input">
								<input class="form-control" id="input_tpt_lahir" name="input_tpt_lahir" tabindex="4" type="text" value="<?=$input_arr['tpt_lahir'];?>" <?=$input_rule;?>/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="input">
								<input class="form-control datepicker" id="input_tgl_lahir" type="text" tabindex="5" name="input_tgl_lahir" value="<?=$input_arr['tgl_lahir'];?>" <?=$input_rule;?>/>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label col-md-4" for="input_alamat">Alamat <font color='red'>*</font></label>
						<div class="col-md-8">
							<div class="input">
								<input class="form-control" id="input_alamat" type="text" name="input_alamat" tabindex="6" value="<?=$input_arr['alamat'];?>" required/>
							</div>
						</div>
					</div>	
					<div class="form-group">
						<label class="control-label col-md-4" for="input_dt2">Kab./Kota <font color='red'>*</font></label>
						<div class="col-md-8">
							<div class="input">
								<?php

									echo "<select name='input_dt2' id='input_dt2' tabindex='7' onchange=\"get_data_linked_toRegency($(this).val(),'".$stage."','".$kuota_jalur_row['lintas_dt2']."','".$kuota_jalur_row['jml_sekolah']."');\" class='form-control' ".($stage!='3'?'required':(!$login?'disabled':'required')).">
									<option value=''></option>";
									foreach($dt2_rows as $row){
										echo "<option value='".$row['dt2_id']."_".$row['dt2_kd']."_".$row['nama_dt2']."'>".$row['nama_dt2']."</option>";
									}
									echo "</select>";
									
								?>
								<span class="help-block">Pilih Kab./Kota sesuai Kartu Keluarga</span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-md-4" for="input_kecamatan">Kecamatan <font color='red'>*</font></label>
						<div class="col-md-8">
							<div id="district-loader" style="display:none">
								<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-1.gif"/>
							</div>
							<div class="input" id="cont_input_kecamatan">
								<select name="input_kecamatan" id="input_kecamatan" tabindex="8" class="form-control" <?=($stage!='3'?'required':(!$login?'disabled':'required'));?>>
									<option value="">-- Pilih Kab./Kota lebih dulu --</option>									
								</select>
								<span class="help-block">Pilih Kecamatan sesuai Kartu Keluarga</span>
							</div>
						</div>
					</div>
				
					
					<div class="form-group">
						<label class="control-label col-md-4" for="input_no_telp">No. Telepon <font color='red'>*</font></label>
						<div class="col-md-8">
							<div class="input">
								<input class="form-control" id="input_no_telp" type="text" tabindex="9" name="input_no_telp" value="" <?=($stage!='3'?'required':(!$login?'disabled':'required'));?>>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<div class="col-md-12" align="right">
							<h4 class="form-inline-title">Nilai Ujian Nasional SMP Thn. <?=date('Y');?></h4>
						</div>
					</div>

					<?php					

					$value_input_rule = '';
					if($input_arr['nil_bhs_indonesia']==0 and $input_arr['nil_bhs_inggris']==0 and $input_arr['nil_matematika']==0 and $input_arr['nil_ipa']==0)
						$value_input_rule = 'required';
					else
						$value_input_rule = 'readonly';
					echo "
						<div class='form-group'>
							<label class='control-label col-md-4' for='input_nil_bhs_indonesia'>Bhs. Indonesia <font color='red'>*</font></label>
							<div class='col-md-8'>
								<div class='input'>
									<input class='form-control decimal' id='input_nil_bhs_indonesia' tabindex='10' type='text' onkeyup=\"count_tot_value();\" name='input_nil_bhs_indonesia' value='".$input_arr['nil_bhs_indonesia']."' ".($stage!='3'?$value_input_rule:(!$login?'disabled':'required')).">
								</div>
							</div>
						</div>

						<div class='form-group'>
							<label class='control-label col-md-4' for='input_nil_bhs_inggris'>Bhs. Inggris <font color='red'>*</font></label>
							<div class='col-md-8'>
								<div class='input'>
									<input class='form-control decimal' id='input_nil_bhs_inggris' tabindex='11' type='text' onkeyup=\"count_tot_value();\" name='input_nil_bhs_inggris' value='".$input_arr['nil_bhs_inggris']."' ".($stage!='3'?$value_input_rule:(!$login?'disabled':'required')).">
								</div>
							</div>
						</div>

						<div class='form-group'>
							<label class='control-label col-md-4' for='input_nil_matematika'>Matematika <font color='red'>*</font></label>
							<div class='col-md-8'>
								<div class='input'>
									<input class='form-control decimal' id='input_nil_matematika' tabindex='12' type='text' onkeyup=\"count_tot_value();\" name='input_nil_matematika' value='".$input_arr['nil_matematika']."' ".($stage!='3'?$value_input_rule:(!$login?'disabled':'required')).">
								</div>
							</div>
						</div>

						<div class='form-group'>
							<label class='control-label col-md-4' for='input_nil_ipa'>IPA <font color='red'>*</font></label>
							<div class='col-md-8'>
								<div class='input'>
									<input class='form-control decimal' id='input_nil_ipa' type='text' tabindex='13' onkeyup=\"count_tot_value();\" name='input_nil_ipa' value='".$input_arr['nil_ipa']."' ".($stage!='3'?$value_input_rule:(!$login?'disabled':'required')).">
								</div>
							</div>
						</div>					

						<div class='form-group'>
							<label class='control-label col-md-4' for='input_tot_nilai'>Total Nilai</label>
							<div class='col-md-8'>
								<div class='input'>
									<input class='form-control decimal' id='input_tot_nilai' type='text' tabindex='14' name='input_tot_nilai' value='".$input_arr['tot_nilai']."' readonly>
								</div>
							</div>
						</div>";
					?>

				</div>

				<div class="col-md-6">
					<div class="form-group">
						<div class="col-md-12" align="right">
							<h4 class="form-inline-title">Berkas Kelengkapan Pendaftaran</h4>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">
							<table class="table table-bordered table-hover">
								<tbody>
									<?php
										$i=0;
										$index = 14;
										foreach($dokumen_persyaratan_rows as $row){
											$i++;
											$index++;
											echo "
											<tr>
												<td>
													<input id='input_berkas' type='checkbox' name='input_berkas".$i."' tabindex='".$index."' value='".$row['ref_dokumen_id']."' ".($row['status']=='mandatory'?"onclick='return false' checked":'')."><label for='input_berkas".$i."'><span></span>".$row['nama_dokumen']."</label>";
													if($row['status']=='mandatory')
														echo "<span class='badge badge-primary pull-right' style='background:#f71313!important'>Wajib</span>";
													else
														echo "<span class='badge badge-primary pull-right' style='background:#ffc044!important'>Opsional</span>";
												echo "</td>
											</tr>";
										}
									?>
									
								</tbody>
							</table>
							<input type="hidden" name="input_n_berkas" value="<?=$i;?>"/>
						</div>
					</div>
				</div>
			</div>

			<div class="row">				
					
				<?php

				if($kuota_jalur_row['lintas_dt2']!='0')
					$class = 'col-md-12';
				else{
					$class = 'col-md-6';
					if($path!='3')
						$class .= " col-md-offset-3";
				}
				
				echo "
				<div class='".$class."'>";

					if($kuota_jalur_row['lintas_dt2']=='1')
					{
						echo "
						<div class='alert alert-warning'>
						  <strong>Perhatian!</strong><br />
						  Pada Jalur Pendaftaran ini anda diperbolehkan memilih sekolah dari kota/kab. lain yang berbatasan dengan kota/kab. domisili.
						</div>";
					}

					echo "
					<div id='regency-dest-school-loader' style='display:none' align='center'>
						<img src='".$this->config->item('img_path')."ajax-loaders/ajax-loader-1.gif'/> Mohon tunggu ...
					</div>

					<div id='regency-dest-school'>
						<table class='table table-bordered'>								
							<thead>
								<tr><td width='4%' align='center'><b>#</b></td>";
								if($kuota_jalur_row['lintas_dt2']!='0')
									echo "<td width='50%' align='center'><b>Kota/Kab.</b></td>";
								
								if($stage=='2')
									echo "<td align='center'><b>SMK Tujuan</b></td><td align='center'><b>Jenis Kompetensi</b></td>";
								else
									echo "<td align='center'><b>".($stage=='1'?'SMA Tujuan':'Sekolah Tujuan')."</b></td>";
								
								echo "</tr>

							</thead>
							<tbody>";								
								for($i=1;$i<=$kuota_jalur_row['jml_sekolah'];$i++)
								{
									echo "
									<tr>
									<td align='center'>".$i."</td>";
									if($kuota_jalur_row['lintas_dt2']!='0')
									{
										echo "
										<td>
										<div id='cont_input_dt2_sekolah_tujuan".$i."'>
											<select name='input_dt2_sekolah_tujuan".$i."' id='input_dt2_sekolah_tujuan".$i."' onchange=\"get_destSchools($(this).val(),'".($path!='5' && $stage!='3'?$peserta_row['dt2_id']:'')."','".$stage."','".$kuota_jalur_row['lintas_dt2']."','".$i."');\" class='form-control' ".($stage!='3'?($i==1?'required':''):(!$login?'disabled':'required')).">";
												echo "<option value=''>".($kuota_jalur_row['lintas_dt2']=='1'?"-- Pilih Kab./Kota Domisili lebih dulu --":"")."</option>";
												foreach($pengaturan_dt2_sekolah_rows as $row)
												{
													$keterangan = ($kuota_jalur_row['lintas_dt2']=='1'?" (".ucwords($row['status']).")":"");
													echo "<option value='".$row['dt2_sekolah_id']."'>".$row['nama_dt2'].$keterangan."</option>";
												}
											echo "</select>
										</div>
										</td>";
									}

									echo "<td>
										<div id='dest-school-loader".$i."' style='display:none'>
											<img src='".$this->config->item('img_path')."ajax-loaders/ajax-loader-1.gif'/>
										</div>
										<div id='cont_input_sekolah_tujuan".$i."'>
											<select name='input_sekolah_tujuan".$i."' id='input_sekolah_tujuan".$i."' onchange=\"get_destFields($(this).val(),'".$i."')\" class='form-control' ".($stage!='3'?($i==1?'required':''):(!$login?'disabled':'required')).">
												<option value=''>".($path=='3'?'-- Pilih Kab./Kota Domisili lebih dulu --':'-- Pilih Kab./Kota Sekolah lebih dulu --')."</option>";
											echo "</select>
										</div>
										<input type='hidden' name='input_komptensi_tujuan' value='0_0'/>
									</td>";

									if($stage=='2'){
										echo "
										<td>
											<div id='dest-field-loader".$i."' style='display:none'>
												<img src='".$this->config->item('img_path')."ajax-loaders/ajax-loader-1.gif'/>
											</div>
											<div id='cont_input_kompetensi_tujuan".$i."'>
												<select name='input_kompetensi_tujuan".$i."' id='input_kompetensi_tujuan".$i."' class='form-control' ".($i==1?'required':'').">
													<option value=''>- Pilih SMK lebih dulu -</option></select>
											</div>
										</td>";
									}

									echo "</tr>";
								}
							echo "</tbody>
						</table>
						<input type='hidden' name='n_schools' id='n_schools' value='".$kuota_jalur_row['jml_sekolah']."'/>";

					echo "</div>
				</div>";


			if($stage!='3'){
				if($path=='3')
				{
					if($kuota_jalur_row['lintas_dt2']=='0'){
						echo "<div class='col-md-6'>";
					}else{
						echo "</div>";
					}
					echo "<div class='row'>
						<div class='col-md-12'>
							<div class='form-group'>
								<div class='col-md-6' align='right'>
									<h4 class='form-inline-title'>Jenis Ujian Nasional SMP</h4>
								</div>
								<div class='col-md-6'>
									<input type='text' class='form-control' name='input_jenis_ujian_smp' value='".$peserta_row['mode_un']."' readonly/>
								</div>
							</div>	
						</div>
					</div>";
					if($kuota_jalur_row['lintas_dt2']=='0'){
						echo "</div>";
					}
				}else{
					echo "<input type='hidden' name='input_jenis_ujian_smp' value''/>";
				}
			}



				if($path=='4'){
					echo "
					<div class='row'>
						<div class='col-md-12'>
							<div class='form-group'>
								<div class='col-md-12' align='center'>
									<h4 class='form-inline-title'>Prestasi Akademik/Non Akademik</h4>
								</div>
							</div>						
							<div class='form-group'>
								<div class='col-md-12'>
									<table class='table table-bordered'>
										<tbody>";
											$i = 0;
											foreach($tingkat_kejuaraan_rows as $row1){
												$i++;
												$j = 1;
												echo "
												<tr>
													<td>													
														<input type='checkbox' name='input_tingkat_kejuaraan".$i."' id='input_tingkat_kejuaraan' onclick=\"toggle_detail_achievement(".$i.",$(this).prop('checked'))\" value='".$row1['ref_tkt_kejuaraan_id']."'>
														<label for='tingkat_kejuaraan".$i."'><span></span><b>Tingkat ".$row1['tingkat_kejuaraan']."</b></label>
													</td>
												</tr>
												<tr>
													<td>
													<table class='table' id='detail_achievement".$i."' style='display:none'>
														<thead>
															<tr><td>Bidang</td><td>Nama Kejuaraan</td><td>Penyelenggaran</td><td>No. Sertifikat</td><td>Peringkat</td><td>Tahun</td></tr>
														</thead>
														<tbody id='detail_achievement".$i."-tbody'>
															<tr id='row-1'>
															<td>
																<div><select name='input_bidang".$i."_".$j."' class='form-control' required>															
																".$bidang_kejuaraan_opts."
																</select></div>
															</td>
															<td>
																<div><input type='text' name='input_nm_kejuaraan".$i."_".$j."' class='form-control' required/></div>
															</td>
															<td>
																<div><input type='text' name='input_penyelenggara".$i."_".$j."' class='form-control' required/></diV>
															</td>
															<td>
																<div><input type='text' name='input_no_sertifikat".$i."_".$j."' class='form-control' required/></div>
															</td>
															<td>
																<div>
																	<select name='input_peringkat".$i."_".$j."' id='input_peringkat".$i."_".$j."' class='form-control' required>";
																		for($k=1;$k<=3;$k++){
																			echo "<option value='".$k."'>".$k."</option>";
																		}
																	echo "</select>
																</div>
															</td>
															<td>
																<div><input type='text' name='input_thn_kejuaraan".$i."_".$j."' id='input_thn_kejuaraan".$i."' class='form-control' required/></div>
															</td>
															<td></td>
															</tr>
														</tbody>														
													</table>
													<input type='hidden' name='input_n_prestasi".$i."' id='n_achievement_rows".$i."' value='".$j."'/>
													</td>
												</tr>";
											}
										echo "
										</tbody>
									</table>
									<input type='hidden' name='input_n_tingkat_kejuaraan' value='".$i."'/>
								</div>
							</div>
						</div>
					</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12" align="center">
					<input type="checkbox" name="input_persetujuan" id="input_persetujuan" onclick="return false" data-toggle="modal" data-target="#terms-conditionModal" value="1">
					<label for="input_persetujuan"><span></span>Baca Ketentuan Berlaku</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<hr></hr>
					<center>

						<?php
						$disabled = ($stage=='3'?(!$login?'disabled':''):'');
						?>
						<button type="submit" class="btn btn-success" id="submit-btn" tabindex="99" <?=$disabled;?>><i class="fa fa-save"></i> Submit</button><br /><br />

						<div id="submit-loader" style="display:none">
							<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-1.gif"/> Data sedang dikirim ke Server ... !!
						</div>
						<div id="submit-notify" style="display:none;color:red"></div>
					</center>
				</div>
			</div>

			<!-- Login Modal -->
		    <div class="modal fade" id="terms-conditionModal" role="dialog">
		        <div class="modal-dialog modal-lg">
		            <div class="modal-content">		                
		                <div class="modal-header">		                    
		                    <h4 class="modal-title">Ketentuan Berlaku</h4>
		                </div>
		                <div class="modal-body">
		                    <?php
		                    	echo $ketentuan_berlaku;
		                    ?>
		                </div>
		                <div class="modal-footer">
		                    <div class="row">
		                        <div class="col-lg-6 col-md-6">&nbsp;</div>
		                        <div class="col-lg-6 col-md-6">
		                            <button type="button" class="btn btn-default" onclick="$('#input_persetujuan').prop('checked',true)" data-dismiss="modal"><i class="fa fa-check"></i> Setuju</button>
		                            <button type="button" class="btn btn-default" onclick="$('#input_persetujuan').prop('checked',false)" data-dismiss="modal"><i class="fa fa-times"></i> Tidak Setuju</button>		                            
		                        </div>
		                    </div>  
		                </div>
		            </form>
		            </div>
		        </div>
		    </div>

		</form>
	</fieldset>
	
</div>

<link rel="stylesheet" href="<?=$this->config->item('js_path');?>plugins/iCheck/all.css">
<script type="text/javascript" src="<?=$this->config->item('js_path');?>plugins/iCheck/icheck.min.js"></script>

<script type="text/javascript">	
	
    var $reg_form=$('#reg-form'), $submitLoader = $('#submit-loader'), $submitBtn = $('#submit-btn'), $submitNotify = $('#submit-notify'), $content = $('#data-view');
    var path = "<?=$path;?>";


   	function validate_checkboxes(id,path){
   		var chks = document.querySelectorAll('input[id="'+id+'"]');
   		var hasChecked = false;
   		for(var i = 0;i < chks.length;i++){
   			if(chks[i].checked){
   				hasChecked = true;
   				break;
   			}
   		}
   		
   		if(!hasChecked && path=='4'){
   			alert('Silahkan isikan data prestasi minimal satu tingkat kejuaraan!');
   			return false;
   		}
   		return true;
   	}

   	function validate_chosen(){
   		var stage = $('#input_tipe_sekolah').val();
   		var n = $('#input_jml_sekolah').val();
   		var input_name = (stage=='1'?'input_sekolah_tujuan':'input_kompetensi_tujuan');

   		filled = 0;
   		for(i=1;i<=n;i++){
   			val = $('#'+input_name+i).val();
   			filled += (val!=''?1:0);
   		}

   		var chosen = [];
   		var status = true;
   		for(i=1;i<=n;i++){

   			val = $('#'+input_name+i).val();

   			if(chosen.indexOf(val)<0 || chosen.length==0 || filled==1)
			{
				chosen.push(val);				
			}else{
				status = false;
				break;
			}
   		}

   		if(!status){
   			alert((stage=='1'?'Sekolah':'Kompetensi')+' pilihan harus berbeda!');
   			return false;
   		}
   		return true;
   	}

    $(function() {

    	jQuery.extend(jQuery.validator.messages, {
		    required: "Required",		   
		});

        // Validation
        var stat = $reg_form.validate(
	        	{
	        		messages:{
	        			required:'required'
	        		},
	                // Do not change code below
	                errorPlacement : function(error, element) {	                	
	                    error.addClass('error');
	                    error.insertAfter(element.parent());
	                }
	            }
        	);


        $reg_form.submit(function(){

            if(stat.checkForm())
            {
            	if(validate_chosen())
            	{
	            	if(validate_checkboxes('input_tingkat_kejuaraan',path))
	            	{
		            	if($('#input_persetujuan').prop('checked')==true)
		            	{
		            		if(confirm('Anda yakin data inputan sudah benar?'))
		            		{
				                $.ajax({
				                  type:'POST',
				                  url:$reg_form.attr('action'),
				                  data:$reg_form.serialize(),
				                  beforeSend:function(){    
				                  	$submitNotify.hide();
				                    $submitLoader.show();
				                    $submitBtn.attr('disabled',true);
				                  },
				                  success:function(data){                    

				                    error=/ERROR/;

				                    if(data=='failed' || data.match(error))
				                    {
				                        if(data=='failed')
				                        {
				                            content_box = "Data gagal dikirim, silahkan ulangi lagi !";
				                        }else{
				                            x = data.split(':');
				                            content_box = x[1].trim();
				                        }
				                        
				                        $submitLoader.hide();
				                        $submitNotify.html(content_box);
				                        $submitNotify.show();
				                        $submitBtn.attr('disabled',false);
				                    }else{

				                    	$content.html(data);

				                    }
				                  }
				                  
				                });
							}
						}else{
							alert('Anda wajib menyetujui Ketentuan Berlaku!');
						}
					}
				}
                return false;
            }
        });
    });	

	function init_jquery_plugin(){
		
		$("#input_tgl_lahir").mask('99-99-9999');

		$(".datepicker").datepicker(
			{ 
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
            	changeYear: true

			});

		$(".decimal").inputmask({
		    'alias': 'decimal',
		    rightAlign: false
		  });

		$(".numeric").inputmask({
		    'alias': 'numeric',
		    rightAlign: false
		  });

		//iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });
	}

	function init_jquery_masked2(i){
		$('#input_thn_kejuaraan'+i).mask('9999');
	}

	$(document).ready(function(){
		init_jquery_plugin();
	});

	_bidang_kejuaraan_opts = "<?=$bidang_kejuaraan_opts;?>";

	function toggle_detail_achievement(i,checked){
		var $detail_achievement = $('#detail_achievement'+i), $n_achievement_rows = $('#n_achievement_rows'+i);
		if(checked)
		{
			$detail_achievement.show();
			if($n_achievement_rows.val()=='')
				$n_achievement_rows.val('1');
		}
		else
			$detail_achievement.hide();

		init_jquery_masked2(i)
	}

	function delete_achievement_row(i,order_num)
    {
    	var $tr = $('#detail_achievement'+i+'-tbody > tr');
    	$tr.remove('#row-'+order_num);    	
    }

	function add_achievement_rows(i){
		var $tbody = $('#detail_achievement'+i+'-tbody'), $lc_tbody = $('#detail_achievement'+i+'-tbody tr:last-child'), $n_achievement_rows = $('#n_achievement_rows'+i);
    	var last_row_id = $lc_tbody.attr('id');
    	
    	x = last_row_id.split('-');
    	last_order = x[1];
    	new_order = parseInt(last_order)+1;

    	new_row = "<tr id='row-"+new_order+"'>"+
    			  "<td><select name='input_bidang"+i+"_"+new_order+"' class='form-control' required>"+_bidang_kejuaraan_opts+"</select></td>"+
				  "<td><input type='text' name='input_nm_kejuaraan"+i+"_"+new_order+"' class='form-control' required/></td>"+
				  "<td><input type='text' name='input_penyelenggara"+i+"_"+new_order+"' class='form-control' required/></td>"+
				  "<td><input type='text' name='input_peringkat"+i+"_"+new_order+"' class='form-control numeric'  required/></td>"+
				  "<td><input type='text' name='input_peringkat_thn_kejuaraan"+i+"_"+new_order+"' id='input_peringkat_thn_kejuaraan' class='form-control' required/></td>"+
				  "<td><button type='button' id='achievement_row"+i+"_"+new_order+"' class='btn btn-default btn-xs' onclick=\"delete_achievement_row('"+i+"','"+new_order+"');\"><i class='fa fa-trash-o'></i></button></td>"+
    			  "</tr>";
    	
    	$n_achievement_rows.val(new_order);
    	$tbody.append(new_row);
    	init_jquery_plugin();
	}	

	function get_destSchools(school_regency,regency,school_type,accross_regency,i){
		
		ajax_object.reset_object();
		var data_ajax = new Array('school_regency='+school_regency,'regency='+regency,'school_type='+school_type,'accross_regency='+accross_regency,'i='+i);
        ajax_object.set_url($('#baseUrl').val()+'reg/get_destSchools').set_data_ajax(data_ajax).set_loading('#dest-school-loader'+i).set_content('#cont_input_sekolah_tujuan'+i).request_ajax();

        if(school_type=='2'){
        	$('#input_kompetensi_tujuan'+i).html("<option value''>- Pilih SMK lebih dulu -");
        }
	}

	function get_destFields(school,i){
		ajax_object.reset_object();
		var data_ajax = new Array('school='+school,'i='+i);
        ajax_object.set_url($('#baseUrl').val()+'reg/get_destFields').set_data_ajax(data_ajax).set_loading('#dest-field-loader'+i).set_content('#cont_input_kompetensi_tujuan'+i).request_ajax();
	}

	function get_data_linked_toRegency(regency,school_type,accross_regency,n_schools){
		
		$.ajax({
          type:'POST',
          url:$('#baseUrl').val()+'reg/get_data_linked_toRegency',
          data:'regency='+regency+'&school_type='+school_type+'&accross_regency='+accross_regency+'&n_schools='+n_schools,
          beforeSend:function(){    
            
            $('#district-loader').show();
            $('#cont_input_kecamatan').hide();

            if(school_type=='1')
            {
	            $('#regency-dest-school-loader').show();            
	            $('#regency-dest-school').hide();
	        }
            
          },
          success:function(data){
            
            x = data.split('#%%#');
						
            $('#district-loader').hide();
            $('#cont_input_kecamatan').html(x[0]);
            $('#cont_input_kecamatan').show();

            if(school_type=='1')
            {
	            $('#regency-dest-school-loader').hide();
	            $('#regency-dest-school').html(x[1]);
	            $('#regency-dest-school').show();
	        }
          }
        });
	}

	function count_tot_value(){
		var $bhs_indo = $('#input_nil_bhs_indonesia'), $bhs_inggris = $('#input_nil_bhs_inggris'), 
			$matematika = $('#input_nil_matematika'), $ipa = $('#input_nil_ipa'), $tot_nilai = $('#input_tot_nilai');

		var bhs_indo = gnv($bhs_indo.val()), bhs_inggris = gnv($bhs_inggris.val()), matematika = gnv($matematika.val()), 
			ipa = gnv($ipa.val()), tot_nilai = 0;

		tot_nilai = parseFloat(bhs_indo)+parseFloat(bhs_inggris)+parseFloat(matematika)+parseFloat(ipa);
    	tot_nilai = (tot_nilai==0?0:number_format(tot_nilai,2,'.',','));
    	$tot_nilai.val(tot_nilai);
	}

	function check_regid(regid){

			var $input_no_peserta = $('#input_no_peserta'),
				$input_nama = $('#input_nama'), 
				$input_sekolah_asal = $('#input_sekolah_asal'), 
				$input_jk = $('#input_jk'),$input_sekolah_asal = $('#input_sekolah_asal'),
				$input_tpt_lahir = $('#input_tpt_lahir'),
				$input_tgl_lahir = $('#input_tgl_lahir'),
				$input_alamat = $('#input_alamat'),
				$input_dt2 = $('#input_dt2'),
				$input_kecamatan = $('#input_kecamatan'),
				$input_no_telp = $('#input_no_telp'),
				$input_tpt_lahir = $('#input_tpt_lahir'),
				$input_tgl_lahir = $('#input_tgl_lahir'),
				$input_nil_bhs_indonesia = $('#input_nil_bhs_indonesia'),
				$input_nil_bhs_inggris = $('#input_nil_bhs_inggris'),
				$input_nil_matematika = $('#input_nil_matematika'),
				$input_nil_ipa = $('#input_nil_ipa');
				$submit_btn = $('#submit-btn');

			var n_dt2 = $('#n_schools').val();

		$.ajax({
            type:'POST',
            url:$('#base_url').val()+'reg/check_regid',
            data:'regid='+regid,   
            beforeSend:function(){
                $('#preloadAnimation').show();
            },
            complete:function(){                    
                $('#preloadAnimation').hide();
            },         
            success:function(data){

            	$input_nama.attr('disabled',(data=='exists'));
            	$input_nama.attr('required',(data=='empty'));

            	$input_sekolah_asal.attr('disabled',(data=='exists'));
            	$input_sekolah_asal.attr('required',(data=='empty'));

            	$input_jk.attr('disabled',(data=='exists'));
            	$input_jk.attr('required',(data=='empty'));

            	$input_tpt_lahir.attr('disabled',(data=='exists'));
            	$input_tpt_lahir.attr('required',(data=='empty'));

            	$input_tgl_lahir.attr('disabled',(data=='exists'));
            	$input_tgl_lahir.attr('required',(data=='empty'));

            	$input_alamat.attr('disabled',(data=='exists'));
            	$input_alamat.attr('required',(data=='empty'));

            	$input_dt2.attr('disabled',(data=='exists'));
            	$input_dt2.attr('required',(data=='empty'));

            	$input_kecamatan.attr('disabled',(data=='exists'));
            	$input_kecamatan.attr('required',(data=='empty'));

            	$input_no_telp.attr('disabled',(data=='exists'));
            	$input_no_telp.attr('required',(data=='empty'));

            	$input_tpt_lahir.attr('disabled',(data=='exists'));
            	$input_tpt_lahir.attr('required',(data=='empty'));

            	$input_nil_bhs_indonesia.attr('disabled',(data=='exists'));
            	$input_nil_bhs_indonesia.attr('required',(data=='empty'));

            	$input_nil_bhs_inggris.attr('disabled',(data=='exists'));
            	$input_nil_bhs_inggris.attr('required',(data=='empty'));

            	$input_nil_matematika.attr('disabled',(data=='exists'));
            	$input_nil_matematika.attr('required',(data=='empty'));

            	$input_nil_ipa.attr('disabled',(data=='exists'));
            	$input_nil_ipa.attr('required',(data=='empty'));

	            $submit_btn.attr('disabled',(data=='exists'));
            	
        		$('#input_dt2_sekolah_tujuan1').attr('disabled',(data=='exists'));
        		$('#input_sekolah_tujuan1').attr('disabled',(data=='exists'));
            	

            	if(data=='exists'){
            		alert('Data anda sudah ada dalam database, silahkan login untuk melanjutkan pendaftaran!');
            	}
            }
        });
	}
</script>

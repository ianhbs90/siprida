
<!-- MAIN PANEL -->
<div id="main" role="main">

	<!-- RIBBON -->
	<div id="ribbon">
		<span class="ribbon-button-alignment"> 
			<span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh"  rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true">
				<i class="fa fa-refresh"></i>
			</span> 
		</span>
		<!-- breadcrumb -->
		<ol class="breadcrumb">
			<li>Home</li><li>Administrasi</li><li>Input Pendaftaran</li>
		</ol>		

	</div>
	<!-- END RIBBON -->

	<!-- MAIN CONTENT -->
	<div id="content">

		<div class="row">
			<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
				<h1 class="page-title txt-color-blueDark">		
					<!-- PAGE HEADER -->
					<i class="fa-fw fa fa-pencil-square-o"></i> 
						Administrasi
					<span>>  
						Input Pendaftaran
					</span>
				</h1>
			</div>	
		</div>

		
		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">
				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					
					<div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-3" data-widget-editbutton="false">						
						
						<header>
							<span class="widget-icon"> <i class="fa fa-gear"></i> </span>
							<h2>Input Data Pendaftaran</h2>
						</header>

						<!-- widget div-->
						<div>

							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->

							</div>
							<!-- end widget edit box -->

							<!-- widget content -->
							<div class="widget-body">

								<form class="form-horizontal" id="<?=$form_id;?>" action="<?=base_url().$active_controller;?>/submit_registration_data" method="POST">									
									<input type="hidden" id="add_access" value="<?=($add_access?'1':'0');?>"/>
									<input type="hidden" name="act" value="add"/>
									<fieldset>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label">No. Peserta <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control" name="input_id_pendaftaran" id="input_id_pendaftaran" type="text" required>
														</div>
													</div>
												</div>
												
												<div class="form-group">
													<label class="col-md-4 control-label">Nama <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control" type="text" name="input_nama" id="input_nama" required>												
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-4 control-label">Jenis Kelamin <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<select name="input_jk" id="input_jk" class="form-control" required>
																<option value=""></option>
																<option value="L">Laki-Laki</option>
																<option value="P">Perempuan</option>
															</select>											
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-4 control-label">Tempat, Tgl. Lahir <font color="red">*</font></label>
													<div class="col-md-4">
														<div class="input">
															<input class="form-control" type="text" name="input_tpt_lahir" id="input_tpt_lahir" required>												
														</div>
													</div>
													<div class="col-md-4">
														<div class="input">
															<input class="form-control datepicker" type="text" name="input_tgl_lahir" id="input_tgl_lahir" required>												
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-4 control-label">Sekolah Asal <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control" type="text" name="input_sekolah_asal" id="input_sekolah_asal" required>												
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-4 control-label">Nama Orang Tua</label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control" type="text" name="input_nm_orang_tua" id="input_nm_orang_tua">												
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-md-4 control-label">Mode UN <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<select name="input_mode_un" id="input_mode_un" class="form-control" required>
																<option value=""></option>
																<option value="UNBK">UNBK</option>
																<option value="UNKP">UNKP</option>
															</select>											
														</div>
													</div>
												</div>

												
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label">Nilai Bhs. Indonesia <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control decimal" type="text" name="input_nil_bhs_indonesia" id="input_nil_bhs_indonesia" onkeyup="count_tot_value();" required>												
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-4 control-label">Nilai Bhs. Inggris <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control decimal" type="text" name="input_nil_bhs_inggris" id="input_nil_bhs_inggris" onkeyup="count_tot_value();" required>												
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-4 control-label">Nilai Matematika <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control decimal" type="text" name="input_nil_matematika" id="input_nil_matematika" onkeyup="count_tot_value();" required>												
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-4 control-label">Nilai IPA <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control decimal" type="text" name="input_nil_ipa" id="input_nil_ipa" onkeyup="count_tot_value();" required>												
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-4 control-label">Tot. Nilai <font color="red">*</font></label>
													<div class="col-md-8">
														<div class="input">
															<input class="form-control decimal" type="text" name="input_tot_nilai" id="input_tot_nilai" onkeyup="count_tot_value();" required>												
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-actions">
											<div class="row">
												<div class="col-md-6">													
												</div>
												<div class="col-md-6">													
													<button class="btn btn-primary" type="submit">
														<i class="fa fa-save"></i>
														Submit
													</button>
												</div>
											</div>
										</div>
									</fieldset>
								</form>

							</div>
							<!-- end widget content -->

						</div>
						<!-- end widget div -->

					</div>
					<!-- end widget -->

				</article>
				<!-- WIDGET END -->				

			</div>

			<!-- end row -->			

		</section>
		<!-- end widget grid -->

		<!-- widget grid -->
		<section id="widget-grid" class="">
			<!-- row -->
			<div id="data-view-loader" align="center" style="display:none">
				<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-7.gif"/>
			</div>
			<div class="row" id="data-view">
			</div>

			

			<!-- end row -->
		</section>
		<!-- end widget grid -->

	</div>
	<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->


<script src="<?=$this->config->item('js_path');?>plugins/masked-input/jquery.inputmask.bundle.min.js"></script>
<script src="<?=$this->config->item('js_path');?>plugins/masked-input/jquery.maskedinput.min.js"></script>

<script type="text/javascript">	
	
	var reg_form_id = '<?=$form_id;?>';
    var $reg_form=$('#'+reg_form_id);
    var reg_form_stat=$reg_form.validate({
    		// Do not change code below
                errorPlacement : function(error, element) {
                    error.addClass('error');
                    error.insertAfter(element.parent());
                }
    	});   	

    $reg_form.submit(function(){
    	if($('#add_access').val()=='1')
    	{
	        if(reg_form_stat.checkForm())
	        {        	
	        	ajax_object.reset_object();
	            ajax_object.set_content('')                           
	                           .set_loading('#preloadAnimation')
	                           .enable_pnotify()
	                           .set_form($reg_form)
	                           .submit_ajax('menambah data');
	             $reg_form[0].reset();
	        }
	    }else{
	    	alert('Anda tidak diijinkan menambah data');	    	
	    }
	    return false;
    });

    function count_tot_value(){
		var $bhs_indo = $('#input_nil_bhs_indonesia'), $bhs_inggris = $('#input_nil_bhs_inggris'), 
			$matematika = $('#input_nil_matematika'), $ipa = $('#input_nil_ipa'), $tot_nilai = $('#input_tot_nilai');

		var bhs_indo = gnv($bhs_indo.val()), bhs_inggris = gnv($bhs_inggris.val()), matematika = gnv($matematika.val()), 
			ipa = gnv($ipa.val()), tot_nilai = 0;

		tot_nilai = parseFloat(bhs_indo)+parseFloat(bhs_inggris)+parseFloat(matematika)+parseFloat(ipa);
    	tot_nilai = (tot_nilai==0?0:number_format(tot_nilai,2,'.',','));
    	$tot_nilai.val(tot_nilai);
	}

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
	}

	$(document).ready(function(){
		init_jquery_plugin();
	});
</script>
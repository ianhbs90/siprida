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
			<li>Home</li><li>Administrasi</li><li>Rekonsilidasi</li>
		</ol>
	</div>
	<!-- END RIBBON -->

	<!-- MAIN CONTENT -->
	<div id="content">
		
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h1 class="page-title txt-color-blueDark">
					<!-- PAGE HEADER -->
					<i class="fa-fw fa fa-pencil-square-o"></i> 
						Administrasi
					<span>>  
						Rekonsilidasi
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
					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-3" data-widget-editbutton="false">
						<header>
							<span class="widget-icon"> <i class="fa fa-search"></i> </span>
							<h2>Pencarian Data Verifikasi Pendaftar</h2>
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

								<form class="form-horizontal" id="<?=$form_id;?>" action="<?=base_url().$active_controller.'/search_recon_data';?>" method="POST">
									<input type="hidden" id="base_url" value="<?=base_url();?>"/>
									<fieldset>

										<?php
										if($this->session->userdata('tipe_sekolah')=='2'){
											echo "<div class='form-group'>
												<label class='control-label col-md-2' for='search_kompetensi'>Kompetensi</label>
												<div class='col-md-4'>
													<select class='form-control' name='search_kompetensi' id='search_kompetensi' required>
														<option value=''></option>";	
															foreach($kompetensi_rows as $row){
																echo "<option value='".$row['kompetensi_id']."'>".$row['nama_kompetensi']."</option>";
															}
													
													echo "</select>
												</div>	
											</div>";
										}else{
											echo "<input type='hidden' name='search_kompetensi' value =''>";
										}
										?>

										<div class="form-group">
											<label class="control-label col-md-2" for="search_jalur">Jalur Pendaftaran</label>
											<div class="col-md-4">
												
												<select class="form-control" name="search_jalur" id="search_jalur" required>
													<?php
														echo "<option value=''></option>";	
														foreach($jalur_rows as $row){
															echo "<option value='".$row['ref_jalur_id']."'>".$row['nama_jalur']."</option>";
														}
													?>
												</select>
												
											</div>
										</div>										
									</fieldset>
									
									<div class="form-actions">
										<div class="row">
											<div class="col-md-12">												
												<?php
												echo "<button class='btn btn-primary' type='submit'><i class='fa fa-search'></i> Search</button>";
												?>
											</div>
										</div>
									</div>

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

			<!-- end row -->

		</section>
		<!-- end widget grid -->


		<!-- widget grid -->
		<section id="widget-grid" class="">			
			<!-- row -->
			<div class="row" id="data-view">
			</div>
			<!-- end row -->			

		</section>
		<!-- end widget grid -->		

	</div>
	<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<!-- MODAL PLACE HOLDER -->
<div class="modal fade" id="remoteModal" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" >
			<div id="form-content">
			</div>
			<div id="form-loader" style='display:none;margin:20px;' align="center">
				<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-1.gif"/> mohon tunggu ...
			</div>			
		</div>
	</div>
</div>
<!-- END MODAL -->



<link rel="stylesheet" type="text/css" href="<?=$this->config->item('js_path');?>plugins/iCheck/all.css"/>
<script src="<?=$this->config->item('js_path');?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=$this->config->item("js_path");?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=$this->config->item("js_path");?>plugins/datatables/dataTables.colVis.min.js"></script>
<script src="<?=$this->config->item("js_path");?>plugins/datatables/dataTables.tableTools.min.js"></script>
<script src="<?=$this->config->item("js_path");?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=$this->config->item("js_path");?>plugins/datatable-responsive/datatables.responsive.min.js"></script>
<script type="text/javascript">	
	
	var base_url = $('#base_url').val();
	
	var search_recon_form_id = '<?=$form_id;?>';
    var $search_recon_form=$('#'+search_recon_form_id);
    var search_recon_stat=$search_recon_form.validate({
    		// Do not change code below
                errorPlacement : function(error, element) {
                    error.addClass('error');
                    error.insertAfter(element.parent());
                }
    	});

    $search_recon_form.submit(function(){
        if(search_recon_stat.checkForm())
        {
 
        	ajax_object.reset_object();
            ajax_object.set_content('#data-view')
                           .set_loading('#preloadAnimation')
                           .disable_pnotify()
                           .set_form($search_recon_form)
                           .submit_ajax();
            return false;
        }

    });



</script>
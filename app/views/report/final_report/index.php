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
			<li>Home</li><li>Laporan</li><li>Hasil Akhir</li>
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
						Laporan
					<span>>  
						Hasil Akhir
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
							<h2>Filter Laporan</h2>
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

								<form class="form-horizontal" id="<?=$form_id;?>" action="<?=base_url().$active_controller.'/control_final_report';?>" method="POST">
									
									<input type="hidden" id="baseUrl" value="<?=base_url()?>"/>

									<fieldset>
										<div class="form-group" id="filter_mode">
											<label class="control-label col-md-2" for="filter_mode">Mode</label>
											<div class="col-md-4">
												<select class="form-control" onchange="control_form_element(this.value)" name="filter_mode" id="filter_mode">
													<option value="1" selected>Daftar Sekolah</option>
													<!-- option value="2">Keseluruhan</option -->
												</select>
												
											</div>
										</div>

										<div class="form-group" id="filter_jenjang">
											<label class="control-label col-md-2" for="filter_jenjang">Jenjang</label>
											<div class="col-md-4">
												<select class="form-control" name="filter_jenjang" id="filter_jenjang" required>
													<option selected></option>
													<?php
														foreach($jenjang_rows as $row){
															echo "<option value='".$row['ref_tipe_sklh_id']."'>".$row['nama_tipe_sekolah']."</option>";
														}
													?>
												</select>
												
											</div>
										</div>

										<div class="form-group" id="filter_dt2">
											<label class="control-label col-md-2" for="filter_dt2">Kab./Kota</label>
											<div class="col-md-4">
												<select class="form-control" name="filter_dt2" id="filter_dt2" required>
													<option selected></option>
													<?php
														foreach($dt2_rows as $row){
															echo "<option value='".$row['dt2_id']."'>".$row['nama_dt2']."</option>";
														}
													?>
												</select>
												
											</div>
										</div>

										<div class="form-group" id="filter_format">
											<label class="control-label col-md-2" for="filter_format">Format</label>
											<div class="col-md-4">
												<div id="content_input_sekolah_id">
													<input type="radio" name="filter_format" value="html" checked>&nbsp;HTML<br />
													<input type="radio" name="filter_format" value="excel">&nbsp;Excel
												</div>
											</div>
										</div>
									</fieldset>
									<div class="form-actions">
										<div class="row">
											<div class="col-md-12">										
												<button class="btn btn-primary" type="submit">Buka</button>
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
			<div class="row" id="data-view"></div>
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

<script type="text/javascript">	

	var base_url = $('#base_url').val();
	
	var search_school_form_id = '<?=$form_id;?>';
    var $search_school_form=$('#'+search_school_form_id);
    var search_school_stat=$search_school_form.validate({
    		// Do not change code below
                errorPlacement : function(error, element) {
                    error.addClass('error');
                    error.insertAfter(element.parent());
                }
    	});

    $search_school_form.submit(function(){
        if(search_school_stat.checkForm())
        {
        	$('#_search_dt2').val($('#search_dt2').val());
        	ajax_object.reset_object();
            ajax_object.set_content('#data-view')
            		   .set_plugin_datatable(false)
                           .set_loading('#preloadAnimation')
                           .disable_pnotify()
                           .set_form($search_school_form)
                           .submit_ajax();
            return false;
        }

    });

    function control_form_element(mode){
    	
    	var $filter_jenjang = $('#filter_jenjang'), $filter_dt2 = $('#filter_dt2');

    	if(mode=='1'){
    		$filter_jenjang.show();
    		$filter_dt2.show();
    	}else{
    		$filter_jenjang.hide();
    		$filter_dt2.hide();
    	}
    }

</script>
<div class="row">
	<div class="col-md-12">
		<div class="container-fluid box">
			<div class="alert alert-warning" style="font-size:1.2em">
				<strong>Perhatian!</strong> Silahkan tentukan Kab./Kota dan Sekolah tujuan anda dan klik Tampil
			</div>

			<form class="form-horizontal" id="<?=$form_id;?>" action="<?=base_url().$active_controller.'/search_result_selection';?>" method="POST">
			<input type="hidden" id="baseUrl" value="<?=base_url();?>"/>
			<input type="hidden" id="active_controller" value="<?=$active_controller;?>"/>
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td>Jenjang Sekolah</td>
						<td>
							<select class="form-control" id="search_stage" onchange="get_schools($('#search_dt2').val(),this.value,1)" name="search_stage">
								<?php
									foreach($tipe_sekolah_opts as $row){										
										echo "<option value='".$row['ref_tipe_sklh_id']."'>".$row['nama_tipe_sekolah']."</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr><td>Kab./Kota</td><td>
						<select class="form-control" id="search_dt2" name="search_dt2" onchange="get_schools(this.value,$('#search_stage').val(),0)">
							<option value=""></option>
							<?php
								
								foreach($dt2_opts as $row){	
									echo "<option value='".$row['dt2_id']."'>".$row['nama_dt2']."</option>";
								}
							?>
						</select>
					</td>
					</tr>
					<tr><td>Sekolah</td>
					<td>
						<div id="loader_search_school" style='display:none'>
							<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-1.gif"/>
						</div>
						<div id="content_search_school">
							<select class="form-control" id="search_school" name="search_school">
								<option value="">-- Pilih Kab./Kota lebih dulu --</option>
							</select>
						</div>
					</td>
					</tr>

					<tr id="field_container" style="display:none">
						<td>Kompetensi</td>
						<td>
							<div id="loader_search_field" style='display:none'>
								<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-1.gif"/>
							</div>
							<div id="content_search_field">
								<select class="form-control" id="search_field" name="search_field">
									<option value="">-- Pilih Sekolah lebih dulu --</option>
								</select>
							</div>
						</td>
					</tr>
					
					<tr><td>Jalur Pendaftaran</td>
					<td>
						<select class="form-control" id="search_path" name="search_path" required>
							<option value=""></option>
							<?php
								foreach($jalur_opts as $row){
									if($row['ref_jalur_id']==1 or $row['ref_jalur_id']==3)
										echo "<option value='".$row['ref_jalur_id']."_".$row['nama_jalur']."'>".$row['nama_jalur']."</option>";
								}
							?>
						</select>
						
					</td>
					</tr>
					<tr>
						<td></td>
						<td><button type="submit" class="btn btn-primary">Tampil</button></td>
					</tr>
				</tbody>
			</table>
			</form>

			<div id="data-view">
			</div>
		</div>
	</div>
</div>

<!-- JQUERY VALIDATE -->
 <script src="<?=$this->config->item("js_path");?>plugins/jquery-validate/jquery.validate.min.js"></script>

<script type="text/javascript">	

	var base_url = $('#baseUrl').val();
	
	var search_result_id = '<?=$form_id;?>';
    var $search_result_form=$('#'+search_result_id);
    var search_result_stat=$search_result_form.validate({
    		// Do not change code below
                errorPlacement : function(error, element) {
                    error.addClass('error');
                    error.insertAfter(element.parent());
                }
    	});

    $search_result_form.submit(function(){
        if(search_result_stat.checkForm())
        {
        	ajax_object.reset_object();
            ajax_object.set_content('#data-view')
                           .set_loading('#preloadAnimation')
                           .disable_pnotify()
                           .set_form($search_result_form)
                           .submit_ajax();
            return false;
        }

    });

    function get_schools(dt2_id,stage,control_field){    	
		ajax_object.reset_object();
		var data_ajax = new Array('dt2_id='+dt2_id,'tipe_sekolah='+stage);
        ajax_object.set_url($('#baseUrl').val()+$('#active_controller').val()+'/get_schools').set_data_ajax(data_ajax).set_loading('#loader_search_school').set_content('#content_search_school').request_ajax();

        if(control_field=='1'){
        	if(stage=='2'){
        		$('#field_container').show();
        	}else{
        		$('#field_container').hide();
        	}
        }
	}

	function get_fields(school){
		ajax_object.reset_object();
		var data_ajax = new Array('sekolah_id='+school);
        ajax_object.set_url($('#baseUrl').val()+$('#active_controller').val()+'/get_fields').set_data_ajax(data_ajax).set_loading('#loader_search_field').set_content('#content_search_field').request_ajax();
	}

</script>
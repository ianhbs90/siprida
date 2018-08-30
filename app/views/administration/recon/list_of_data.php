<form class="form-horizontal" id="<?=$form_id;?>" action="<?=base_url().$active_controller.'/submit_recon_data';?>" method="POST">
	<input type="hidden" name="sekolah_id" value="<?=$sekolah_id;?>"/>
	<input type="hidden" name="kompetensi_id" value="<?=$kompetensi_id;?>"/>
	<input type="hidden" name="tipe_sekolah" value="<?=$tipe_sekolah;?>"/>
	<input type="hidden" name="jalur_id" value="<?=$jalur_id;?>"/>
	<input type="hidden" name="n_rows" value="<?=count($rows);?>"/>
	<table id="data-table-jq" class="table table-striped table-bordered table-hover" width="100%">
		<thead>
			<tr>
				<th colspan="<?=($tipe_sekolah=='2'?11:10);?>" style="font-size:1.2!important">
					Jumlah yang tersimpan : <?=$n_data;?> dari <?=count($rows);?> data<br />
					Jumlah yang terkoreksi : <?=$n_koreksi;?> data<br />
				</th>
			</tr>
			<tr>
				<th width='4%'>No.</th>
				<th>No. Pendaftaran</th>
				<th>ID Pendaftaran</th>
				<th>Nama</th>
				<th>Sekolah Asal</th>
				<?php
					if($tipe_sekolah=='2'){
						echo "<th>Kd. Kompetensi</th>";
					}
				?>
				<th>Kd. Sekolah</th>
				<th>Kd. Jalur</th>
				<th>Skor</th>
				<th width='8%'>Berkas Ada</th>
				<th width='8%'>Koreksi Nilai</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$no=0;
			$this->global_model->reinitialize_dao();
			$dao = $this->global_model->get_dao();

			foreach($rows as $row)
			{
				foreach($row as $key => $val){
	                  $key=strtolower($key);
	                  $$key=$val;
	              }

				$no++;

				$levAchievement = '';
				$ratAchievement = '';

				if($jalur_id=='4'){

					$sql = "SELECT * FROM pendaftaran_prestasi WHERE id_pendaftaran='".$id_pendaftaran."' AND status='1'";

			
					$row2 = $dao->execute(0,$sql)->row_array();
					
					$levAchievement = $row2['tkt_kejuaraan_id'];
					$ratAchievement = $row2['peringkat'];
				}

				$score = generate_score($dao,$row['tipe_sekolah_id'],$jalur_id,$row['jarak_sekolah'],
									   $row['tot_nilai'],$levAchievement,$ratAchievement,$row['mode_un']);

				echo "
				<tr><td align='center'>".$no."</td>
				<td>".$no_pendaftaran."</td>
				<td>".$id_pendaftaran."</td>
				<td>".$nama."</td>
				<td>".$sekolah_asal."</td>";
				
				if($tipe_sekolah=='2'){
					echo "<td>".$kompetensi_id."</td>";
				}
				
				echo "<td>".$sekolah_id."</td>";

				echo "<td>".$jalur_id."</td>
				";
				// echo "<td align='right'>".number_format($jarak_sekolah)."</td>";
				echo "<td align='right'>".number_format($score)."</td>
				<td align='center'>
					<input type='hidden' name='id_pendaftaran".$no."' value='".$id_pendaftaran."'/>
					<input type='hidden' name='pilihan_ke".$no."' value='".$pilihan_ke."'/>
					<input type='checkbox' name='verified".$no."' id='verified".$no."' value='1' ".($row['n_rekon']>0?'checked':'')."/>
	            </td>
	            <td align='center'>
	            	<input type='checkbox' name='correction".$no."' id='correction".$no."' value='1' ".($row['n_correction']>0?'checked':'')."/>
	            </td>
				</tr>";
			}
			?>
		</tbody>
		<tfoot>
			<tr>
			<td colspan="<?=($tipe_sekolah=='2'?11:10);?>" align="right">
				<?php
				echo "
				<button type='submit' class='btn btn-primary'>
				<i class='fa fa-save'></i> Submit
				</button>";
				?>
			</td>
			</tr>
		</tfoot>
	</table>
	
	</form>
<script type="text/javascript">
	$(document).ready(function(){

		$('input').iCheck({
		    checkboxClass: 'icheckbox_square-blue',
		    radioClass: 'iradio_square-blue',
		    increaseArea: '20%' // optional
		  });

		oTable = $('#data-table-jq').dataTable({
            "oLanguage": {
            "sSearch": "Search :"
            },
            "aoColumnDefs": [
                {
                    'bSortable': false,
                    'aTargets': [0]
                } //disables sorting for column one
            ],
            'iDisplayLength': 10,
            "sPaginationType": "full_numbers",
            "paging": false
        });
		/* END TABLETOOLS */
	});

	var base_url = $('#base_url').val();
	
	var submit_recon_form_id = '<?=$form_id;?>';
    var $submit_recon_form=$('#'+submit_recon_form_id);
    var submit_recon_stat=$submit_recon_form.validate({
    		// Do not change code below
                errorPlacement : function(error, element) {
                    error.addClass('error');
                    error.insertAfter(element.parent());
                }
    	});

    $submit_recon_form.submit(function(){
        if(submit_recon_stat.checkForm())
        {
        	ajax_object.reset_object();
            ajax_object.set_content('')
                           .set_loading('#preloadAnimation')
                           .enable_pnotify()
                           .set_form($submit_recon_form)
                           .submit_ajax('menyimpan data');
            return false;
        }

    });
</script>
<?php
	$admin_type = $this->session->userdata('admin_type_id');	
	if($admin_type=='1' or $admin_type=='2'){	
	echo "
	<div class='row' style='margin-bottom:5px;' align='center'>
		<div class='col-lg-12 col-md-12'>";

			if($add_access)
				echo "<a id='add-button2' onclick=\"load_form(this.id)\" class='btn btn-default btn-xs' data-toggle='modal' data-target='#remoteModal'>";
			else
				echo "<a onclick=\"alert('anda tidak diijinkan untuk menambah data');\" class='btn btn-default btn-xs'>";

			echo "<input type='hidden' name='act' value='add' id='ajax-req-dt'/>
				  <input type='hidden' id='ajax-req-dt' name='type' value='2'/>
				  <i class='fa fa-plus'></i> Tambah</a>&nbsp;";

			/*
			if($update_access)
				echo "<a id='autoConfig-button2' onclick=\"load_form_autoConfig(this.id)\" class='btn btn-default btn-xs' data-toggle='modal' data-target='#remoteModal'>";
			else
				echo "<a onclick=\"alert('anda tidak diijinkan untuk merubah data');\" class='btn btn-default btn-xs'>";

			echo "<input type='hidden' name='act' value='add' id='ajax-req-dt'/>
				  <input type='hidden' id='ajax-req-dt' name='type' value='2'/>
				  <i class='fa fa-gear'></i> Pengaturan Otomatis</a>";
			*/
		echo "</div>
	</div>";
	}
	echo "
	<table id='data-table-jq2' class='table table-bordered'>
		<thead>
			<th>No.</th>
			<th>Nama Sekolah</th>";
			
			// echo "<th>Jml. Diterima</th>
			// <th>Domisili</th>
			// <th>Afirmasi</th>
			// <th>Akademik</th>
			// <th>Prestasi</th>
			// <th>Khusus</th>			
			// <th>Jml. Kuota</th>";

			echo "<th>Sisa Kuota</th>
			<th>Tinggal Kelas</th>
			<th>Tot. Kuota</th>
			<th width='10%'>Aksi</th></tr>

		</thead>
		<tbody>";
			$no = 0;			
			foreach($rows as $row){
				if(($admin_type=='1' or $admin_type=='2') 
					or (($admin_type=='3' or $admin_type=='4') and $this->session->userdata('sekolah_id')==$row['sekolah_id']))
				{
					$no++;				
					echo "<tr>
					<td align='center'>".$no."</td>
					<td>".$row['nama_sekolah']."</td>";
					// echo "<td align='right'>".number_format($row['jml_diterima'])."</td>
					// <td align='right'>".number_format($row['kuota_domisili'])."</td>
					// <td align='right'>".number_format($row['kuota_afirmasi'])."</td>
					// <td align='right'>".number_format($row['kuota_akademik'])."</td>
					// <td align='right'>".number_format($row['kuota_prestasi'])."</td>
					// <td align='right'>".number_format($row['kuota_khusus'])."</td>
					// <td align='right'>".number_format($row['jml_kuota'])."</td>";

					echo "
					<td align='right'>".number_format($row['sisa_kuota'])."</td>
					<td align='right'>".number_format($row['jml_tinggal_kelas'])."</td>
					<td align='right'>".number_format($row['grand_kuota'])."</td>
					<td align='center'>";
						if($update_access)
		                	echo "<a href='#' title='Edit' class='btn btn-xs btn-default' id='edit2_".$no."' onclick=\"load_form(this.id)\" data-toggle='modal' data-target='#remoteModal'>";
		                else
		                	echo "<a href='#' title='Edit' class='btn btn-xs btn-default' onclick=\"alert('anda tidak diijinkan untuk merubah data!');\">";
		                
		                echo "
		                <input type='hidden' id='ajax-req-dt' name='id' value='".$row['pengaturan_kuota_id']."'/>
		                <input type='hidden' id='ajax-req-dt' name='type' value='2'/>
		                <input type='hidden' id='ajax-req-dt' name='act' value='edit'/>
		            	<i class='fa fa-edit'></i></a>&nbsp";

		            	if($delete_access)
		            		echo "<a href='#' title='Hapus' class='btn btn-xs btn-default' onclick=\"if(confirm('Anda yakin?')){delete_record(this.id,'2')}\" id='delete2_".$no."'>";
		            	else
		            		echo "<a href='#' title='Hapus' class='btn btn-xs btn-default' onclick=\"alert('anda tidak diijinkan untuk menghapus data!')\">";

		            	echo "
		            	<input type='hidden' id='ajax-req-dt' name='id' value='".$row['pengaturan_kuota_id']."'/>
		            	<input type='hidden' id='ajax-req-dt' name='type' value='2'/>
		            	<input type='hidden' id='ajax-req-dt' name='act' value='delete'/>
		            	<i class='fa fa-trash-o'></i></a>
					</td></tr>";
				}
			}
		echo "</tbody>
	</table>";
	
?>

<script type="text/javascript">
	$(function(){
		$('#data-table-jq2').dataTable({
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
            "sPaginationType": "full_numbers"
        });
	});
</script>
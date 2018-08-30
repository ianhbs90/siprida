<style type="text/css">
	table.table tr.selected > td{		
		font-weight:bold!important;
        background: #0185c6;
        color:#fff;
	}
</style>
<div style="padding-top:5px;padding-bottom:5px">
	<?php 
	if($jalur_id=='1'){?>
	<div class="alert alert-warning" style="font-size:1.2em">
		Kelulusan Calon Peserta Didik Baru pada sekolah ini didasarkan pada <u>jarak terdekat domisili yang bersangkutan dengan sekolah ini</u> dibandingkan dengan 
		<u>jarak domisili yang bersangkutan dengan sekolah pilihan lainnya</u> ! 
	</div>
	<?php } ?>
</div>
<table class='table table-bordered table-striped table-hover'>
	<?php

	$lbl_skor = 'Skor';
	$satuan = '';

	if($tipe_sekolah=='1'){
	    $lbl_skor = ($jalur_id=='1' || $jalur_id=='2'?'Jarak':'Skor');
	    $satuan = ($jalur_id=='1' || $jalur_id=='2'?' m':'');
	}

	echo "
	<thead>";

		echo "<tr>
	        <td colspan='6' align='left'>
	        <b>JALUR ".strtoupper($nama_jalur);
	        	
	        echo ", Kuota : ".number_format($kuota)."</b>";

	        if($tipe_sekolah=='2'){
	        	echo "<br />KOMPETENSI : ".strtoupper($nama_kompetensi);
	        }
	        	        
	        echo "<a href='".base_url().$active_controller."/print_result/".$sekolah_id."/".$jalur_id."/".$tipe_sekolah."/".$kompetensi_id."' 
	        	class='btn btn-default btn-xs pull-right' target='_blank'><i class='fa fa-print'></i> Cetak</a>";

	        echo "</td>";

	    echo "</tr>";

	    echo "<tr>
	        <td width='4%' align='center'><b>No.</b></td>
	        <td align='center'><b>No. Peserta</b></td>
	        <td align='center'><b>Nama</b></td>
	        <td><b>Sekolah Asal</b></td>";
	        echo "<td align='center'><b>".$lbl_skor."</b></td>";

	    echo "</tr>
	</thead>
	<tbody>";
		
		$i = 0;

		$need_confirmed = array('2-18-19-01-001-302-3','2-18-19-01-001-007-2',
								'2-18-19-04-080-046-3','2-18-19-01-063-197-4','2-18-19-01-133-175-2');

		foreach($rows as $row){
			$i++;
			
			$marked = (in_array($row['id_pendaftaran'],$need_confirmed) || $row['score']==0?"<font color='red'>(perlu verifikasi ulang)</font>":"");
			
			echo "<tr>
				<td align='center'>".$i."</td>
				<td align='center'>".$row['id_pendaftaran']."</td>
	            <td>".$row['nama']." ".$marked."</td>
	            <td>".$row['sekolah_asal']."</td>";
	    		echo "<td align='right'>".number_format($row['score'],0,',','.').$satuan."</td>";
				echo "</tr>";
		}
	
	echo "</tbody>";

	?>
</table>
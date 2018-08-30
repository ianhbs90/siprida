<html>
	<head>
		<title>PPDB Online 2018 | Provinsi Sulawesi Selatan</title>
		<link rel="stylesheet" type="text/css" href="<?=$this->config->item('css_path');?>report-style.css"/>		
		<style type="text/css">@import "<?=$this->config->item('css_path');?>report-table-style.css";</style>
	</head>
	<body>
		<div style="margin:20px;">
		<div class="fluid">
			<img src="<?=$this->config->item('img_path');?>logo_sulsel.png" width="48px" style="float:left"/>
			<div style="margin-left:10px;margin-top:5px;float:left">
			<h3>
				<?php
				echo "
				DINAS PENDIDIKAN PROVINSI ".strtoupper($this->_SYS_PARAMS[2])."<br />";
				echo strtoupper($nama_sekolah);
				if($tipe_sekolah=='3')
					echo "<font style='font-weight:normal'> (Boarding School)</font>";
				?>						
			</h3>
			<font style="font-size:1.3em!important;">Pengumuman Hasil Seleksi Penerimaan Peserta Didik Baru 2018</font>
			</div>
		
			<div style="clear:both"></div>
			</div><br />
			
		<table class="report" cellpadding="0" cellspacing="0">
			<?php

			$lbl_skor = 'Skor';
			$satuan = '';

			if($tipe_sekolah=='1'){
			    $lbl_skor = ($jalur_id=='1' || $jalur_id=='2'?'Jarak':'Skor');
			    $satuan = ($jalur_id=='1' || $jalur_id=='2'?' m':'');
			}

			echo "
			<thead>
				<tr>
			        <td colspan='5' align='left'>
			        <b>
			        JALUR ".strtoupper($nama_jalur);
			        if($tipe_sekolah=='3'){
			        	echo ", Kuota : ".number_format($kuota)."</b>";
			        }
			        echo "</b>
			        </td>
			    </tr>
			    <tr>
			        <td width='4%' align='center'><b>Peringkat</b></td>
			        <td align='center'><b>No. Peserta</b></td>
			        <td align='center'><b>Nama</b></td>
			        <td><b>Sekolah Asal</b></td>";
			        echo "<td align='center'><b>".$lbl_skor."</b></td>";
			    echo "</tr>
			</thead>
			<tbody>";

				$i = 0;

				foreach($rows as $row){
					$i++;
					
					echo "<tr>
						<td align='center'>".$i."</td>
						<td align='center'>".$row['id_pendaftaran']."</td>
			            <td>".$row['nama']."</td>
			            <td>".$row['sekolah_asal']."</td>";
			    		echo "<td align='right'>".number_format($row['score'],0,',','.').$satuan."</td>";
						echo "</tr>";
				}
			echo "</tbody>";

			?>
		</table>
	</div>
	</body>
</html>
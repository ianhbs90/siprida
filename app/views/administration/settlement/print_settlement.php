<!DOCTYPE html>
<html>
	<head>
		<title>PPDB Online Prov. <?=$this->_SYS_PARAMS[2]." Tahun ".date('Y');?> | Hasil Pendaftaran Ulang</title>
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
						DINAS PENDIDIKAN PROVINSI ".strtoupper($this->_SYS_PARAMS[2])."<br />
						".strtoupper($this->session->userdata('nama_sekolah'))."<br />";
						?>						
					</h3>
					Hasil Pendaftaran Ulang PPDB Online
					</div>
				
				<div style="clear:both"></div>
			</div>
			<br />
			<div class="fluid">				
				<div class="grid12">					
					<?php	
					$x_tgl_daftar_ulang = explode(' ',$daftar_ulang_row['tgl_daftar_ulang']);
					$tgl_daftar_ulang = us_date_format($x_tgl_daftar_ulang[0]);

					
					echo "
					<table class='report' cellpadding='0' cellspacing='0'>
						<tbody>
							<tr><td>No. Registrasi</td><td><b>".$pendaftaran_row['no_pendaftaran']."</b></td></tr>
							<tr><td>Nama</td><td>".$pendaftaran_row['nama']."</td></tr>
							<tr><td>J. Kelamin</td><td>".($pendaftaran_row['jk']=='L'?'Laki-laki':'Perempuan')."</td></tr>
							<tr><td>Sekolah Asal</td><td>".$pendaftaran_row['sekolah_asal']."</td></tr>
							<tr><td>Alamat</td><td>".$pendaftaran_row['alamat']."</td></tr>
							<tr><td>Kecamatan</td><td>".$pendaftaran_row['nama_kecamatan']."</td></tr>
							<tr><td>Kab./Kota</td><td>".$pendaftaran_row['nama_dt2']."</td></tr>
							<tr><td>Jalur Pendaftaran</td><td>".$daftar_ulang_row['nama_jalur']."</td></tr>
							<tr><td>Jenjang Sekolah Tujuan</td><td>".$daftar_ulang_row['nama_tipe_sekolah']."</td></tr>";

							if($daftar_ulang_row['tipe_sekolah_id']=='1'){
								echo "<tr><td>Sekolah Tujuan</td><td>".$daftar_ulang_row['nama_sekolah']."</td></tr>";
							}else{
								echo "
								<tr><td>Sekolah Tujuan</td><td>".$daftar_ulang_row['nama_sekolah']."</td></tr>
								<tr><td>Kompetensi Tujuan</td><td>".$daftar_ulang_row['nama_kompetensi']."</td></tr>";
							}

							echo "<tr><td>Tgl. Pendaftaran Ulang</td><td>".indo_date_format($daftar_ulang_row['tgl_daftar_ulang'],'longDate')."</td></tr>
							<tr><td>Status</td><td><b>Terdaftar Ulang</b></td></tr>
						</tbody>
					</table><br />
					<table width='100%'>
						<tr>
							<td width='50%'></td>
							<td align='right'>". ucwords(strtolower($this->session->userdata('nama_dt2'))).", ".indo_date_format($daftar_ulang_row['tgl_daftar_ulang'],'longDate')."</td>
						</tr>
						<tr><td colspan='2'><br /></td></tr>
						<tr>
							<td align='center'>
								Petugas Pendaftaran Ulang,<br /><br /><br /><br /><br />
								<b><u>".$this->session->userdata('fullname')."</u></b>
							</td>
							<td align='center'>
								Calon Siswa,<br /><br /><br /><br /><br />
								<b><u>".$pendaftaran_row['nama']."</u></b>
							</td>
						</tr>
					</table>";
					?>
				</div>
			</div>
		</div>
	</body>
</html>
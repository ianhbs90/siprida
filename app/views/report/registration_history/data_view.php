<?php

	if($error>0)
	{
		switch($error){
			case 1:$warning="Data tidak ditemukan";break;
			default:$warning='kesalahan tidak diketahui';break;
		}		
			

		echo "<br />
		<div class='alert alert-warning'>
			<strong>Perhatian!</strong> ".$warning."
		</div>";

		die();
	}

?>

	<hr></hr>

	<div class="row">				
		<div class="col-md-6">
			<?php	
				echo "
				<table class='table table-bordered table-striped'>
					<tbody>
						<tr><td colspan='2'><b>Data Diri Siswa</b></td></tr>
						<tr><td>No. Peserta</td><td>".$pendaftaran_row['id_pendaftaran']."</td></tr>
						<tr><td>Nama</td><td>".$pendaftaran_row['nama']."</td></tr>
						<tr><td>J. Kelamin</td><td>".($pendaftaran_row['jk']=='L'?'Laki-laki':'Perempuan')."</td></tr>
						<tr><td>Sekolah Asal</td><td>".$pendaftaran_row['sekolah_asal']."</td></tr>
						<tr><td>Alamat</td><td>".$pendaftaran_row['alamat']."</td></tr>
					</tbody>
				</table>";
			?>			
		</div>		
		<div class="col-md-6">
			<?php
				echo "
				<table class='table table-bordered table-striped'>
					<tbody>
						<tr><td colspan='2'><b>Sekolah Tujuan</b></td></tr>";
						if(count($sekolah_tujuan_row)>0){
							echo "<tr><td>Sekolah</td><td>".$sekolah_tujuan_row['nama_sekolah']."</td></tr>";
							if($pendaftaran_row['tipe_sekolah_id']=='2'){
								echo "<tr><td>Kompetensi</td><td>".$sekolah_tujuan_row['nama_kompetensi']."</td></tr>";
							}
							echo "<tr><td>Tgl. Daftar Ulang</td><td>".indo_date_format($pendaftaran_row['tgl_daftar_ulang'],'longDate')."</td></tr>";
						}else{
							echo "<tr><td colspan='3'>Data tidak tersedia</td></tr>";
						}

					echo "</tbody>
				</table>";
			?>
		</div>
	</div>

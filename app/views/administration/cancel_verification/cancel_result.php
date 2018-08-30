<?php	
	echo "<br />
	<div class='col-md-6 col-md-offset-3'>
		<table class='table table-bordered table-striped'>
		<tbody>
			<tr><td>No. Peserta</td><td>".$id_pendaftaran."</td></tr>
			<tr><td>No. Registrasi</td><td>".$no_registrasi."</td></tr>
			<tr><td>No. Verifikasi</td><td>".$no_verifikasi."</td></tr>
			<tr><td>Nama</td><td>".$nama."</td></tr>";
			if($tipe_sekolah_id=='1'){
				echo "<tr><td>Sekolah Tujuan (pilihan ke)</td><td>".$nama_sekolah." (".$pilihan_ke.")</td></tr>";
			}else{
				echo "<tr><td>Sekolah Tujuan</td><td>".$nama_sekolah."</td></tr>";
				echo "<tr><td>Kompetensi Tujuan (pilihan ke)</td><td>".$nama_kompetensi." (".$pilihan_ke.")</td></tr>";
			}
			echo "<tr><td>Status</td><td><font color='green'><b>telah dibatalkan</b></font></td></tr>
		</tbody>
		</table>
	</div>";	
?>
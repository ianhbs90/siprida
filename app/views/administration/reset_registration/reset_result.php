<?php	
	echo "
	<div class='col-md-6 col-md-offset-3'>
		<table class='table table-bordered table-striped'>
		<tbody>
			<tr><td>No. Peserta</td><td>".$id_pendaftaran."</td></tr>
			<tr><td>Nama</td><td>".$nama."</td></tr>
			<tr><td>J. Kelamin</td><td>".($jk=='L'?'Laki-laki':'Perempuan')."</td></tr>
			<tr><td>Sekolah Asal</td><td>".$sekolah_asal."</td></tr>
			<tr><td>Alamat</td><td>".$alamat."</td></tr>
			<tr><td>Status</td><td><font color='green'><b>".($act=='reset'?'telah direset':'telah dihapus')."</b></font></td></tr>
		</tbody>
		</table>
	</div>";	
?>
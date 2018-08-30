<?php	
	echo "
	<div class='col-md-6 col-md-offset-3'>
		<table class='table table-bordered table-striped'>
		<tbody>
			<tr><td>No. Registrasi</td><td><b>".$pendaftaran_row['no_pendaftaran']."</b></td></tr>
			<tr><td>Nama</td><td>".$pendaftaran_row['nama']."</td></tr>
			<tr><td>J. Kelamin</td><td>".($pendaftaran_row['jk']=='L'?'Laki-laki':'Perempuan')."</td></tr>
			<tr><td>Sekolah Asal</td><td>".$pendaftaran_row['sekolah_asal']."</td></tr>
			<tr><td>Alamat</td><td>".$pendaftaran_row['alamat']."</td></tr>
			<tr><td>Kecamatan</td><td>".$pendaftaran_row['nama_kecamatan']."</td></tr>
			<tr><td>Kab./Kota</td><td>".$pendaftaran_row['nama_dt2']."</td></tr>
			<tr><td>Jalur Pendaftaran</td><td>".$pendaftaran_row['nama_jalur']."</td></tr>
			<tr><td>Jenjang Sekolah Tujuan</td><td>".$sekolah_pilihan_row['nama_tipe_sekolah']."</td></tr>";


			if($sekolah_pilihan_row['tipe_sekolah_id']=='1' or $sekolah_pilihan_row['tipe_sekolah_id']=='3'){
				echo "<tr><td>Sekolah Tujuan (pilihan ke)</td><td>".$sekolah_pilihan_row['nama_sekolah']." (".$sekolah_pilihan_row['pilihan_ke'].")</td></tr>";
			}else{
				echo "
				<tr><td>Sekolah Tujuan</td><td>".$sekolah_pilihan_row['nama_sekolah']."</td></tr>
				<tr><td>Kompetensi Tujuan (pilihan ke)</td><td>".$sekolah_pilihan_row['nama_kompetensi']." (".$sekolah_pilihan_row['pilihan_ke'].")</td></tr>";
			}

			echo "<tr><td>Tgl. Verifikasi</td><td>".indo_date_format($sekolah_pilihan_row['tgl_verifikasi'],'longDate')."</td></tr>
			<tr><td>".($pendaftaran_row['jalur_id']=='1'?'Jarak':'Skor')."</td><td>".number_format($sekolah_pilihan_row['score'])." ".($pendaftaran_row['jalur_id']=='1'?'m':'')."</td></tr>
			<tr><td>Status</td><td><font color='green'><b>Telah diverifikasi</b></font></td></tr>
			<tr>
			<td></td>
			<td><a class='btn btn-default' href='".base_url().$active_controller."/print_verification/".$sekolah_pilihan_row['tipe_sekolah_id']."/".urlencode($encoded_regid)."/".urlencode($encoded_fieldid)."' target='_blank'><i class='fa fa-print'></i> Cetak</a></td>
			</tr>
		</tbody>
		</table>
	</div>";
?>
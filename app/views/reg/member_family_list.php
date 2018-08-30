
<table class="table table-bordered">
	<thead>
		<tr>
			<td widtd="5%" align="center"><b>No.</b></td><td align="center"><b>Nama</b></td>
			<td align="center"><b>Tgl. Lahir</b></td><td align="center"><b>Kab./Kota</b></td>
			<td align="center"><b>Umur</b></td><td width="8%" align="center"><b>Aksi</b></td>
		</tr>
	</thead>
	<tbody>
		<?php
			$no=0;
			foreach($rows as $row){
				$no++;

				$age = date_diff(date_create($row['tgl_lahir']), date_create('now'))->y;

				echo "<tr>
				<td align='center'>".$no."</td>
				<td>".$row['nm_lengkap']."</td>
				<td>".indo_date_format($row['tgl_lahir'],'shortDate')."</td>
				<td>".$row['wilayah']."</td>
				<td align='center'>".$age." Tahun</td>
				<td align='center'>
					<button type='button' class='btn btn-default btn-xs' onclick=\"get_main_form('".$row['no_kk']."','".$row['nm_lengkap']."','".$row['alamat']."',
																								 '".$row['tmpt_lahir']."','".$row['tgl_lahir']."')\">
					<i class='fa fa-check'></i>
					</button>
				</td>
				</tr>";
			}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6" align="right">Silahkan pilih data anda pada tabel di atas!</td>
		</tr>
	</tfoot>
</table>
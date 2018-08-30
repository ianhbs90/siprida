<?php
	$onchange = ($tipe_sekolah=='1' || $tipe_sekolah=='3'?"view_school_registrants($(this).val(),'".$jalur_id."','".$i."')":"get_destFields($(this).val(),'".$jalur_id."','".$i."')");	
	echo "
	<select name='input_sekolah_tujuan".$i."' id='input_sekolah_tujuan".$i."' onchange=\"".$onchange."\" class='form-control' ".($i==1?'required':'').">
		<option value=''>".(count($sekolah_rows)==0?"- Pilih Kab.Kota lebih dulu -":"")."</option>";
		foreach($sekolah_rows as $row){
			echo "<option value='".$row['sekolah_id']."_".$row['nama_sekolah']."'>".$row['nama_sekolah']."</option>";
		}
	echo "</select>";
?>
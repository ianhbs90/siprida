<?php
	echo "<select name='search_school' id='search_school' onchange=\"get_fields(this.value)\" class='form-control' required>";
	if(count($rows)==0){
		echo "<option value=''>-- Pilih Kab./Kota lebih dulu --</option>";
	}else{
		echo "<option value=''></option>";
	}
	foreach($rows as $row){
		echo "<option value='".$row['sekolah_id']."_".$row['tipe_sekolah_id']."'>".$row['nama_sekolah']."</option>";
	}
	echo "</select>";
?>
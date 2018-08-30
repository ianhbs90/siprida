<?php
	echo "<select name='search_field' id='search_field' class='form-control' required>";
	if(count($rows)==0){
		echo "<option value=''>-- Pilih Sekolah lebih dulu --</option>";
	}else{
		echo "<option value=''></option>";
	}
	foreach($rows as $row){
		echo "<option value='".$row['kompetensi_id']."_".$row['nama_kompetensi']."'>".$row['nama_kompetensi']."</option>";
	}
	echo "</select>";
?>
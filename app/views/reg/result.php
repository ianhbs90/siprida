<style type="text/css">
	table.table tr.selected > td{		
		font-weight:bold!important;
        background:#26daf2;
        color:#fff;
	}
</style>
<div class="container-fluid box">
<?php    
        
    
    if(count($sekolah_pilihan_rows)>0)
    {
        echo "
        <div class='alert alert-danger'>
        Berikut ini merupakan hasil sementara dari proses verifikasi yang dilakukan di setiap sekolah pilihan.<br />
        <b>Silahkan lakukan pengecekan secara berkala !</b>

    </div>";
    	echo "
    	<ul class='nav nav-tabs tabs-left'>";	   	
    	   	$i=0;
    	    foreach($sekolah_pilihan_rows as $row){
    	    	$i++;
    	    	echo "<li ".($i==1?"class='active'":"")."><a data-toggle='tab' href='#menu".$i."'>(".$i.") ".$row['nama_sekolah']."</a></li>";
    	    }

    	echo "</ul>


    	<div class='tab-content'>";
    		$i=0;
    		$j = 0;

                $lbl_skor = '';
                $satuan = '';
                if($tipe_sekolah=='1'){
                    $lbl_skor = ($jalur_pilihan=='1' || $jalur_pilihan=='2'?'Jarak':'Skor');
                    $satuan = ($jalur_pilihan=='1' || $jalur_pilihan=='2'?' m':'');
                }

                $limit_line = '#1ed83d';

                $report_time = date('d-m-Y H:i');
        		foreach($sekolah_pilihan_rows as $row){			
        
        			$i++;

        			echo "<div id='menu".$i."' class='tab-pane ".($i==1?"fade in active":"fade")."'>";
                    
                    echo "
                    <table class='table table-bordered table-striped table-hover'>
                    <thead>
                        <tr>
                            <td colspan='5' align='left'>
                                <div class='row'>
                                    <div class='col-md-6'>
                                        <b>JALUR ".strtoupper($nama_jalur_pilihan)."</b> 
                                        ".($tipe_sekolah=='1'?"Kuota : ".number_format($row['kuota_jalur'])." orang":"")."
                                    </div>
                                    <div class='col-md-6' align='right'>
                                        Waktu Lihat : ".$report_time."
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width='4%' align='center'><b>Peringkat</b></td>
                            <td align='center'><b>No. Peserta</b></td>
                            <td align='center'><b>Nama</b></td>
                            <td><b>Sekolah Asal</b></td>";
                            // echo "<td><b>Jarak Sekolah</b></td>";
                            // echo "<td><b>Mat</b></td>";
                            // echo "<td><b>B.Ing</b></td>";
                            // echo "<td><b>B.Ind</b></td>";
                            echo "<td align='center'><b>".$lbl_skor."</b></td>

                        </tr>
                    </thead><tbody>";

        			if($tipe_sekolah=='1')
                    {
    					
				    	$k = 0;
				    	$line_printed = false;
                        $hasil_seleksi = $hasil_seleksi_arr[$row['sekolah_id']];

				    	foreach($hasil_seleksi as $row2)
				    	{
                           
				    		$k++;
				    		$selected_row = ($this->session->userdata('nopes')==$row2['id_pendaftaran']?"class='selected'":"");
                            
				    		if($k<=$row['kuota_jalur'])
				    		{
					    		echo "<tr ".$selected_row.">
					    		<td align='center'>".$k."</td>
                                <td align='center'>".$row2['id_pendaftaran']."</td>
                                <td>".$row2['nama']."</td>
                                <td>".$row2['sekolah_asal']."</td>";
                                // echo "<td>".$row2['jarak_sekolah']."</td>
                                // <td>".$row2['nil_matematika']."</td>
                                // <td>".$row2['nil_bhs_inggris']."</td>
                                // <td>".$row2['nil_bhs_indonesia']."</td>";
					    		echo "<td align='right'>".number_format($row2['score'],0,',','.').$satuan."</td>
					    		</tr>";
					    	}
                            else
                            {
					    		// if(!$line_printed){
					    		// 	echo "<tr>
					    		// 	<td colspan='5' align='center' style='background:#fc2f2f;color:#fff'>Batas Kuota</td>
					    		// 	</tr>";
					    		// 	$line_printed = true;
					    		// }
					    		echo "<tr ".$selected_row.">
					    		<td align='center'>".$k."</td>
                                <td align='center'>".$row2['id_pendaftaran']."</td>
                                <td>".$row2['nama']."</td>
                                <td>".$row2['sekolah_asal']."</td>";
                                // echo "<td>".$row2['jarak_sekolah']."</td>
                                // <td>".$row2['nil_matematika']."</td>
                                // <td>".$row2['nil_bhs_inggris']."</td>
                                // <td>".$row2['nil_bhs_indonesia']."</td>";
					    		echo "<td align='right'>".number_format($row2['score'],0,',','.').$satuan."</td>
					    		</tr>";
					    	}

				    	}

				    	    				
        			}
        			else
        			{
        				
        			    		
			    		$params = array($row['sekolah_id'],$row['sekolah_id']);			    		

						$kompetensi_dao->set_sql_params($params);
						$query = $kompetensi_dao->execute(1);
						$kompetensi_rows = $query->result_array();
                        

						foreach($kompetensi_rows as $row2){
							echo "<tr>
							<td colspan='5'><b>".$row2['nama_kompetensi']."</b>, Kuota : ".number_format($row2['kuota_jalur'])." orang</td>
							</tr>";

							$k = 0;
					    	$line_printed = false;
                            $hasil_seleksi = $hasil_seleksi_arr[$row['sekolah_id']][$row2['kompetensi_id']];
					    	foreach($hasil_seleksi as $row3)
					    	{

					    		$k++;
					    		$class = ($this->session->userdata('nopes')==$row3['id_pendaftaran']?"class='selected'":"");

					    		if($k<=$row2['kuota_jalur'])
					    		{

                                    echo "<tr ".$class.">
                                    <td align='center'>".$k."</td>
                                    <td align='center'>".$row3['id_pendaftaran']."</td>
                                    <td>".$row3['nama']."</td>
                                    <td>".$row3['sekolah_asal']."</td>";
                                    // echo "<td>".$row3['jarak_sekolah']."</td>
                                    // <td>".$row3['nil_matematika']."</td>
                                    // <td>".$row3['nil_bhs_inggris']."</td>
                                    // <td>".$row3['nil_bhs_indonesia']."</td>";
                                    echo "<td align='right'>".number_format($row3['score'],0,',','.')."</td>
                                    </tr>";

						    	}else{
						    		// if(!$line_printed){
						    		// 	echo "<tr>
						    		// 	<td colspan='5' align='center' style='background:#ff0000;color:#fff'>Yang berada di bawah garis merah tidak lulus dalam seleksi jalur ini.</td>
						    		// 	</tr>";
						    		// 	$line_printed = true;
						    		// }
						    		echo "<tr ".$class.">
                                    <td align='center'>".$k."</td>
                                    <td align='center'>".$row3['id_pendaftaran']."</td>
                                    <td>".$row3['nama']."</td>
                                    <td>".$row3['sekolah_asal']."</td>";
                                    // echo "<td>".$row3['jarak_sekolah']."</td>
                                    // <td>".$row3['nil_matematika']."</td>
                                    // <td>".$row3['nil_bhs_inggris']."</td>
                                    // <td>".$row3['nil_bhs_indonesia']."</td>";
                                    echo "<td align='right'>".number_format($row3['score'],0,',','.')."</td>
                                    </tr>";
						    	}

					    	}

						}
			    	
        			}
        
        			$j++;
                    echo "</tbody>
                    </table></div>";
        		}

    	echo "</div>";
    }else{
        echo "
        <div class='alert alert-warning'>
            <strong>Perhatian!</strong> Data tidak tersedia!
        </div>";
    }
    
?>
</div>
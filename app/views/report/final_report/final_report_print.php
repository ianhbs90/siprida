<!DOCTYPE html>
<html>
	<head>
		<title>PPDB Online Prov. <?=$this->_SYS_PARAMS[2]." Tahun ".date('Y');?> | Hasil Verifikasi</title>
		<link rel="stylesheet" type="text/css" href="<?=$this->config->item('css_path');?>report-style.css"/>		
		<style type="text/css">@import "<?=$this->config->item('css_path');?>report-table-style.css";</style>
	</head>
	<body>
		<div style="width:650px;margin:0 auto;">
			<div class="fluid">
					<img src="<?=$this->config->item('img_path');?>logo_sulsel.png" width="48px" style="float:left"/>
					<div style="margin-left:10px;margin-top:5px;float:left">
					<h3>
						<?php
						echo "
						DINAS PENDIDIKAN PROVINSI ".strtoupper($this->_SYS_PARAMS[2]);
						?>						
					</h3>
					Hasil Akhir PPDB Online
					<?php 
						if($mode=='2'){
							echo "<br />Per Jenjang Sekolah";
						}else{
							echo "<br />".($jenjang=='1'?'SMA':'SMK').' di '.$nama_dt2;
						}
					?>
					</div>
				
				<div style="clear:both"></div>
			</div>
			<br />
			<div>
				<div class="fluid">				
					<div class="grid12">					
						<?php	
						echo "					
						<table class='report' cellpadding='0' cellspacing='0'>
							<thead><tr>";
								echo "<th rowspan='2'>".($mode=='1'?'Nama Sekolah':'Jenjang')."</th>";
								
								foreach($jalur_rows as $row){
									if($row['ref_jalur_id']=='1'){
									echo "<th colspan='".($row['ref_jalur_id']=='1'?4:2)."'>".$row['nama_jalur']."</th>";
									}
								}

								// echo "<th colspan='3' style='background:green;color:#fff'>Total</th>";
							
							echo "</tr>
								<tr>";
								foreach($jalur_rows as $row){

									if($row['ref_jalur_id']=='1'){
										if($row['ref_jalur_id']=='1'){
											echo "<th>Kuota</th>";
										}
										echo "
										<th>Lulusan</th>";
										
										if($row['ref_jalur_id']=='1'){
											echo "<th>Selisih</th>";
										}
									}

								}
								// echo "<th>Kuota</th><th>Lulusan (1)</th><th>Daftar Ulang (2)</th>";
								echo "</tr>
							</thead>
							<tbody>";
								if($mode=='1'){	

									

									foreach($sekolah_rows as $row1){
										$tot_quota = 0;
											$tot_registrant = 0;
											$tot_graduate = 0;
											$tot_settlement = 0;
											$tot_diff = 0;
										echo "<tr>
										<td>".$row1['nama_sekolah']."</td>";

										foreach($jalur_rows as $row2){

											if($row2['ref_jalur_id']=='1')
											{
												$quota = $quotas[$row1['sekolah_id']][$row2['ref_jalur_id']];
												$quota2 = $quotas2[$row1['sekolah_id']][$row2['ref_jalur_id']];
												$registrant = $registrants[$row1['sekolah_id']][$row2['ref_jalur_id']];
												$graduate = $graduates[$row1['sekolah_id']][$row2['ref_jalur_id']];
												$settlement = $settlements[$row1['sekolah_id']][$row2['ref_jalur_id']];
												$diff = $quota-$settlement;
												$diff2 = $quota2-$graduate;

												if($row2['ref_jalur_id']=='1'){
													echo "<td align='right'>".number_format($quota2)."</td>";	
												}
												echo "<td align='right'>".number_format($graduate)."</td>";
												if($row2['ref_jalur_id']=='1'){
													echo "<td align='right'>".number_format($diff2)."</td>";	
												}

												$tot_quota += $quota;
												$tot_registrant += $registrant;
												$tot_graduate += $graduate;
												$tot_settlement += $settlement;
												$tot_diff += $diff;
											}
										}

										// echo "
										// <td align='right'>".number_format($tot_quota)."</td>
										// <td align='right'>".number_format($tot_graduate)."</td>
										// <td align='right'>".number_format($tot_settlement)."</td>";
										echo "</tr>";
									}

								}else{
									foreach($jenjang_rows as $row1){
										echo "<tr>
										<td>".$row1['nama_tipe_sekolah']."</td>";

										foreach($jalur_rows as $row2){
											
											$graduate = $graduates[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']];
											$settlement = $settlements[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']];

											$selisih = $graduate-$settlement;

											echo "<td align='right'>".number_format($registrants[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']])."</td>";
											echo "<td align='right'>".number_format($graduate)."</td>";
											echo "<td align='right'>".number_format($settlement)."</td>";
											echo "<td align='right'>".number_format($selisih)."</td>";
										}

										echo "</tr>";
									}
								}

							echo "</tbody>
						</table><br />";

						?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
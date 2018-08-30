<!DOCTYPE html>
<html>
	<head>
		<title>PPDB Online Prov. <?=$this->_SYS_PARAMS[2]." Tahun ".date('Y');?> | Hasil Verifikasi</title>
		<link rel="stylesheet" type="text/css" href="<?=$this->config->item('css_path');?>report-style.css"/>		
		<style type="text/css">@import "<?=$this->config->item('css_path');?>report-table-style.css";</style>
	</head>
	<body>
		<div style="margin:20px;">
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
			<div style="width:2500px">
				<div class="fluid">				
					<div class="grid12">					
						<?php	
						echo "					
						<table class='report' cellpadding='0' cellspacing='0'>
							<thead><tr>";
								echo "<th rowspan='2'>".($mode=='1'?'Nama Sekolah':'Jenjang')."</th>";
								
								foreach($jalur_rows as $row){
									echo "<th colspan='4'>".$row['nama_jalur']."</th>";
								}

							echo "
								<th colspan='4' style='background:green;color:#fff'>Total</th>
							</tr>
								<tr>";
								foreach($jalur_rows as $row){

									echo "<th>Kuota (1)</th><th>Lulusan (2)</th><th>Daftar Ulang (3)</th><th>Selisih (4)<br /><small>(1)-(3)</small></th>";

								}
								echo "
								<th>Kuota (1)</th><th>Lulusan (2)</th><th>Daftar Ulang (3)</th><th>Selisih (4)<br /><small>(1)-(3)</small></th>
								</tr>
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
											$quota = $quotas[$row1['sekolah_id']][$row2['ref_jalur_id']];
											$registrant = $registrants[$row1['sekolah_id']][$row2['ref_jalur_id']];
											$graduate = $graduates[$row1['sekolah_id']][$row2['ref_jalur_id']];
											$settlement = $settlements[$row1['sekolah_id']][$row2['ref_jalur_id']];
											$diff = $quota-$settlement;

											echo "<td align='right'>".number_format($quota)."</td>";
											echo "<td align='right'>".number_format($graduate)."</td>";
											echo "<td align='right'>".number_format($settlement)."</td>";
											echo "<td align='right'>".number_format($diff)."</td>";

											$tot_quota += $quota;
											$tot_registrant += $registrant;
											$tot_graduate += $graduate;
											$tot_settlement += $settlement;
											$tot_diff += $diff;

										}

										echo "
										<td align='right'>".number_format($tot_quota)."</td>
										<td align='right'>".number_format($tot_graduate)."</td>
										<td align='right'>".number_format($tot_settlement)."</td>
										<td align='right'>".number_format($tot_diff)."</td>";
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
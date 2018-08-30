<style type="text/css">
	table.table tr.selected > td{		
		font-weight:bold!important;
		background:#5e92e5;
		color:#fff;
	}
</style>

<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">


		<!-- NEW WIDGET START -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget" id="wid-id-7" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false">

				<header>
					
					<ul class="nav nav-tabs pull-left in">

						<li class="active">
							<a data-toggle="tab" href="#hr1"> Baru </span> </a>
						</li>

						<li>
							<a data-toggle="tab" href="#hr2"> Lama </span></a>
						</li>

					</ul>

				</header>
				<!-- widget div-->
				<div>

					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->

					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body">

						<?php
							
							if($n_pengumuman>0){
						?>
						<input type="hidden" id="base_url" value="<?=base_url();?>"/>
						<div class="tab-content">
							<div class="tab-pane active" id="hr1">										
								<div id="content-hr1">

									<table class="table table-bordered">
										<tbody>
											<?php
											echo "
											<tr><td>Nama</td><td> : ".$pendaftaran_row['nama']."</td></tr>
											<tr><td>Sekolah Asal</td><td> : ".$pendaftaran_row['sekolah_asal']."</td></tr>
											<tr><td>Jalur Pendaftaran</td><td> : ".$pendaftaran_row['nama_jalur']."</td></tr>
											<tr>
												<td></td><td><a href='#selected-row1' class='btn btn-default btn-xs'>Menuju Calon Siswa</a></td>
											</tr>";
											?>
											
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="6"><?=$kuota_row1['nama_sekolah'];?>, Kuota : <?=number_format($kuota_row1['tot_kuota']);?> orang</th>
											</tr>
											<tr><th>No.</th><th>No. Peserta</th><th>Nama</th><th>Sekolah Asal</th><th>Skor</th><th>Rincian</th></tr>
										</thead>
										<tbody>
										<?php
											$i = 0;
											$line_printed = false;

											foreach($new_result_rows as $row){
												$i++;

												$selected_row = ($pendaftaran_row['id_pendaftaran']==$row['id_pendaftaran']?"class='selected'":"");
												$selected_id = ($pendaftaran_row['id_pendaftaran']==$row['id_pendaftaran']?"id='selected-row1'":"");

												if($i<=$kuota_row1['tot_kuota']){
													echo "<tr ".$selected_row." ".$selected_id.">
														<td align='center'>".$i."</td>
														<td>".$row['id_pendaftaran']."</td>
														<td>".$row['nama']."</td>
														<td>".$row['sekolah_asal']."</td>
														<td align='right'>".$row['score']."</td>
														<td align='center'>
															<button type='btn btn-default btn-xs' onclick=\"\" data-toggle='modal'><i class='fa fa-eye'></i></button>
														</td>
													</tr>";
												}else{

													if(!$line_printed){
										    			echo "<tr>
										    			<td colspan='6' align='center' style='background:#f22424;color:#fff'>Yang berada di bawah garis merah tidak lulus dalam seleksi jalur ini.</td>
										    			</tr>";
										    			$line_printed = true;
										    		}

										    		echo "<tr>
														<td align='center'>".$i."</td>
														<td>".$row['id_pendaftaran']."</td>
														<td>".$row['nama']."</td>
														<td>".$row['sekolah_asal']."</td>
														<td align='right'>".$row['score']."</td>
														<td align='center'>
															<button type='btn btn-default btn-xs' onclick=\"\" data-toggle='modal'><i class='fa fa-eye'></i></button>
														</td>
													</tr>";
												}
											}
										?>
										</tbody>
									</table>

								</div>
							</div>
							<div class="tab-pane" id="hr2">
								<div id="content-hr2">
									<table class="table table-bordered">
										<tbody>
											<?php
											echo "
											<tr><td>Nama</td><td> : ".$pendaftaran_row['nama']."</td></tr>
											<tr><td>Sekolah Asal</td><td> : ".$pendaftaran_row['sekolah_asal']."</td></tr>
											<tr>
												<td></td><td><a href='#selected-row1' class='btn btn-default btn-xs'>Menuju Calon Siswa</a></td>
											</tr>";
											?>
											
										</tbody>
									</table>

									<table class="table table-bordered">
										<thead>
											<tr>
												<th colspan="6"><?=$kuota_row2['nama_sekolah'];?>, Kuota : <?=number_format($kuota_row2['tot_kuota']);?> orang</th>
											</tr>
											<tr><th>No.</th><th>No. Peserta</th><th>Nama</th><th>Sekolah Asal</th><th>Skor</th><th>Rincian</th></tr>
										</thead>
										<tbody>
										<?php
											$i = 0;

											$line_printed = false;

											foreach($old_result_rows as $row){
												$i++;

												$selected_row = ($pendaftaran_row['id_pendaftaran']==$row['id_pendaftaran']?"class='selected'":"");
												$selected_id = ($pendaftaran_row['id_pendaftaran']==$row['id_pendaftaran']?"id='selected-row2'":"");


												if($i<=$kuota_row2['tot_kuota']){
													echo "<tr ".$selected_row." ".$selected_id.">
														<td align='center'>".$i."</td>
														<td>".$row['id_pendaftaran']."</td>
														<td>".$row['nama']."</td>
														<td>".$row['sekolah_asal']."</td>
														<td align='right'>".$row['score']."</td>
														<td align='center'>
															<button type='btn btn-default btn-xs' onclick=\"\" data-toggle='modal'><i class='fa fa-eye'></i></button>
														</td>
													</tr>";
												}else{
													if(!$line_printed){
										    			echo "<tr>
										    			<td colspan='6' align='center' style='background:#f22424;color:#fff'>Yang berada di bawah garis merah tidak lulus dalam seleksi jalur ini.</td>
										    			</tr>";
										    			$line_printed = true;
										    		}

										    		echo "<tr>
														<td align='center'>".$i."</td>
														<td>".$row['id_pendaftaran']."</td>
														<td>".$row['nama']."</td>
														<td>".$row['sekolah_asal']."</td>
														<td align='right'>".$row['score']."</td>
														<td align='center'>
															<button type='btn btn-default btn-xs' onclick=\"\" data-toggle='modal'><i class='fa fa-eye'></i></button>
														</td>
													</tr>";
												}
											}
										?>
										</tbody>
									</table>

								</div>
							</div>
						</div>

						<?php 
						}else{
							echo "<div class='alert alert-warning'>Data tidak tersedia!</div>";
						} 
						?>

					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->

		</article>
		<!-- WIDGET END -->

	</div>

	<!-- end row -->

	<!-- end row -->

</section>
<!-- end widget grid -->
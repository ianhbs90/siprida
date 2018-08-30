<!-- NEW WIDGET START -->
<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	
	<div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-3" data-widget-editbutton="false">
		<header>
			<span class="widget-icon"> <i class="fa fa-table"></i> </span>
			<h2>Daftar Lulusan</h2>
		</header>

		<!-- widget div-->
		<div>

			<!-- widget edit box -->
			<div class="jarviswidget-editbox">
				<!-- This area used as dropdown edit box -->
			</div>
			<!-- end widget edit box -->

			<!-- widget content -->
			<div class="widget-body no-padding" id="list_of_data">				
				<?php

					echo "
					<table id='data-table-jq' class='table table-striped table-bordered table-hover' width='100%'>
						<thead>
							<tr>";
								if($mode=='2'){
									echo "<td rowspan='2'>Nama Sekolah</td>";
								}else if($mode=='3'){
									echo "<td rowspan='2'>Jenjang Sekolah</td>";
								}
								echo "<td colspan='5' align='center'><b>Pendaftar</b></td>
								<td colspan='5' align='center'><b>Lulusan</b></td>
							</tr>
							<tr>";

							foreach($jalur_rows as $row){
								echo "<td align='center'><b>".$row['nama_jalur']."</b></td>";
							}

							foreach($jalur_rows as $row){
								echo "<td align='center'><b>".$row['nama_jalur']."</b></td>";
							}

							echo "</tr>
						</thead>
						<tbody>";

							if($mode=='3'){
								
								foreach($jenjang_rows as $row1){
									echo "<tr>
									<td>".$row1['nama_tipe_sekolah']."</td>";
									
									foreach($jalur_rows as $row2){
										echo "<td>".$registrants[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']]."</td>";
									}

									foreach($jalur_rows as $row2){
										echo "<td>".$graduates[$row1['ref_tipe_sklh_id']][$row2['ref_jalur_id']]."</td>";
									}

									echo "</tr>";
								}


							}

							
						echo "</tbody>
					</table>";
				?>
			</div>
			<!-- end widget content -->
		</div>
		<!-- end widget div -->

	</div>
	<!-- end widget -->

</article>
<!-- WIDGET END -->	

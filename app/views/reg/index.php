<link rel="stylesheet" href="<?=$this->config->item('css_path');?>sidebar-style.css"/>

<div class="sidebar-content">
	<div class="row">
	 <div class="col-sm-4 toggle-menu-right">
	  <button type="button" class="btn btn-sm" id="toggle-menu"><i class="fa fa-bars"></i></button>	
	 </div>	
	</div>
	<div class="wrapper">
	    <!-- Sidebar Holder -->
	    <nav id="sidebar">
	        <div class="sidebar-header">
	            <select class="form-control" onchange="location.href='<?php echo base_url()."Reg/stage/";?>'+$(this).val();">
	            	<?php
	            		foreach($tipe_sekolah_rows as $row){
	            			$selected = ($stage==$row['ref_tipe_sklh_id']?'selected':'');
	            			echo "<option value='".$row['ref_tipe_sklh_id']."' ".$selected.">".$row['nama_tipe_sekolah']."</option>";
	            		}
	            	?>
	            </select>
	        </div>

	        <ul class="list-unstyled components">	            
	            <?php
	            	$active = ($path==''?"class='active'":"");
	            	echo "
	            	<li ".$active.">
		                <a href='".base_url()."Reg/stage/".$stage."'>
		                    <i class='fa fa-home'></i>
		                    Info Umum
		                </a>
		            </li>";

	            	foreach($jalur_pendaftaran_rows as $row){
	            		$active = ($path==$row['ref_jalur_id']?"class='active'":"");
	            		echo "
	            			<li ".$active.">
				                <a href='".base_url()."Reg/stage/".$stage."/".$row['ref_jalur_id']."'>
				                    <i class='fa fa-check'></i>".$row['nama_jalur']."
				                </a>
			            	</li>";
	            	}
	            ?>
	        </ul>

	    </nav>

	    <!-- Page Content Holder -->
	    <div id="content" style="width:100%">

	    	<?php
	    	if($path!='')
	    	{
	    	echo "
	        <nav>
	            <div class='container-fluid'>
	                <div class='navbar-header'>
	                	<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#bs-example-navbar-collapse-1'>
		    				<span class='icon-bar'></span>
		    				<span class='icon-bar'></span>
		     				<span class='icon-bar'></span>
		    			</button>
	                </div>
	                <div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>
	                    <ul class='nav navbar-nav navbar-left' id='tab-menu'>";
	                        
	                        /*echo "<li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this);\" id='tab-menu1'>
	                        		<input type='hidden' id='ajax-req-dt' name='tab_id' value='0'/>
	                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
	                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        		<i class='fa fa-info-circle'></i> 
	                        		<b>Panduan</b></a>
	                        </li>";*/

	                        echo "<li>
	                        	<a href='javascript:;' class='active' onclick=\"tabMenu_navigation(this,'Aturan');\" id='tab-menu2'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='1'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-book'></i> <b>Aturan</b></a>
	                        </li>
	                        <li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this,'Jadwal');\" id='tab-menu3'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='2'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-calendar'></i> <b>Jadwal</b></a>
	                        </li>
	                        
	                        <!--li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this,'Prosedur');\" id='tab-menu4'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='3'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-check-circle'></i> <b>Prosedur</b></a>
	                        </li-->";

	                        echo "<li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this,'Daftar');\" id='tab-menu5'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='4'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-pencil-square-o'></i> <b>Daftar</b></a>
	                        </li>";

	                      
	                        echo "<li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this,'Hasil');\" id='tab-menu6'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='5'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-file-text-o'></i> <b>Hasil Sementara</b></a>
	                        </li>";
	                    	

	                        echo "<li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this,'Statistik');\" id='tab-menu7'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='6'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-bar-chart-o'></i> <b>Statistik</b></a>
	                        </li>";

	                        /*echo "<li>
	                        	<a href='javascript:;' onclick=\"tabMenu_navigation(this,'Kuota');\" id='tab-menu8'>
	                        	<input type='hidden' id='ajax-req-dt' name='tab_id' value='7'/>
                        		<input type='hidden' id='ajax-req-dt' name='stage' value='".$stage."'/>
                        		<input type='hidden' id='ajax-req-dt' name='path' value='".$path."'/>
	                        	<i class='fa fa-users'></i> <b>Kuota</b></a>
	                        </li>"; */
	                    
	                    echo "</ul>
	                </div>
	            </div>
	        </nav>";
	        } ?>
           
	        <div id="data-view-loader" align="center" style="display:none">
	        	<img src="<?=$this->config->item('img_path');?>ajax-loaders/ajax-loader-7.gif"/><br />
	        </div>
	        <?php if (!empty($path)) { ?>
	        <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" onclick="tabMenu_navigation(this,'Aturan');" id='tab-menu2' data-parent=".tab-pane" href="javascript:void(0)">
		          Aturan
		        </a>
		      </h4>
		    </div>
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" onclick="tabMenu_navigation(this,'Jadwal');" id='tab-menu3' data-parent=".tab-pane" href="javascript:void(0)">
		         Jadwal
		        </a>
		      </h4>
		    </div>
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" onclick="tabMenu_navigation(this,'Daftar');" id='tab-menu5' data-parent=".tab-pane" href="javascript:void(0)">
		         Daftar 
		        </a>
		      </h4>
		    </div>
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" onclick="tabMenu_navigation(this,'Daftar');" id='tab-menu6' data-parent=".tab-pane" href="javascript:void(0)">
		         Hasil
		        </a>
		      </h4>
		    </div>
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" onclick="tabMenu_navigation(this,'Statistik');" id='tab-menu7' data-parent=".tab-pane" href="javascript:void(0)">
		         Statistik 
		        </a>
		      </h4>
		    </div>
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" onclick="tabMenu_navigation(this,'Kuota');" id='tab-menu8' data-parent=".tab-pane" href="javascript:void(0)">
		         Kuota 
		        </a>
		      </h4>
		    </div>
			<?php } ?>
	        <div id="data-view">	
	        	<?php 
	        		$this->load->view($active_controller.'/'.$tab_view);
	        	?>
		    </div>
			
	    </div>
	</div>
</div>

<script src="<?=$this->config->item('js_path');?>my_scripts/ajax_object.js"></script>

<script type="text/javascript">
     $(document).ready(function () {
         $('#sidebarCollapse').on('click', function () {
             $('#sidebar').toggleClass('active');
         });
     });
     $('#toggle-menu').on('click', function() {
     	$('#sidebar').slideToggle(200);	
     });
     function tabMenu_navigation(obj,active_breadrumb){

     	ajax_object.
     	reset_object();
        ajax_object.set_url($('#baseUrl_node').val()+'reg/content_tab_menu')
        	.set_id_input(obj.id)
        	.set_input_ajax('ajax-req-dt')
        	.set_data_ajax()
        	.set_loading('#data-view-loader')
        	.set_content('#data-view')
        	.request_ajax();

        $('#tab-menu > li a').removeClass('active');

        $(obj).addClass('active');

        $('#active_item_breadcrumb').html(active_breadrumb);
       
     }

 </script>

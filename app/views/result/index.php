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

	        <ul class="list-unstyled components">	            
	            <?php
	            	$active = ($submenu=='selection'?"class='active'":"");
	            	echo "
	            	<li ".$active.">
		                <a href='".base_url()."result/selection'>
		                    <i class='fa fa-check'></i>
		                    Hasil Seleksi
		                </a>
		            </li>";
		            // $active = ($submenu=='statistic'?"class='active'":"");
		            // echo "<li ".$active.">
		            //     <a href='".base_url()."result/statistic'>
		            //         <i class='fa fa-bar-chart'></i>
		            //         Statistik
		            //     </a>
		            // </li>";
	            ?>
	        </ul>

	    </nav>

	    <!-- Page Content Holder -->
	    <div id="content" style="width:100%">
	        <?php echo $subcontent;?>			
	    </div>
	</div>
</div>



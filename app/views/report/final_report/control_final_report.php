
<script type="text/javascript">
	base_url = "<?php echo base_url(); ?>";
	format = "<?php echo $format; ?>";
	dt2_id = "<?php echo $dt2_id; ?>";
	jenjang = "<?php echo $jenjang; ?>";
	mode = "<?php echo $mode; ?>";

	funct = (format=='html'?'final_report_print':'final_report_excel');

	params = mode;
	if(mode=='1'){
		params += '/'+jenjang+'/'+dt2_id;
	}

	url = base_url+'report/'+funct+'/'+params;
	var win = window.open(url, '_blank');
  	win.focus();
</script>
<?php 
	$autoSearchOptions = "width: 278, delimiter: /(,|;)\s*/, deferRequestBy: 200, noCache: true, minChars: 3,onSelect : function(event, ui) {filterSelectEvent();}";
?>
<!-- Load c3.css -->
			<link href="<?php echo url(); ?>/themes/admin/css/nouislider.css" rel="stylesheet" type="text/css">  
			<!-- Load d3.js and c3.js -->
			<script src="<?php echo url(); ?>/js/nouislider.js" charset="utf-8"></script>
			<script src="<?php echo url(); ?>/js/wNumb.js" charset="utf-8"></script>
<style>
	#dateRange li{
		display: inline;
		float: left;
	}
	#dateRange li input[type='text']{
		width: 140px;
		float: left;
   	 	margin-right: 5%;
	}
	.left{
	margin-left:10px;
	}
	.filtersection > LABEL {
    padding-top: 10px;
    border-top: 1px solid #bbbbbb;
    border-bottom:0px;
    display: block;
    color: black;
	}
	.filtersection-top{
		border-top: 1px solid #bbbbbb;
		margin-top: 10px;
    	margin-bottom: 10px;
	}
	.last{
	height:200px;
	}
	.autocomplete{
    	margin-top: 3px;
    	border:1px solid #ccc;
	}
	.dataSet label{
	margin-bottom: 0px !important;
    padding-bottom: 5px;
	}
</style>
<script>
$(document).ready(function(){
	var slider = document.getElementById('slider');	
	noUiSlider.create(slider, {
		start: [<?php echo $min_quantity; ?>, <?php echo $max_quantity; ?>],
		tooltips: true,
		connect: true,
		range: {
			'min': <?php echo $min_quantity; ?>,
			'max': <?php echo $max_quantity; ?>
		},
		format: wNumb({
			decimals: 0
		})
	});
	
	$('#fromDate').datepicker({
	});

	$('#toDate').datepicker({
	});

	// Autocomplet Options for the 'POSTO Number' field 
	var postoNumberAutoCompleteOptions = {
	        serviceUrl: '<?php echo url(); ?>/bcp/get_posto_numbers',
	        <?php echo $autoSearchOptions; ?>,
	        onSelect: function (event, ui) {
	        	var selText = $(event).children('.idfield').html();
				var id = $(event).children('.idfield').attr('name');
				selText=selText.replace(/\&amp;/g,'&');
				if(event.length>20){
					if(event.substring(0,21)=="No results found for "){
						return false;
					}else{
						$('#posto_num').val(selText);
					}
				}else{
					$('#posto_num').val(selText);
				}
	                        }
	};
	// Trigger the Autocompleter for 'POSTO Number' field
	a = $('#posto_num').autocomplete(postoNumberAutoCompleteOptions);
	
	// Autocomplet Options for the 'SSCC Number' field 
	var ssccNumberAutoCompleteOptions = {
			serviceUrl: '<?php echo url(); ?>/bcp/get_sscc_numbers',
	        <?php echo $autoSearchOptions; ?>,
        	onSelect: function (event, ui) {
    	        	var selText = $(event).children('.idfield').html();
    				var id = $(event).children('.idfield').attr('name');
    				selText=selText.replace(/\&amp;/g,'&');
    				if(event.length>20){
    					if(event.substring(0,21)=="No results found for "){
    						return false;
    					}else{
    						$('#sscc_num').val(selText);
    					}
    				}else{
    					$('#sscc_num').val(selText);
    				}
    	  }
	};
	// Trigger the Autocompleter for 'POSTO Number' field
	a = $('#sscc_num').autocomplete(ssccNumberAutoCompleteOptions);
	
	// Autocomplet Options for the 'Material Number/UPC' field 
	var materialNumberAutoCompleteOptions = {
			serviceUrl: '<?php echo url(); ?>/bcp/get_material_numbers',
	        <?php echo $autoSearchOptions; ?>,
        	onSelect: function (event, ui) {
    	        	var selText = $(event).children('.idfield').html();
    				var id = $(event).children('.idfield').attr('name');
    				selText=selText.replace(/\&amp;/g,'&');
    				if(event.length>20){
    					if(event.substring(0,21)=="No results found for "){
    						return false;
    					}else{
    						$('#material_num').val(selText);
    					}
    				}else{
    					$('#material_num').val(selText);
    				}
    	  }
	};
	// Trigger the Autocompleter for 'POSTO Number' field
	a = $('#material_num').autocomplete(materialNumberAutoCompleteOptions);
	
	// Autocomplet Options for the 'Putaway Location' field 
	var putawayLocationAutoCompleteOptions = {
			serviceUrl: '<?php echo url(); ?>/bcp/get_putaway_location',
	        <?php echo $autoSearchOptions; ?>,
        	onSelect: function (event, ui) {
    	        	var selText = $(event).children('.idfield').html();
    				var id = $(event).children('.idfield').attr('name');
    				selText=selText.replace(/\&amp;/g,'&');
    				if(event.length>20){
    					if(event.substring(0,21)=="No results found for "){
    						return false;
    					}else{
    						$('#putaway_loc').val(selText);
    					}
    				}else{
    					$('#putaway_loc').val(selText);
    				}
    	  }
	};
	// Trigger the Autocompleter for 'POSTO Number' field
	a = $('#putaway_loc').autocomplete(putawayLocationAutoCompleteOptions);
});

</script>
<form id="filterForm" method="post">
<hr class="filtersection-top"/>
	<div id="" class="filtersection text-center">
			<button type="button" class="btn btn-primary" onclick="filterSelectEvent();">Apply</button>
			<button class="btn btn-primary " onclick="resetFilters(); return false;">Reset</button>
	</div>
	<div id="" class="filtersection">
			<label>Quntity</label>
			<div class="form-group col-md-12" style="margin-top: 10px;">
			<div id="slider"></div>
			</div>
		</div>
	<div id="dateRange" class="filtersection">
		<label>Date Range</label>
		<ul>
			<li>
				<label for="fromDate">From</label>
				<div class="form-group">
					<input id="fromDate" type="text" name="from_date" class="form-control" placeholder="mm/dd/yyyy" value="<?php if(isset($fromDate) && $fromDate !='') echo $fromDate; ?>">
				</div>
			</li>
			<li>
				<label for="toDate">To</label>
				<div class="form-group left">
					<input id="toDate" type="text" name="to_date" class="form-control" placeholder="mm/dd/yyyy" value="<?php if(isset($toDate) && $toDate !='') echo $toDate; ?>">
					<!-- <button style="position: absolute;" onclick="filterSelectEvent(); return false;">Done</button> -->
				</div>
			</li>
		</ul>
	</div>
	<div id="" class="filtersection">
		<label>POSTO Number</label>
		<ul>
		<?php if(isset($postoSelected)){ foreach($postoSelected as $postoNumber){ ?>
				<li class="">
					<input type="checkbox" name="posto_previous[]" class="autocompleteInputBox" id="posto_pre" value="<?php echo $postoNumber; ?>" checked='checked'>
					<label for=""><?php echo $postoNumber; ?></label>
				</li>
		<?php } } ?>		
		</ul>
		<div class="form-group">
			<input id="posto_num" type="text" name="posto_number" value="" class="form-control" placeholder="Please Enter Posto Number">
		</div>
	</div>
	<div id="" class="filtersection">
		<label>SSCC Number</label>
		<ul>
		<?php if(isset($ssccSelected)){ foreach($ssccSelected as $ssccNumber){ ?>
				<li class="">
					<input type="checkbox" name="sscc_previous[]" class="" id="sscc_pre" value="<?php echo $ssccNumber; ?>" checked='checked'>
					<label for=""><?php echo $ssccNumber; ?></label>
				</li>
		<?php } } ?>		
		</ul>
		<div class="form-group">
			<input id="sscc_num" type="text" name="sscc_number" value="" class="form-control autocompleteInputBox" placeholder="Please Enter SSCC Number">
		</div>
	</div>
	<div id="" class="filtersection">
		<label>Material Number/UPC</label>
		<ul>
		<?php if(isset($materialSelected)){ foreach($materialSelected as $matNumber){ ?>
				<li class="">
					<input type="checkbox" name="material_previous[]" class="" id="material_previous" value="<?php echo $matNumber; ?>" checked='checked'>
					<label for=""><?php echo $matNumber; ?></label>
				</li>
		<?php } } ?>		
		</ul>
		<div class="form-group">
			<input id="material_num" type="text" name="material_number" value="" class="form-control" placeholder="Please Enter Material/UPC Number">
		</div>
	</div>
	<div id="" class="filtersection last">
		<label>Putaway Location</label>
		<ul>
		<?php if(isset($locationSelected)){ foreach($locationSelected as $locNumber){ ?>
				<li class="">
					<input type="checkbox" name="putaway_previous[]" class="" id="putaway_previous" value="<?php echo $locNumber; ?>" checked='checked'>
					<label for=""><?php echo $locNumber; ?></label>
				</li>
		<?php } } ?>		
		</ul>
		<div class="form-group">
			<input id="putaway_loc" type="text" name="putaway_location" value="" class="form-control" placeholder="Please Enter Putaway location Number">
		</div>
	</div>
</form>
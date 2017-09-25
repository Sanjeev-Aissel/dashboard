<?php 
	$autoSearchOptions = "width: 278, delimiter: /(,|;)\s*/, deferRequestBy: 200, noCache: true, minChars: 3,onSelect : function(event, ui) {filterSelectEvent();}";
?>
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
</style>
<script>
$(document).ready(function(){
	$('#fromDate').datepicker({
	});

	$('#toDate').datepicker({
	});
});

</script>
<form id="filterForm" action="#">
<hr class="filtersection-top"/>
	<div id="" class="filtersection text-center">
			<button class="btn btn-primary ">Apply</button>
			<button class="btn btn-primary ">Reset</button>
			<!-- a id="resetbutton" type="button" class="btn-xs" href="#" onclick="resetFilters(); return false;"><i class="icon-refresh"></i> Reset</a -->
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
		<label>Company</label>
		<input type="hidden" name="" value=""/>
		<div class="form-group">
			<input id="" type="text" name="" class="form-control" placeholder="">
		</div>
	</div>
	<div id="" class="filtersection">
		<label>Plants</label>
		<input type="hidden" name="" value=""/>
		<div class="form-group">
			<input id="" type="text" name="" class="form-control" placeholder="">
		</div>
	</div>
	<div id="" class="filtersection">
		<label>LPNS</label>
		<input type="hidden" name="" value=""/>
		<div class="form-group">
			<input id="" type="text" name="" class="form-control" placeholder="">
		</div>
	</div>
	<div id="" class="filtersection">
		<label>Material</label>
		<input type="hidden" name="" value=""/>
		<div class="form-group">
			<input id="" type="text" name="" class="form-control" placeholder="">
		</div>
	</div>
	<div id="" class="filtersection last">
		<label>Production Number</label>
		<input type="hidden" name="" value=""/>
		<div class="form-group">
			<input id="" type="text" name="" class="form-control" placeholder="">
		</div>
	</div>
</form>
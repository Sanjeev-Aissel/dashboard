@extends(Config::get('constants.admintheme'))
		
		@section('content')
		<?php 
			$autoSearchOptions = "width: 278, delimiter: /(,|;)\s*/, deferRequestBy: 200, noCache: true, minChars: 3,onSelect : function(event, ui) {filterSelectEvent();}";
		?>
		<link href="<?php echo url(); ?>/themes/admin/css/toggle.css" rel="stylesheet">
		<style>
		.pointer{
			cursor: pointer;
			color:#292998;
		}
		.top-input{
		display: inline;
		width: auto;
		vertical-align: middle;
		}
		.removeBtn{
		vertical-align: middle !important;
		}
		.pull-right{
		text-align: right;
		}
		/* .inputBox td{
		padding:2px !important;
		} */
		.mat_upc{
		 background-color: rgba(245, 79, 0, 0.38);
		}
		fieldset.bcp-border {
		    /* border: 1px groove #ddd !important; */
		    padding: 0 1.4em 1.4em 1.4em !important;
		    margin: 0 0 1.5em 0 !important;
		    -webkit-box-shadow:  0px 0px 0px 0px #000;
		            box-shadow:  0px 0px 0px 0px #000;
		}
		
		    legend.bcp-border {
		        font-size: 1.2em !important;
		        font-weight: bold !important;
		        text-align: left !important;
		        width:auto;
		        padding:0 10px;
		        border-bottom:none;
		    }
		    label.error {
			/* position: absolute; */
		    top: 35px;
		    bottom: 0;
		    left: 5px;
		    right: 0;
		    font-size: 10px;
		    color: #bd0000;
		    float: none !important; 
		 }
		 /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 30px;
			  height: 15px;
			  float: right;
			}
			
			/* Hide default HTML checkbox */
			.switch input {display:none;}
			
			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 5px;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			}
			
			.slider:before {
			  position: absolute;
			  content: "";
			  height: 10px;
			  width: 15px;
			  left: 0px;
			  bottom: 0px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}
			
			input:checked + .slider {
			  background-color: #FF6600;
			}
			
			input:focus + .slider {
			  box-shadow: 0 0 1px #FF6600;
			}
			
			input:checked + .slider:before {
			  -webkit-transform: translateX(15px);
			  -ms-transform: translateX(15px);
			  transform: translateX(15px);
			}
			
			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}
			
			.slider.round:before {
			  border-radius: 50%;
			}
			.orange{
			background:#ffe7ba;
			}
			.message{
			display:none;
			}
			.addBtn {
		    vertical-align: inherit !important; 
			}
			
		</style>
		<script>
		var baseUrl = "<?php echo url(); ?>";
		$(document).on('change', '#toggle', function(){
			var checked = this.checked ? this.value : '0';
			if(checked == 1){
					document.getElementById("matUPCNumber").placeholder = "Enter UPC Number";
					$("#matUPCNumber").addClass("orange");
				}else{
					document.getElementById("matUPCNumber").placeholder = "Enter Material Number";
					$("#matUPCNumber").removeClass("orange");
					}
			});
		$(document).on('change', '#dateToggle', function(){
			var checked = this.checked ? this.value : '0';
			if(checked == 1){
					document.getElementById("probBbeDate").placeholder = "Enter BBE Date";
					$("#probBbeDate").addClass("orange");
				}else{
					document.getElementById("probBbeDate").placeholder = "Enter Production Date";
					$("#probBbeDate").removeClass("orange");
					}
			});
		
		function addInputRow(){
				var no_of_filed = $('#noOfFields').val();
				if(no_of_filed < 5){
						no_of_filed = parseInt(no_of_filed)+1;
						$('#noOfFields').val(no_of_filed);
						var wrapperData = $('#bcpTableData .rowData:last');
						var clonedRow = wrapperData;
						 
                        $('#bcpTableData').append("<tr class='rowData'>"+ (clonedRow).html()+"</tr>");
						$(wrapperData).find('.addBtn').html('<button type="button" class="btn btn-danger btn-xs" data-title="Delete" onclick="deleteRow(this)"><span class="glyphicon glyphicon-remove-circle"></span></button>');
				        
						var ssccNumber = "ssccNumber"+no_of_filed;
						var sscc_number = "sscc_number"+no_of_filed;
						$('#bcpTableData tr:last td:eq(0) input:last').val('');
						$('#bcpTableData tr:last td:eq(0) input:last').attr('name',sscc_number);
						$('#bcpTableData tr:last td:eq(0) input:last').attr('id',ssccNumber);
						$('#bcpTableData tr:last td:eq(0) input:last').addClass('ssccNumber form-control');
						
						var lastMatUPC = "matUPCNumber"+no_of_filed;
						var mat_upc_number = "mat_upc_number"+no_of_filed;
						$('#bcpTableData tr:last td:eq(1) input:last').val('');
						$('#bcpTableData tr:last td:eq(1) input:last').attr('name',mat_upc_number);
						$('#bcpTableData tr:last td:eq(1) input:last').attr('id',lastMatUPC);
						$('#bcpTableData tr:last td:eq(1) input:last').addClass('matUPCNumber form-control');

						var quantity = "quantity"+no_of_filed;
						$('#bcpTableData tr:last td:eq(2) input:last').val('');
						$('#bcpTableData tr:last td:eq(2) input:last').attr('name',quantity);
						$('#bcpTableData tr:last td:eq(2) input:last').attr('id',quantity);
						$('#bcpTableData tr:last td:eq(2) input:last').addClass('quantity form-control');

						var batch = "batch"+no_of_filed;
						$('#bcpTableData tr:last td:eq(3) input:last').val('');
						$('#bcpTableData tr:last td:eq(3) input:last').attr('name',batch);
						$('#bcpTableData tr:last td:eq(3) input:last').attr('id',batch);
						$('#bcpTableData tr:last td:eq(3) input:last').addClass('batch form-control');

						var probBbeDate = "probBbeDate"+no_of_filed;
						var prob_bbe_date = "prob_bbe_date"+no_of_filed;
						$('#bcpTableData tr:last td:eq(4) input:last').val('');
						$('#bcpTableData tr:last td:eq(4) input:last').attr('name',prob_bbe_date);
						$('#bcpTableData tr:last td:eq(4) input:last').attr('id',probBbeDate);
						$('#bcpTableData tr:last td:eq(4) input:last').addClass('probBbeDate form-control');
						$('#bcpTableData tr:last td:eq(4) input:last').removeClass('hasDatepicker');

						var destBin = "destBin"+no_of_filed;
						$('#bcpTableData tr:last td:eq(5) input:last').val('');
						$('#bcpTableData tr:last td:eq(5) input:last').attr('name',destBin);
						$('#bcpTableData tr:last td:eq(5) input:last').attr('id',destBin);
						$('#bcpTableData tr:last td:eq(5) input:last').addClass('destBin form-control');
						
						$('#bcpTableData tr:last td:last').html('<button type="button" class="btn btn-success btn-xs" data-title="Add" onclick="addInputRow(this)"><span class="glyphicon glyphicon-plus-sign"></span></button>');

						// Autocomplet Options for the 'SSCC Number' field 
						var ssccNumberAutoCompleteOptions = {
								serviceUrl: '<?php echo url(); ?>/bcp/get_sscc_numbers_listing',
						        <?php echo $autoSearchOptions; ?>,
					        	onSelect: function (event, ui) {
					    	        	var selText = $(event).children('.idfield').html();
					    				var id = $(event).children('.idfield').attr('name');
					    				selText=selText.replace(/\&amp;/g,'&');
					    				if(event.length>20){
					    					if(event.substring(0,21)=="No results found for "){
					    						return false;
					    					}else{
					    						$('#ssccNumber'+no_of_filed).val(selText);
					    						getLpnAvailabilty(selText, no_of_filed);
					    					}
					    				}else{
					    					$('#ssccNumber'+no_of_filed).val(selText);
					    					getLpnAvailabilty(selText, no_of_filed)
					    				}
					    	  }
						};
						// Trigger the Autocompleter for 'POSTO Number' field
						a = $('#ssccNumber'+no_of_filed).autocomplete(ssccNumberAutoCompleteOptions);
					}
				}
		function deleteRow(thisEle){
			var no_of_filed = $('#noOfFields').val();
			if(no_of_filed > 1){
			no_of_filed = parseInt(no_of_filed)-1;
			$('#noOfFields').val(no_of_filed);
			$(thisEle).parent().parent().remove();
			}
		 }
		
		var checkContents = function(input) {
		    var text = input.value;
		    if(!/[a-zA-Z]/.test(text))
		         input.value = ""; 
		   }
		var checkNumeric = function(input, type) {
			var content = '<label for="poStoNumber" generated="true" class="instruction">PO/STO should start with 45/47</label>';
			var text = input.value;
			if(type == true){
				if(text.substring(0, 1) == '4'){
					if(text.length > 1){ 
					var x = text.substring(0, 2);
						 if(x != '45' && x != '47'){
							 input.value = "";
							 }
					}
				 }else{
					console.log(input.closest('td'));
					 input.value = "";
					 
				}
			}else{
				if(text.substring(0, 1) == '0'){
					if(text.length > 1){ 
					var x = text.substring(0, 2);
						 if(x != '00'){
							 input.value = "";
							 }
					}
				 }else{
					 input.value = "";
				}

				}
			}
		$('body').on('focus',".proBbeDate", function(){
		    $(this).datepicker();
		});
		$(document).ready(function(){
			// Autocomplet Options for the 'SSCC Number' field 
			var ssccNumberAutoCompleteOptions = {
					serviceUrl: '<?php echo url(); ?>/bcp/get_sscc_numbers_listing',
			        <?php echo $autoSearchOptions; ?>,
		        	onSelect: function (event, ui) {
		    	        	var selText = $(event).children('.idfield').html();
		    				var id = $(event).children('.idfield').attr('name');
		    				selText=selText.replace(/\&amp;/g,'&');
		    				if(event.length>20){
		    					if(event.substring(0,21)=="No results found for "){
		    						return false;
		    					}else{
		    						$('#ssccNumber').val(selText);
		    						getLpnAvailabilty(selText);
		    					}
		    				}else{
		    					$('#ssccNumber').val(selText);
		    					getLpnAvailabilty(selText)
		    				}
		    	  }
			};
			// Trigger the Autocompleter for 'POSTO Number' field
			a = $('#ssccNumber').autocomplete(ssccNumberAutoCompleteOptions);
			
			$('#bcpForm').validate({
					rules:{
						po_sto_number: {
							required:true,
							number:true,
							maxlength:10
							},			
						sscc_number: {
							required:true,
							number:true,
							maxlength:20
							},
						mat_upc_number: {
							required:true,
							maxlength:12
							},
						quantity: {
							required:true,
							number:true,
							maxlength:10
							},	
						prob_bbe_date: {
							required:true
							},
						destBin: {
							required:true,
							maxlength:10
							},
						sscc_number2: {
							required:true,
							number:true,
							maxlength:20
							},
						mat_upc_number2: {
							required:true,
							maxlength:12
							},
						quantity2: {
							required:true,
							number:true,
							maxlength:10
							},	
						prob_bbe_date2: {
							required:true
							},
						destBin2: {
							required:true,
							maxlength:10
							},
						sscc_number3: {
							required:true,
							number:true,
							maxlength:20
							},
						mat_upc_number3: {
							required:true,
							maxlength:12
							},
						quantity3: {
							required:true,
							number:true,
							maxlength:10
							},	
						prob_bbe_date3: {
							required:true
							},
						destBin3: {
							required:true,
							maxlength:10
							},
						sscc_number4: {
							required:true,
							number:true,
							maxlength:20
							},
						mat_upc_number4: {
							required:true,
							maxlength:12
							},
						quantity4: {
							required:true,
							number:true,
							maxlength:10
							},	
						prob_bbe_date4: {
							required:true
							},
						destBin4: {
							required:true,
							maxlength:10
							},
						sscc_number5: {
							required:true,
							number:true,
							maxlength:20
							},
						mat_upc_number5: {
							required:true,
							maxlength:12
							},
						quantity5: {
							required:true,
							number:true,
							maxlength:10
							},	
						prob_bbe_date5: {
							required:true
							},
						destBin5: {
							required:true,
							maxlength:10
							}		
						},
					messages:{
						po_sto_number: {
								required: "Please Enter PP/STO Number",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 10 Characters"
							},
						sscc_number: {
								required: "Please Enter SSCC Number",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 20 Characters"
							},
						mat_upc_number: {
								required: "Please Enter MAT/UPC Number",
								maxlength: "Maximum 12 Characters"
							},
						quantity: {
								required: "Please Enter Quantity",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 10 Characters"
							},	
						prob_bbe_date: {
								required: "Please Enter PRO/BBE Date"
							},
						destBin: {
								required: "Please Enter Putaway Location",
								maxlength: "Maximum 10 Characters"
							},
						sscc_number2: {
								required: "Please Enter SSCC Number",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 20 Characters"
							},
						mat_upc_number2: {
								required: "Please Enter MAT/UPC Number",
								maxlength: "Maximum 12 Characters"
							},
						quantity2: {
								required: "Please Enter Quantity",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 10 Characters"
							},	
						prob_bbe_date2: {
								required: "Please Enter PRO/BBE Date"
							},
						destBin2: {
								required: "Please Enter Putaway Location",
								maxlength: "Maximum 10 Characters"
							},
						sscc_number3: {
								required: "Please Enter SSCC Number",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 20 Characters"
							},
						mat_upc_number3: {
								required: "Please Enter MAT/UPC Number",
								maxlength: "Maximum 12 Characters"
							},
						quantity3: {
								required: "Please Enter Quantity",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 10 Characters"
							},	
						prob_bbe_date3: {
								required: "Please Enter PRO/BBE Date"
							},
						destBin3: {
								required: "Please Enter Putaway Location",
								maxlength: "Maximum 10 Characters"
							},
						sscc_number4: {
								required: "Please Enter SSCC Number",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 20 Characters"
							},
						mat_upc_number4: {
								required: "Please Enter MAT/UPC Number",
								maxlength: "Maximum 12 Characters"
							},
						quantity4: {
								required: "Please Enter Quantity",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 10 Characters"
							},	
						prob_bbe_date4: {
								required: "Please Enter PRO/BBE Date"
							},
						destBin4: {
								required: "Please Enter Putaway Location",
								maxlength: "Maximum 10 Characters"
							},	
						sscc_number5: {
								required: "Please Enter SSCC Number",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 20 Characters"
							},
						mat_upc_number5: {
								required: "Please Enter MAT/UPC Number",
								maxlength: "Maximum 12 Characters"
							},
						quantity5: {
								required: "Please Enter Quantity",
								number: "Only Numbers Allowed",
								maxlength: "Maximum 10 Characters"
							},	
						prob_bbe_date5: {
								required: "Please Enter PRO/BBE Date"
							},
						destBin5: {
								required: "Please Enter Putaway Location",
								maxlength: "Maximum 10 Characters"
							}	
						},
				    submitHandler: function (form) {
					            return false;
					        }	
						
				});
			$('#submitForm').on('click', function(){
		       // $('#bcpForm').validate()
		        if(!$('#bcpForm').validate().form()){
					return false;
				}else{
					var form = $('#bcpForm');
					var noOfFields = $("#noOfFields").val();
					$.ajax({
						url:baseUrl+'/bcp/set_posto_data',
						type:"Post",
						data: form.serialize(),
						dataType:'json',
						success:function(returndata){
								if(returndata['status'] == true){
										$("#alert-type").addClass("alert-success");
										$(".show-message").html(returndata['message']);
										$(".message").show();
										$("#matUPCNumber").val("");
										$("#quantity").val("");
										$("#batch").val("");
										$("#probBbeDate").val("");
										$("#destBin").val("");
										$("#ssccNumber").val("");
										if(noOfFields > 1){
												for(var i=2;i<=noOfFields;i++){
													$("#matUPCNumber"+i).val("");
													$("#quantity"+i).val("");
													$("#batch"+i).val("");
													$("#probBbeDate"+i).val("");
													$("#destBin"+i).val("");
													$("#ssccNumber"+i).val("");
													}
											}	
									}else{
										$("#alert-type").addClass("alert-danger");
										$(".show-message").html(returndata['message']);
										$(".message").show();
									}
							}
					});
				}
		    });	
		});	
		 
		function getLpnAvailabilty(lpnNumber, count=null){
			dataString = "lpn_number="+lpnNumber;
			$.ajax({
				type: "post",
				dataType:"json",
				data: dataString,
				url: baseUrl+'/bcp/check_lpn_availability',
				success: function(returnData){
					if(returnData['status']=='ava'){
							$("#ssccNumber"+count).val(returnData['lpn']);
							$("#matUPCNumber"+count).val(returnData['upc']);
							$("#quantity"+count).val(returnData['quantity']);
							$("#batch"+count).val(returnData['batch']);
							$("#probBbeDate"+count).val(returnData['pedate']);
							//$("#textbit").html(returnData['lpn']+" - Available.");
						}else{
							$("#textbit").html(returnData['lpn']+" - NA. <b class='pointer' onclick='selectLpn("+returnData['lpn']+");'>Select Anyways?</b>");
							}
				}/* ,
				complete: function(){
					//hide loading image
				} */
			});
			}		
		</script>
		<style>
		/* #inputDetails .barcode{
		    margin-left: 10%;
    		margin-top: 5px;
		} */
		label.instruction {
		color: #6f6b6b;
   		margin-left: 70px;
   		font-weight: 500;
   		font-size: 10px;
		}
		.scannerImg > input
			{
			    display: none;
			}
		.scannerImg{
		height: 0px;
		}	
		.barcode{
			    background: rgba(0, 0, 0, 0) url(http://localhost/dashboard/images/forbidden.png) no-repeat scroll 2px 2px / 25px auto;
			    float: left;
			    height: 25px;
			    margin-left: -6px;
    			margin-top: -6px;
			    position: absolute;
			    width: 25px;
			    -moz-transform: scaleX(-1);
		        -o-transform: scaleX(-1);
		        -webkit-transform: scaleX(-1);
		        transform: scaleX(-1);
		        filter: FlipH;
		        -ms-filter: "FlipH";
			}
		#barcode-Desk{
		display:none;
		}	
		.text-center{
		font-size: 11px;
		}
		.dataSet label {
		    margin-bottom: 0px !important;
		    padding-bottom: 5px;
		}
		</style>
		<br/>
		<fieldset class="bcp-border">
    		<legend class="bcp-border">Data Entry</legend>
		<form method="post" class="form-horizontal" id="bcpForm">
		<div class="row">
			<div class="col-md-6">
				<div class="col-md-8">
			      <label for="email2">PO / STO:</label>
			      <input type="text" class="form-control poStoNumber top-input" name="po_sto_number" id="poStoNumber" placeholder="Enter PO/STO Number" onkeyup="javascript:checkNumeric(this, true)" />
			    </div>
			</div>
			<div class="col-md-6">
				<div class="col-md-8 pull-right">
			      <label for="email2">BOL:</label>
			      <input type="text" class="form-control bol top-input" name="bol" id="bol" placeholder="Enter Bill of Lading Number">
			    </div>
			</div>	
		</div>
		<hr/>
		<table id="bcpTableData" class="table table-bordered table-striped">
		  <tr class="info">
		  <th>SSCC (LPN)</th>
		      <th>Mat / UPC <label class="switch">
						  <input type="checkbox" id="toggle" class="toggle" value="1" checked/>
						  <span class="slider"></span>
						</label></th>
		      <th>Qty</th>
		      <th>Batch</th>
		      <th>Prod / BBE Date <label class="switch">
						  <input type="checkbox" id="dateToggle" class="dateToggle" value="1"/>
						  <span class="slider"></span>
						</label></th>
		      <th>Dest Bin </th>
		      
		      <th></th>
		  </tr>
		  <input type="hidden" name="no_of_fileds" id="noOfFields" value="<?php echo 1; ?>"></input>
		  <tr id="inputDetails" class="default rowData">
		      <td><div class="scannerImg"><label for="Take-Picture"><div class="barcode"></div></label>
			        	  <input id="Take-Picture" class="barcode_import" type="file" accept="image/*;capture=camera" /></div>
			        	  <input type="text" class="form-control ssccNumber autocompleteInputBox" name="sscc_number" id="ssccNumber" placeholder="  Enter SSCC Number" onkeyup="javascript:checkNumeric(this, false)"/>
			        <div class="text-center"><span id="textbit" class="textbit"></span>
			     </div>
			  </td>
		      <td><input type="text" class="form-control matUPCNumber orange" name="mat_upc_number" id="matUPCNumber" placeholder="Enter UPC Number"/></td>
		      <td><input type="text" class="form-control quantity" name="quantity" id="quantity" placeholder="Enter Quantity"/></td>
		      <td><input type="text" class="form-control batch" name="batch" id="batch" placeholder="Enter Batch"/></td>
		      <td><input type="text" class="form-control proBbeDate" name="prob_bbe_date" id="probBbeDate" placeholder="Enter Production Date"/></td>
		      <td><input type="text" class="form-control destBin" name="destBin" id="destBin" placeholder="Enter Destination Bin Number" onkeyup="javascript:checkContents(this)"/></td>
		      
		      <td class="addBtn"><button type="button" class="btn btn-success btn-xs" data-title="Add" onclick="addInputRow(this)"><span class="glyphicon glyphicon-plus-sign"></span></button></td>
		  </tr>
		
		</table>
		<div class="col-md-12 pull-right">
			<button type="button" class="btn btn-primary submitForm" id="submitForm"><span class="glyphicon glyphicon-file"></span> Save</button>
			<button type="reset" class="btn btn-primary submitForm" id="submitForm"><span class="glyphicon glyphicon-ban-circle"></span> Reset</button>
		</div>
		</form>
		</fieldset>
		<div class="col-md-12 text-center message">
			<div id="alert-type" class="alert fade in alert-dismissable">
			    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
			    <span class="show-message"></span>
			</div>
		</div>
		<div id="barcode-Desk"><canvas width="320" height="200" id="picture"></canvas></div>
		<script type="text/javascript" src="<?php echo url(); ?>/js/barcode/JOB.js"></script>
	<script type="text/javascript">
			var takePicture = document.querySelector("#Take-Picture"),
			showPicture = document.createElement("img");
			Result = document.querySelector("#textbit");
			var canvas =document.getElementById("picture");
			var ctx = canvas.getContext("2d");
			JOB.Init();
			JOB.SetImageCallback(function(result) {
				if(result.length > 0){
					var tempArray = [];
					for(var i = 0; i < result.length; i++) {
						//tempArray.push(result[i].Format+" : "+result[i].Value);
						tempArray.push(result[i].Value);
					}
					Result.innerHTML=tempArray.join("<br />");
					getLpnAvailabilty(tempArray);
				}else{
					if(result.length === 0) {
						Result.innerHTML="Decoding failed.";
					}
				}
			});
			JOB.PostOrientation = true;
			JOB.OrientationCallback = function(result) {
				canvas.width = result.width;
				canvas.height = result.height;
				var data = ctx.getImageData(0,0,canvas.width,canvas.height);
				for(var i = 0; i < data.data.length; i++) {
					data.data[i] = result.data[i];
				}
				ctx.putImageData(data,0,0);
			};
			JOB.SwitchLocalizationFeedback(true);
			JOB.SetLocalizationCallback(function(result) {
				ctx.beginPath();
				ctx.lineWIdth = "2";
				ctx.strokeStyle="red";
				for(var i = 0; i < result.length; i++) {
					ctx.rect(result[i].x,result[i].y,result[i].width,result[i].height); 
				}
				ctx.stroke();
			});
			if(takePicture && showPicture) {
				takePicture.onchange = function (event) {
					var files = event.target.files;
					if (files && files.length > 0) {
						file = files[0];
						try {
							var URL = window.URL || window.webkitURL;
							showPicture.onload = function(event) {
								Result.innerHTML="";
								JOB.DecodeImage(showPicture);
								URL.revokeObjectURL(showPicture.src);
							};
							showPicture.src = URL.createObjectURL(file);
						}
						catch (e) {
							try {
								var fileReader = new FileReader();
								fileReader.onload = function (event) {
									showPicture.onload = function(event) {
										Result.innerHTML="";
										JOB.DecodeImage(showPicture);
									};
									showPicture.src = event.target.result;
								};
								fileReader.readAsDataURL(file);
							}
							catch (e) {
								Result.innerHTML = "Neither createObjectURL or FileReader are supported";
							}
						}
					}
				};
			}
			function selectLpn(lpnNumber){
					$("#ssccNumber").val(lpnNumber);
					$("#textbit").hide();
				}
		</script>
@stop		
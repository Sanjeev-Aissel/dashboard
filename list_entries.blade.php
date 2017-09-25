@extends(Config::get('constants.admintheme'))
		
		@section('content')
		<script type="text/javascript">
			var rangeType = '<?php echo (isset($range)) ? $range : '';?>';
			$(document).ready(function(){
				var mudelBoxOpts = {
						modal: true,
						autoOpen: false,
						width:900,
						position: ['center', 40],
						show: "scale",
						hide: "scale",
						//title:"Edit Posto Details"
						dialogClass: 'mudelView',
						open: function() {
							//display correct dialog content
						}
					};
					$('#modelBoxContenHolder').dialog(mudelBoxOpts);
					
				$("#filterSlide").click(function(){
					slideClass = $(this).attr('class');
					if(slideClass == 'expanded'){
						$(this).removeClass('expanded');
						$(this).addClass('collapsed');	

						$("#posto-listing").addClass('col-lg-12');
						$("#posto-listing").removeClass('col-lg-9');

						$("#filters").hide();
					}else{
						$(this).removeClass('collapsed');
						$(this).addClass('expanded');

						$("#posto-listing").addClass('col-lg-9');
						$("#posto-listing").removeClass('col-lg-12');

						$("#filters").show();
					}
					listPosts();
	      		});
	      		
			 	listPosts(); 
			});
			function listPosts(){
				var data = {};
				data['q1'] = $(".noUi-handle-lower").attr("aria-valuetext");
				data['q2'] = $(".noUi-handle-upper").attr("aria-valuetext");
				
				var sd = $('#fromDate').val();
				if(sd!=''){
					var sdSplits = sd.split("/");
						data['from_date'] = sdSplits[2]+"-"+sdSplits[0]+"-"+sdSplits[1];
					}
				var ed = $('#toDate').val();
				if(ed!=''){
					var edSplits = ed.split("/");
					data['to_date'] = edSplits[2]+"-"+edSplits[0]+"-"+edSplits[1];
				}
				var postoPrevious = new Array();
				$.each($(".filtersection input[name='posto_previous[]']:checked"), function() {
					postoPrevious.push($(this).val());
				});
				data['posto_previous'] = postoPrevious;
				if($('#posto_num').val()!='')
					data['posto_number'] = $('#posto_num').val();
				
				var ssccPrevious = new Array();
				$.each($(".filtersection input[name='sscc_previous[]']:checked"), function() {
					ssccPrevious.push($(this).val());
				});
				data['sscc_previous'] = ssccPrevious;
				if($('#sscc_num').val()!='')
					data['sscc_number'] = $('#sscc_num').val();
				
				var matPrevious = new Array();
				$.each($(".filtersection input[name='material_previous[]']:checked"), function() {
					matPrevious.push($(this).val());
				});
				data['material_previous'] = matPrevious;
				if($('#material_num').val()!='')
					data['material_number'] = $('#material_num').val();

				var putawayPrevious = new Array();
				$.each($(".filtersection input[name='putaway_previous[]']:checked"), function() {
					putawayPrevious.push($(this).val());
				});
				data['putaway_previous'] = putawayPrevious;
				if($('#putaway_loc').val()!='')
					data['putaway_location'] = $('#putaway_loc').val();
				
				$("#verifiedGridContainer").html("");
				// Append the required div and table
				$("#verifiedGridContainer").html('<table id="PostoResultSet"></table><div id="PostoPage"></div>');
				//var ele	= document.getElementById('gridContainer');
				//var gridWidth	= ele.clientWidth;
				var targetUrl = baseUrl+'bcp/get_listing';
				var gridWidth	= $('#verifiedGridContainer').width();
				
				jQuery("#PostoResultSet").jqGrid({
					url:targetUrl,
					datatype: "json",
					colNames: ['Id','PO/STO Number','SSCC Number','Material Number/UPC','UPC ?', 'BBE Date', 'Production date ?', 'Quantity','Putaway Location', 'Batch', 'BOL', 'Action'],
					colModel: [{name:'id',index:'id', hidden:true},
					           {name:'posto_number',index:'posto_number', width:'160', align:'center'},
								{name:'posto_sscc_lpn',index:'posto_sscc_lpn', align:'center'},
								{name:'posto_mu_number',index:'posto_mu_number', align:'center'},
								{name:'posto_mu_type',index:'posto_mu_type', align:'center'},
								{name:'posto_pe_date',index:'posto_pe_date', width:'160', align:'center'},
								{name:'posto_pe_type',index:'posto_pe_type', width:'160', align:'center'},
								{name:'posto_qty',index:'posto_qty', width:'160', align:'center'},
								{name:'posto_put_location',index:'posto_put_location', width:'160',align:'center'},
								{name:'posto_batch',index:'posto_batch', width:'160',align:'center'},
								{name:'posto_bol_number',index:'posto_bol_number', width:'120',align:'center'},
								{name:'status',index:'status', width:'60', align:'center', search:false}
								],
				    postData:data,
					
					autowidth: true,
					shrinkToFit: false, 
					loadonce:false,
					multiselect: false,
					ignoreCase:true,
					hiddengrid:false,
					height: "auto",
					toppager:false,
					pager: '#PostsContactPage',
				   	mtype: "POST",
					sortname: 'name',
					viewrecords: true,
					sortorder: "desc",
					jsonReader: { repeatitems : false, id: "0" },
					editurl:"#",
					rowList: [10,50,100,200],
					
					rownumbers: false,
					autowidth: true,
					shrinkToFit: false, 
					loadonce:false,
					multiselect: true,
					ignoreCase:true,
					hiddengrid:false,
					height: "auto",
				   	mtype: "POST",
					sortname: 'name',
					viewrecords: true,
					sortorder: "desc",
					jsonReader: { repeatitems : false, id: "0" },
					editurl:"#",
					rowList: [10,50,100,200],
					pager: '#PostoPage',
					gridComplete: function(){
						//Get array of id'f from jqgrid			   
				    	var arrIds = jQuery("#PostoResultSet").jqGrid('getDataIDs'); 
				    	// Loop trough each row(id) and prepare a microview, edit and delete links and update the perticular row
				    	for(var i=0;i < arrIds.length;i++){ 
				    		var arrId =  jQuery('#PostoResultSet').jqGrid ('getRowData',  arrIds[i]);
				    		var cl = arrIds[i];
				    		var editDeleteLink ='';
				    		var id = arrId.id;	 
					    	if(1 == 1){
					    			editDeleteLink += "<a class='editPosto' title='Edit Posto' href='#' onclick='showLpnDetails("+id+"); return false;'><i class='fa fa-edit'></i></a> <a class='deletePosto' title='Delete Posto' onclick='deletePosto("+id+");'><i class='fa fa-times-circle-o' aria-hidden='true'></i></a>";
					    		}
						    	
					    		jQuery("#PostoResultSet").jqGrid('setRowData',arrIds[i],{status:editDeleteLink});
					    	}
				    		//Microview label	
				    	initilizeGridSearchPlaceholder();
				    	}
				});
				jQuery("#PostoResultSet").jqGrid('navGrid','#PostoPage',{edit:false,add:false,del:false,search:false,refresh:false});
				jQuery("#PostoResultSet").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false, defaultSearch:"cn"}); 		
				//$('#PostsResultSet').triggerHandler('jqGridAfterGridComplete');
				//Toggle Toolbar Search 
				jQuery("#PostoResultSet").jqGrid('navButtonAdd',"#PostoPage",{
					caption:"Search",title:"Toggle Search",
					onClickButton:function(){ 			
						if(jQuery(".ui-search-toolbar").css("display")=="none") {
							jQuery(".ui-search-toolbar").css("display","");
						} else {
							jQuery(".ui-search-toolbar").css("display","none");
						}							
					} 
				}); 
				//jQuery("#PostsResultSet").jqGrid('setGridWidth',gridWidth); 	
				
			}
			
			function filterSelectEvent(){
				listPosts();
				var q1 = $(".noUi-handle-lower").attr("aria-valuetext");
				var q2 = $(".noUi-handle-upper").attr("aria-valuetext");
				var data = $("#filterForm").serialize();
				var data = data+"&q1="+q1+"&q2="+q2;
				$.ajax({
					type: "post",
					dataType:"text",
					data: data,
					url: baseUrl+'reload_posto_filters',
					success: function(returnData){
							$("#filterbody").html(returnData);
					},
					complete: function(){
						//hide loading image
					}
				});
			}
			function resetFilters(){
				$( ".filtersection ul li input[type='checkbox']" ).each(function() {
					$(this).removeAttr('checked');   			
				});
				$('#fromDate').val('');
				$('#toDate').val('');
				filterSelectEvent();
			}
			function initilizeGridSearchPlaceholder(){
				$(".ui-search-toolbar input[type='text']").each(function(){
					$(this).attr("placeholder","");
					$(this).attr("placeholder","Search");
			    });
		    }
			function showLpnDetails(postoId){
				$('.modelBoxContenHolder').html("<div class='mudelBoxLoading'>Loading...</div>");
				$('#modelBoxContenHolder').dialog('open');
				$('.modelBoxContenHolder').load(baseUrl+'bcp/edit_posto/'+postoId);
				return false;
			}
			function deletePosto(id){
				var j = confirm("Are you sure you want to delete record?");
				if(j){
					$("#PostoResultSet #"+id).css({display:"none"})
					}
				}
		</script>
		<style>
			/* #filterSlide{
					background: none repeat scroll 0 0 #f8f8f8;
				    border-color: #bbbbbb -moz-use-text-color #bbbbbb #bbbbbb;
				    border-image: none;
				    border-radius: 5px 0 0 5px;
				    border-style: solid none solid solid;
				    border-width: 3px 0 3px 3px;
				    position: absolute;
				    top: 0;
				    cursor: pointer;
				    right: -1px;
				    z-index: 1;
				    padding: 5px;
				} */
				#filterSlide{
					position: absolute;
				        top: 60px;
					    cursor: pointer;
					    right: 7px;
					    z-index: 1;
					    padding: 10px;
				}
				#filterSlide i{
					font-size: 25px;
				}
			#page-wrapper {
			    padding: 0 16px;
			}
			.filtersection INPUT[type='text'] {
			    width: 298px;
			}
			.gridWrapper {
		    width: 100%;
		    border :0px !important;
			}
			.gridWrapper .ui-jqgrid .ui-jqgrid-htable th div {
			    	color: #38537C;
			        font-weight: normal;
				    letter-spacing: 1px;
				    color: #000000;
				    font-size: 12px;
				    height: 30px;
				    line-height: 24px;
			}
			#verifiedGridContainer{
			width:100%;
			}
			.panel-body {
    			padding: 0px !important;
			}
			.module-list{
			 margin-top: 0px;
			}
			.ui-state-default ui-th-column ui-th-ltr:hover{
			background:none !important;
			}
		</style>
        <div class="row">
        	<div id="filterSlide" class="expanded">
						<button type="button" class="filterDisplay">Hide Filter</button>
					</div>
		 <div id="posto-listing" class="col-lg-9">
		 <br/><br/>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           <b>Posto Listings</b> 
                           <button class="pull-right">Export</button>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
							<div class='module-list'>
								<div class='gridWrapper' id='verifiedGridContainer'>
									<table id='PostsResultSet'></table>
									<div id='PostsContactPage'></div>
								</div>
							</div>
                        </div>
                    </div>
              	</div>
		<div id="filters"  class="col-lg-3">
			<div id="filterheader">
				<h4><!--<i class="fa fa-filter" style="color: #428bca"></i> --> Filters</h4>
				<!--  a id="resetbutton" type="button" class="btn-xs" href="#" onclick="resetFilters(); return false;"><i class="icon-refresh"></i> Reset</a> -->
			</div>
			<div id="filterbody">
				<?php include 'app/views/bcp/posto_refine_element.php';?>
			</div>
		</div>
	</div>      
	<div id='modelBoxContenHolder' class='modelBoxContenHolder'></div>
		@stop
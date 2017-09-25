@extends(Config::get('constants.admintheme'))
		
		@section('content')
		<script type="text/javascript">
			var rangeType = '<?php echo (isset($range)) ? $range : '';?>';
			var startDate = '<?php echo $weekStart; ?>';
			var presentDate = '<?php echo date('m/d/Y'); ?>';
			$(document).ready(function(){
				var mudelBoxOpts = {
					modal: true,
					autoOpen: false,
					width:900,
					position: ['center', 40],
					show: "scale",
					hide: "scale",
					dialogClass: 'mudelView',
					open: function() {
						//display correct dialog content
					}
				};
				$('#modelBoxContenHolder').dialog(mudelBoxOpts);
					
			 	listPosts(); 
			
				jQuery('#PostsResultSet').jqGrid('navGrid','#PostsContactPage',{edit:false,add:false,del:false,search:false,refresh:false});
				jQuery('#PostsResultSet').jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false, defaultSearch:'cn'});
			
				$('#fromDate').datepicker({
				});
			    $('#toDate').datepicker({
				});
			    $('#fromDate').val(startDate);
			    $('#toDate').val(presentDate);

			    $("#filterSlide").click(function(){
					slideClass = $(this).attr('class');
					if(slideClass == 'expanded'){
						$(this).removeClass('expanded');
						$(this).addClass('collapsed');	

						$("#posts").addClass('col-lg-12');
						$("#posts").removeClass('col-lg-9');
						$(".filterDisplay").html("Show Filter");
						$("#filters").hide();
					}else{
						$(this).removeClass('collapsed');
						$(this).addClass('expanded');

						$("#posts").addClass('col-lg-9');
						$("#posts").removeClass('col-lg-12');
						$(".filterDisplay").html("Hide Filter");
						$("#filters").show();
					}
					listPosts();
	      		});
	      		
			});
			function filterSelectEvent(){
				var startDate = $('#fromDate').val();
				
				var endDate   = $('#toDate').val();
					listPosts(startDate,endDate);
				}
			
			function listPosts(startDate=false,endDate=false){
				if(startDate!=false){
					var arrSplits = startDate.split("/");
					startDate = arrSplits[2]+"-"+arrSplits[0]+"-"+arrSplits[1];
					}

				if(endDate!=false){
					var arrSplits = endDate.split("/");
					endDate = arrSplits[2]+"-"+arrSplits[0]+"-"+arrSplits[1];
					}

				
				
				var targetUrl = baseUrl+'posts/list_posts';
				if(rangeType != ''){
					targetUrl += "/"+rangeType;
				}
				if(startDate != '' && endDate != ''){
					month_year	= startDate+"/"+endDate;
					targetUrl += "/0/"+month_year;
					}
				$("#verifiedGridContainer").html("");
				
				// Append the required div and table
				$("#verifiedGridContainer").html('<table id="PostsResultSet"></table><div id="PostsContactPage"></div>');
				//var ele	= document.getElementById('gridContainer');
				//var gridWidth	= ele.clientWidth;
				var gridWidth	= $('#verifiedGridContainer').width();
				jQuery("#PostsResultSet").jqGrid({
					url:targetUrl,
					datatype: "json",
					colNames: ['Id','Company','Plant','LPN','PublisherKey','SourcePointKey','Date','PartNumber','Material', 'Production Date'],
					colModel: [{name:'id',index:'id', hidden:true},
								{name:'company',index:'company', width:'100'},
								{name:'plant',index:'plant', width:'180', align:'center'},
								{name:'lpn',index:'lpn', width:'300', align:'center'},
								{name:'publisherkey',index:'publisherkey', width:'160', align:'center'},
								{name:'sourcepointkey',index:'sourcepointkey', width:'160', align:'center'},
								{name:'date',index:'date', width:'160'},
								{name:'partnumber',index:'partnumber', align:'center', width:'100'},
								{name:'name',index:'name', width:'160'},
								{name:'productiondate',index:'productiondate', width:'160'}
								],
					rownumbers: true,
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
					gridComplete: function(){
						var records = jQuery("#PostsResultSet").getGridParam('records');
						$('.total-count').html("Total Posts : "+records); 
						initilizeGridSearchPlaceholder();
						}
				});
				
				jQuery("#PostsResultSet").jqGrid('navGrid','#PostsContactPage',{edit:false,add:false,del:false,search:false,refresh:false});
				jQuery("#PostsResultSet").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false, defaultSearch:"cn"}); 		
				//$('#PostsResultSet').triggerHandler('jqGridAfterGridComplete');
				//Toggle Toolbar Search 
				jQuery("#PostsResultSet").jqGrid('navButtonAdd',"#PostsContactPage",{
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
			function initilizeGridSearchPlaceholder(){
				$(".ui-search-toolbar input[type='text']").each(function(){
					$(this).attr("placeholder","");
					$(this).attr("placeholder","Search");
			    });
		    }

			function showLpnDetails(lpn){
				$('.modelBoxContenHolder').html("<div class='mudelBoxLoading'>Loading...</div>");
				$('#modelBoxContenHolder').dialog('open');
				$('.modelBoxContenHolder').load(baseUrl+'posts/'+lpn+'/show');
				return false;
			}
		</script>
		<style>
			/* a.selected-filter{
				background: none repeat scroll 0 0 #bbbbbb;
			    padding: 3px 3px 3px 3px;
			    cursor: pointer;
			    color: #333;
			}
			#load_PostsResultSet{
				top: 40px;
			}
			#dateRange li{
				display: inline;
				float: left;
				margin-right: 5px;
			}
			.form-control{
			display: inline !important;
			}
			.filtersection ul{
			float:right;
			}
			.filtersection INPUT[type='text'] {
    		width: 150px !important;
			}
			#dateRange li input[type='text']{
				width: 100px;
				float: left;
		   	 	margin-right: 5%;
			} */
			
			
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
			#posto-listing {
			    padding: 0px;
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
		 <div id="posts" class="col-lg-9">
		 <br/><br/>
		 <?php 
                	$titleMessage = "All Posts";
                	if(isset($range) && $range != ''){
                		if($range == 'today')
                			$titleMessage = 'Posts Today';
                		if($range == 'week')
                			$titleMessage = 'Posts This Week';
                		if($range == 'month')
                			$titleMessage = 'Posts This Month';
                	}
         ?>
                   <div class="panel panel-default">
                        <div class="panel-heading">
                            Posts 
                            <?php if(isset($range) && $range != ''){?>
		                		<?php if($range=='today' || $range=='week' || $range=='month'){?>
		                            <a class="selected-filter" href="<?php echo Request::root();?>/posts">
		                            	<?php echo $range;?>
		                            	<i style="color: #428bca" class="glyphicon glyphicon-remove"></i>
		                            </a>
								<?php }else{?>
									<a class="selected-filter" href="<?php echo Request::root();?>/posts">
		                            	<?php echo 'Geo Map Filters';?>
		                            	<i style="color: #428bca" class="glyphicon glyphicon-remove"></i>
		                            </a>
								<?php }?>
		                	<?php } ?>
		                	<span class="pull-right total-count"></span>
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
				
			</div>
			<div id="filterbody">
				<?php include 'app/views/posts/posts_refine_element.php';?>
			</div>
		</div>
		</div>      
			
              <div id='modelBoxContenHolder' class='modelBoxContenHolder'></div>
		@stop
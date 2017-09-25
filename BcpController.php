<?php 
//comment here

class BcpController extends BaseController {
		
	function index(){
		//echo 'BCP layout';
		return View::make('bcp.bcp_form');
	}
	
	function layout_mobile(){
		return View::make('bcp.bcp_form_mobile');
	}
	
	function list_bcp(){
		$max_quantity = Posto::max('posto_qty');
		$min_quantity = Posto::min('posto_qty');
		return View::make('bcp.list_entries', compact('max_quantity','min_quantity'));
	}
	
	function get_listing(){
		$input = Input::all();
		$exactMatchFilters = array();
		$arrFilterData = array();
		//Search Filter Prepare
		//Quntity Storing variables
		if(isset($input['q1']) || isset($input['q2'])){
			if($input['q1'] != '' && $input['q2'] != ''){
				$quntityMin = $input['q1'];
				$quntityMax = $input['q2'];
				$arrFilterData['quntityWhere'] = " posto_qty >= '".$quntityMin."' AND posto_qty <= '".$quntityMax."'";
			}elseif($input['q1'] != ''){
				$quntityMin = $input['q1'];
				$arrFilterData['quntityWhere'] = " posto_qty >= '".$quntityMin;
			}elseif($input['q2'] != ''){
				//To date only
				$arrFilterData['quntityWhere'] = " posto_qty <= '".$quntityMax;
		
			}
		}
		if(isset($input['from_date']) || isset($input['to_date'])){
			if($input['from_date'] != '' && $input['to_date'] != ''){
				//rnage
				$timestamp = strtotime($input['from_date']);
				$fromDate = date("Y-m-d", $timestamp)." 00:00:00";
		
				$timestamp = strtotime($input['to_date']);
				$toDate = date("Y-m-d", $timestamp)." 23:59:59";
		
				$arrFilterData['dateWhere'] = " created_at >= '".$fromDate."' AND created_at <= '".$toDate."'";
			}elseif($input['from_date'] != ''){
				//From date only
				$timestamp = strtotime($input['from_date']);
				$fromDate = date("Y-m-d", $timestamp)." 00:00:00";
				$arrFilterData['dateWhere'] = " created_at >= '".$fromDate."'";
			}elseif($input['to_date'] != ''){
				//To date only
				$timestamp = strtotime($input['to_date']);
				$toDate = date("Y-m-d", $timestamp)." 23:59:59";
				$arrFilterData['dateWhere'] = " created_at <= '".$toDate."'";
		
			}
		}
		
		
		//postoNumber 
		$postoSelected = array();
		if(isset($input['posto_previous'])){
		$previous_selected = $input['posto_previous'];
			if(count($previous_selected) > 0){
				foreach($previous_selected as $selected){
					$postoSelected[] = $selected;
				}
			}
		}
		if(isset($input['posto_number']) && $input['posto_number']!=null){
			$postoSelected[] = $input['posto_number'];
		}
		$arrFilterData['posto_number'] = $postoSelected;
		
		//ssccnumber
		$ssccSelected = array();
		if(isset($input['sscc_previous'])){
		$previous_sscc_selected = $input['sscc_previous'];
			if(count($previous_sscc_selected) > 0){
				foreach($previous_sscc_selected as $sscc_selected){
					$ssccSelected[] = $sscc_selected;
				}
			}
		}
		if(isset($input['sscc_number']) && $input['sscc_number']!=null){
			$ssccSelected[] = $input['sscc_number'];
		}
		$arrFilterData['sscc_number'] = $ssccSelected;
		
		//materialSelected
		$materialSelected = array();
		if(isset($input['material_previous'])){
		$previous_mat_selected = $input['material_previous'];
			if(count($previous_mat_selected) > 0){
				foreach($previous_mat_selected as $mat_selected){
					$materialSelected[] = $mat_selected;
				}
			}
		}
		if(isset($input['material_number']) && $input['material_number']!=null){
			$materialSelected[] = $input['material_number'];
		}
		$arrFilterData['mat_number'] = $materialSelected;
		
		//materialSelected
		$locationSelected = array();
		if(isset($input['putaway_previous'])){
		$previous_put_selected = $input['putaway_previous'];
			if(count($previous_put_selected) > 0){
				foreach($previous_put_selected as $put_selected){
					$locationSelected[] = $put_selected;
				}
			}
		}
		if(isset($input['putaway_location']) && $input['putaway_location']!=null){
			$locationSelected[] = $input['putaway_location'];
		}
		$arrFilterData['location_number'] = $locationSelected;
		
		$page		= $_REQUEST['page']; // get the requested page 
		$limit 		= $_REQUEST['rows']; // get how many rows we want
		$start		= 0;
		$sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort
		$sord = $_REQUEST['sord']; // get the direction
		if(!$sidx) $sidx =1;
		$whereResultArray = array();
		if(isset($_REQUEST['filters'])){
			$filterData=$_REQUEST['filters'];
			$arrFilter=array();
			$arrFilter=json_decode(stripslashes($filterData));
			$field='field';
			$op='op';
			$data='data';
			$groupOp='groupOp';
			$searchGroupOperator=$this->search_nested_arrays($arrFilter, $groupOp);
			$searchString=$this->search_nested_arrays($arrFilter, $data);
			$searchOper=$this->search_nested_arrays($arrFilter, $op);
			$searchField=$this->search_nested_arrays($arrFilter, $field);
			$whereResultArray=array();
			if(count($searchField) > 0){
				foreach($searchField as $key=> $val){
					$whereResultArray[$val]=$searchString[$key];		
				}
			}
			$searchGroupOperator=$searchGroupOperator[0];
			$searchResults=array();
		}

		if(isset($range))
			$whereResultArray['range_type'] = $range;
		$count	= $this->getPostoByFilters($limit,$start,true,$sidx,$sord,$whereResultArray, $arrFilterData);
		if( $count >0 ) {
			$total_pages = ceil($count/$limit); 
		} else { 
			$total_pages = 0; 
		} 
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start < 0) $start = 0;
			
		$arrKolDetailResult = array();
		$data 				= array();
		$arrKolDetail		= array();
		
		
		$postsOriginal = $this->getPostoByFilters($limit,$start,false,$sidx,$sord,$whereResultArray,$arrFilterData);
		$arrRows = array();
		$data = array();
		foreach($postsOriginal as $row){
			$rowData = array();
			
			$rowData['id'] = $row['id'];
			$rowData['posto_number'] = $row['posto_number'];
			$rowData['posto_sscc_lpn'] = $row['posto_sscc_lpn'];
			$rowData['posto_bol_number'] = $row['posto_bol_number'];
			$rowData['posto_mu_number'] = $row['posto_mu_number'];
			$rowData['posto_mu_type'] = ($row['posto_mu_type']==1)?'Yes':'No';
			$rowData['posto_qty'] = $row['posto_qty'];
			$rowData['posto_batch'] = $row['posto_batch'];
			$rowData['posto_pe_date'] = date("m/d/Y", strtotime($row['posto_pe_date']));
			$rowData['posto_pe_type'] = ($row['posto_pe_type']==1)?'Yes':'No';
			$rowData['posto_put_location'] = $row['posto_put_location'];
			$rowData['posto_status'] = $row['posto_status'];
			$rowData['updated_at'] = $row['updated_at'];
			$rowData['created_at'] = $row['created_at'];
			$arrRows[] = $rowData;
		}
		
		$data['records']=$count;
		$data['total']=$total_pages;
		$data['page']=$page;
		$data['rows']=$arrRows;
		echo json_encode($data);
	}
	
	function search_nested_arrays($array, $key){
		if(is_object($array))
			$array = (array)$array;
		  
			// search for the key
			$result = array();
			foreach ($array as $k => $value) {
				if(is_array($value) || is_object($value)){
					$r = $this->search_nested_arrays($value, $key);
					if(!is_null($r))
						array_push($result,$r);
				}
			}
		  
			if(array_key_exists($key, $array))
				array_push($result,$array[$key]);
				 
				 
				if(count($result) > 0){
					// resolve nested arrays
					$result_plain = array();
					foreach ($result as $k => $value) {
						if(is_array($value))
							$result_plain = array_merge($result_plain,$value);
							else
								array_push($result_plain,$value);
					}
					return $result_plain;
				}
				return NULL;
	}
	function getPostoByFilters($limit=null,$startFrom=null,$doCount=null,$sidx = '',$sord = '',$where = '', $arrFilterData =''){
		$whereRaw = "";
		$initialObj = Posto::limit($limit)->skip($startFrom);
		if($whereRaw != '')
			$initialObj = $initialObj->whereRaw($whereRaw);
			
			
			if(isset($arrFilterData['quntityWhere'])){
				$initialObj->whereRaw($arrFilterData['quntityWhere']);
			}
			
			if(isset($arrFilterData['dateWhere'])){
				$initialObj->whereRaw($arrFilterData['dateWhere']);
			}
			
			if(isset($arrFilterData['posto_number']) && count($arrFilterData['posto_number']) > 0){
				//$whereRaw = "lpn LIKE '%".$where['lpn']."%'";
				$initialObj->whereIn('posto_number', $arrFilterData['posto_number']);
			}
			if(isset($arrFilterData['sscc_number'])  && count($arrFilterData['sscc_number']) > 0){
				//$whereRaw = "lpn LIKE '%".$where['lpn']."%'";
				$initialObj->whereIn('posto_sscc_lpn', $arrFilterData['sscc_number']);
			}
			if(isset($arrFilterData['mat_number']) && count($arrFilterData['mat_number']) > 0){
				//$whereRaw = "lpn LIKE '%".$where['lpn']."%'";
				$initialObj->whereIn('posto_mu_number', $arrFilterData['mat_number']);
			}
			if(isset($arrFilterData['location_number']) && count($arrFilterData['location_number']) > 0){
				//$whereRaw = "lpn LIKE '%".$where['lpn']."%'";
				$initialObj->whereIn('posto_put_location', $arrFilterData['location_number']);
			}
	
			if(isset($where['posto_sscc_lpn'])){
				$initialObj->where('posto_sscc_lpn','like', "%".$where['posto_sscc_lpn']."%");
			}
			if(isset($where['posto_number'])){
			 	$query = "CAST(posto_number AS TEXT) like '%".$where['posto_number']."%'";
				$initialObj->whereRaw($query);
			}
			if(isset($where['posto_mu_number'])){
				$query = "CAST(posto_mu_number AS TEXT) like '%".$where['posto_mu_number']."%'";
				$initialObj->whereRaw($query);
			}
			if(isset($where['posto_put_location'])){
				$query = "CAST(posto_put_location AS TEXT) like '%".$where['posto_put_location']."%'";
				$initialObj->whereRaw($query);
			}
			/*
			if(isset($where['posto_mu_number'])){
				$initialObj->where('posto_mu_number','like', "%".$where['posto_mu_number']."%");
			}
			if(isset($where['posto_pe_date'])){
				//$whereRaw = "date LIKE '%".$where['date']."%'";
				$initialObj->where('posto_pe_date','like', "%".$where['posto_pe_date']."%");
			}
			if(isset($where['posto_qty'])){
				//$whereRaw = "partnumber LIKE '%".$where['partnumber']."%'";
				$initialObj->where('posto_qty','like', "%".$where['posto_qty']."%");
			}
			if(isset($where['posto_put_location'])){
				$initialObj->where('posto_put_location','ilike', "%".$where['posto_put_location']."%");
			} */
			
			if($doCount){
				return $initialObj->count();
			}
			
			if($sidx!='' && $sord!=''){
				$initialObj->orderBy('created_at', 'DESC');
			}
			
			$initialObj->orderBy('created_at', 'DESC');
			$selectArray = array();
			$selectArray[] = 'id';
			$selectArray[] = 'posto_number';
			$selectArray[] = 'posto_sscc_lpn';
			$selectArray[] = 'posto_bol_number';
			$selectArray[] = 'posto_mu_number';
			$selectArray[] = 'posto_mu_type';
			$selectArray[] = 'posto_qty';
			$selectArray[] = 'posto_batch';
			$selectArray[] = 'posto_pe_date';
			$selectArray[] = 'posto_pe_type';
			$selectArray[] = 'posto_put_location';
			$selectArray[] = 'posto_status';
			$selectArray[] = 'updated_at';
			$selectArray[] = 'created_at';
	
			$postsOriginal = $initialObj->get($selectArray);
			//print_r(DB::getQueryLog());exit;
			return $postsOriginal;
	}
	
	function set_posto_data(){
		$input = Input::all();
		
		//Add Address Details
		$posto = array();
		$posto['posto_number'] = $input['po_sto_number'];
		$posto['posto_bol_number'] = $input['bol'];
		
		$no_of_fields = $input['no_of_fileds'];
		
			$posto['posto_mu_number'] = $input['mat_upc_number'];
			$posto['posto_mu_type'] = 1;
			$posto['posto_qty'] = $input['quantity'];
			$posto['posto_batch'] = $input['batch'];
			$posto['posto_pe_date'] = "2016-11-17";
			$posto['posto_pe_type'] = 1;
			$posto['posto_put_location'] = $input['destBin'];
			$posto['posto_sscc_lpn'] = $input['sscc_number'];
			$posto['posto_status'] = 1;
			
			if($no_of_fields > 1){
				$bulk_insert_data = array();
				$now = date('Y-m-d H:i:s');
				$posto['updated_at'] = $now;
				$posto['created_at'] = $now;
				$bulk_insert_data[] = $posto;
				for($i = 2;$i<=$no_of_fields;$i++){
					$posto1['posto_number'] = $input['po_sto_number'];
					$posto1['posto_bol_number'] = $input['bol'];
					$posto1['posto_mu_number'] = $input['mat_upc_number'.$i];
					$posto1['posto_mu_type'] = 1;
					$posto1['posto_qty'] = $input['quantity'.$i];
					$posto1['posto_batch'] = $input['batch'.$i];
					$posto1['posto_pe_date'] = "2016-11-17";
					$posto1['posto_pe_type'] = 1;
					$posto1['posto_put_location'] = $input['destBin'.$i];
					$posto1['posto_sscc_lpn'] = $input['sscc_number'.$i];
					$posto1['posto_status'] = 1;
					$posto1['updated_at'] = $now;
					$posto1['created_at'] = $now;
					$bulk_insert_data[] = $posto1;
				}
			}
			
		$rules = Posto::$rules;
		$validation = Validator::make($posto,$rules);
		if ($validation->passes())
		{
			if($no_of_fields > 1){
				Posto::insert($bulk_insert_data);
			 }else{
				Posto::create($posto);
			}
			$data['status'] = true;
			$data['message'] = "Data Stored Successfully!";
		}else{
			$data['status'] = false;
			$data['message'] = "There were validation errors";
		}
		echo json_encode($data);
	}	
	
	function edit_posto(){
		return View::make('bcp.bcp_edit_form');
	}
	function reload_posto_filters(){
		$input = Input::all();
		
		//Date Stroing
		$dateWhere = '';
		if(isset($input['from_date']) || isset($input['to_date'])){
			if($input['from_date'] != '' && $input['to_date'] != ''){
				//rnage
				$timestamp = strtotime($input['from_date']);
				$fromDate =$input['from_date'];
	
				$timestamp = strtotime($input['to_date']);
				$toDate = $input['to_date'];
	
				$dateWhere = " date >= '".$fromDate."' AND date <= '".$toDate."'";
			}elseif($input['from_date'] != ''){
				//From date only
				$timestamp = strtotime($input['from_date']);
				$fromDate =$input['from_date'];
				$dateWhere = " date >= '".$fromDate."'";
			}elseif($input['to_date'] != ''){
				//To date only
				$timestamp = strtotime($input['to_date']);
				$toDate = $input['to_date'];
				$dateWhere = " date <= '".$toDate."'";
	
			}
		}
		
		//Quntity Storing variables
		if(isset($input['q1']) || isset($input['q2'])){
			if($input['q1'] != '' && $input['q2'] != ''){
				$min_quantity = $input['q1'];
				$max_quantity = $input['q2'];
			}elseif($input['q1'] != ''){
				//From date only
				$min_quantity = $input['q1'];
			}elseif($input['q2'] != ''){
				//To date only
				$max_quantity = $input['q2'];
				
			}
			
		}
		
		//postoNumber 
		$postoSelected = array();
		if(isset($input['posto_previous'])){
		$previous_selected = $input['posto_previous'];
			if(count($previous_selected) > 0){
				foreach($previous_selected as $selected){
					$postoSelected[] = $selected;
				}
			}
		}
		if(isset($input['posto_number']) && $input['posto_number']!=null){
			$postoSelected[] = $input['posto_number'];
		}
		
		//ssccnumber
		$ssccSelected = array();
		if(isset($input['sscc_previous'])){
		$previous_sscc_selected = $input['sscc_previous'];
			if(count($previous_sscc_selected) > 0){
				foreach($previous_sscc_selected as $sscc_selected){
					$ssccSelected[] = $sscc_selected;
				}
			}
		}
		if(isset($input['sscc_number']) && $input['sscc_number']!=null){
			$ssccSelected[] = $input['sscc_number'];
		}
		
		//materialSelected
		$materialSelected = array();
		if(isset($input['material_previous'])){
		$previous_mat_selected = $input['material_previous'];
			if(count($previous_mat_selected) > 0){
				foreach($previous_mat_selected as $mat_selected){
					$materialSelected[] = $mat_selected;
				}
			}
		}
		if(isset($input['material_number']) && $input['material_number']!=null){
			$materialSelected[] = $input['material_number'];
		}
		
		//materialSelected
		$locationSelected = array();
		if(isset($input['putaway_previous'])){
		$previous_put_selected = $input['putaway_previous'];
			if(count($previous_put_selected) > 0){
				foreach($previous_put_selected as $put_selected){
					$locationSelected[] = $put_selected;
				}
			}
		}
		if(isset($input['putaway_location']) && $input['putaway_location']!=null){
			$locationSelected[] = $input['putaway_location'];
		}
		
		
								
		//return View::make('reports.geo_map_refine_element', compact('topPlants','topCompanies','topNames','tcShowAll','topCompaniesSelected','tpShowAll','topPlantsSelected','tnShowAll','topNamesSelected','fromDate','toDate'));
		return View::make('bcp.posto_refine_element', compact('postoSelected','ssccSelected','materialSelected','locationSelected','min_quantity','max_quantity','fromDate','toDate'));
	}
	
	function get_posto_numbers($str){
			$arrSuggests =array();
			$query = "CAST(posto_number AS TEXT) like '".$str."%'";
			$results = DB::table('posto_table')->whereRaw($query)->limit('5')->get();
			foreach($results as $row){
				//$arrSuggests[] = $row->name;
				$arrSuggests[]="<div class='dataSet'><label name='".$row->id."' class='idfield' style='display:block'>".$row->posto_number."</label></div>";
			}
			$arrReturnData['query'] 		= $str;
			$arrReturnData['suggestions']	= $arrSuggests;
			echo json_encode($arrReturnData);
	}
	
	function get_sscc_numbers($str){
		$arrSuggests =array();
		$results = Posto::where('posto_sscc_lpn','like',$str.'%')->limit('5')->get(array('posto_sscc_lpn'));
		foreach($results as $row){
			//$arrSuggests[] = $row->name;
			$arrSuggests[]="<div class='dataSet'><label name='".$row->id."' class='idfield' style='display:block'>".$row->posto_sscc_lpn."</label></div>";
		}
		$arrReturnData['query'] 		= $str;
		$arrReturnData['suggestions']	= $arrSuggests;
		echo json_encode($arrReturnData);
	}
	
	function get_material_numbers($str){
		$arrSuggests =array();
		$query = "CAST(posto_mu_number AS TEXT) like '".$str."%'";
		$results = DB::table('posto_table')->whereRaw($query)->limit('5')->get();
		foreach($results as $row){
			//$arrSuggests[] = $row->name;
			$arrSuggests[]="<div class='dataSet'><label name='".$row->id."' class='idfield' style='display:block'>".$row->posto_mu_number."</label></div>";
		}
		$arrReturnData['query'] 		= $str;
		$arrReturnData['suggestions']	= $arrSuggests;
		echo json_encode($arrReturnData);
	}
	
	function get_putaway_location($str){
		$arrSuggests =array();
		$query = "CAST(posto_put_location AS TEXT) like '".$str."%'";
		$results = DB::table('posto_table')->whereRaw($query)->limit('5')->get();
		foreach($results as $row){
			//$arrSuggests[] = $row->name;
			$arrSuggests[]="<div class='dataSet'><label name='".$row->id."' class='idfield' style='display:block'>".$row->posto_put_location."</label></div>";
		}
		$arrReturnData['query'] 		= $str;
		$arrReturnData['suggestions']	= $arrSuggests;
		echo json_encode($arrReturnData);
	}
	
	function check_lpn_availability(){
		$input = Input::all();
		$lpn = $input['lpn_number'];
		$results = PostOriginal::where('lpn',$lpn)->get();
		
		if(count($results) > 0){
			$data['status'] = "ava";
			$data['lpn'] = $results[0]->lpn;
			$data['upc'] = $results[0]->upc12;
			$data['quantity'] = $results[0]->quantity;
			$data['batch'] = $results[0]->batch;
			if($results[0]->productiondate!=''){
				$data['pedate'] = $results[0]->productiondate;
				$data['pedate_status'] = 1;
			}else{
				$data['pedate'] = $results[0]->expirationdate;
				$data['pedate_status'] = 0;
			}
		}else{
			$data['status'] = "nav";
			$data['lpn'] = $lpn;
		}
		
		echo json_encode($data);
	}
	
	function get_sscc_numbers_listing($str){
		$arrSuggests =array();
		$results = PostOriginal::where('lpn','like',$str.'%')->limit('5')->get(array('lpn'));
		foreach($results as $row){
			//$arrSuggests[] = $row->name;
			$arrSuggests[]="<div class='dataSet'><label name='".$row->id."' class='idfield' style='display:block'>".$row->lpn."</label></div>";
		}
		$arrReturnData['query'] 		= $str;
		$arrReturnData['suggestions']	= $arrSuggests;
		echo json_encode($arrReturnData);
	}
	
}

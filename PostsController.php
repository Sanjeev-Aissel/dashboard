<?php 
//comment here

class PostsController extends BaseController {
		
	function index($range = null){
		if(!isset($range))
			$range = '';
		$range = urldecode($range);
		parse_str($range,$exactMatchFilters);
		/**/
		if(date('D')!='Mon')
		{
			$weekStart = date('m/d/Y',strtotime('last Monday'));
		}else{
			$weekStart = date('m/d/Y');
		}
		return View::make('posts.index',compact('range','exactMatchFilters', 'weekStart'));
	}
	
	function list_posts($range = null, $start_date = null, $end_date = null){
		$exactMatchFilters = array();
		/* if(!($range =='today' || $range =='week' || $range =='month') && $start_date == null && $end_date == null){
			$range = 'week';
		} */
		if(isset($range) && !($range =='today' || $range =='week' || $range =='month')){
			//echo $range;
			$range = urldecode($range);
			parse_str($range,$exactMatchFilters);
			//print_r($exactMatchFilters);
			
		}

		$arrFilterData = array();
		$arrFilterData['startDate'] = $start_date;
		$arrFilterData['endDate'] = $end_date;
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
		$count	= $this->getPostsByFilters($limit,$start,true,$sidx,$sord,$whereResultArray,$exactMatchFilters, $arrFilterData);
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
		
		
		$postsOriginal = $this->getPostsByFilters($limit,$start,false,$sidx,$sord,$whereResultArray,$exactMatchFilters, $arrFilterData);
		$arrRows = array();
		$data = array();
		foreach($postsOriginal as $row){
			$rowData = array();
			
			$rowData['id'] = $row['id'];
			
			if($row->companyObj)
				$rowData['company'] = $row->companyObj->name;
			else 
				$rowData['company'] = "";
			
			$rowData['company'] ="<a href='".Request::root()."/companies/".$row->company_id."'>".$rowData['company']."</a>";
			
			if($row->plantObj)
				$rowData['plant'] = $row->plantObj->name;
			else 
				$rowData['plant'] = "";
			
			$rowData['plant'] ="<a href='".Request::root()."/plants/".$row->plant_id."'>".$rowData['plant']."</a>";
			$rowData['lpn'] = $row->lpn;
			$rowData['lpn'] ="<a href='#' onclick=\"showLpnDetails('".$rowData['lpn']."'); return false;\">".$rowData['lpn']."</a>";
			$rowData['publisherkey'] = $row->publisherkey;
			$rowData['sourcepointkey'] = $row->sourcepointkey;
			$rowData['date'] = date("Y-m-d H:i:s", strtotime($row->postsDate));
			//$rowData['time'] = $row->time;
			$rowData['partnumber'] = $row->partnumber;
			$rowData['name'] = $row->name;
			$rowData['productiondate'] = $row->productiondate;
			$rowData['productiontime'] = $row->productiontime;
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
	
	function getPostsByFilters($limit=null,$startFrom=null,$doCount=null,$sidx = '',$sord = '',$where = '',$exactMatchFilters = '', $arrFilterData =''){
		$whereRaw = "";
		if(isset($where['range_type'])){
			$whereRange = '';
			$thisYear =  date('Y');
			$thisMonth =  date('m');
			$thisMonth =  9;
			$thisDay =  date('d');
			
			switch($where['range_type']){
				case 'year' : $whereRange = " date >= '".$thisYear."-01-01 00:00:00'";
								break;
				case 'month' : $whereRange = " date >= '".$thisYear."-".$thisMonth."-01 00:00:00'";
								break;
				case 'week' : 
								//check the current day
								if(date('D')!='Mon')
								{    
								 //take the last monday
								  $staticstart = date('Y-m-d',strtotime('last Monday'));    
								
								}else{
								    $staticstart = date('Y-m-d');   
								}
								//always next saturday
								if(date('D')!='Sat')
								{
								    $staticfinish = date('Y-m-d',strtotime('next Saturday'));
								}else{
								        $staticfinish = date('Y-m-d');
								}
								$whereRange = " date >= '".$staticstart." 00:00:00'";
								break;
				case 'today' : $whereRange = " date = '".$thisYear."-".$thisMonth."-".$thisDay." 00:00:00'";
								break;
			}
			$whereRaw = $whereRange;
		}
		$initialObj = Post::with('companyObj','plantObj')->limit($limit)->skip($startFrom);
		if($whereRaw != '')
			$initialObj = $initialObj->whereRaw($whereRaw);
		//Relatinal queries
		if(isset($where['company'])){
			$initialObj->whereHas('companyObj', function($q) use($where)
			{
			    $q->where('name', 'ilike', "%".$where['company']."%");
			
			});
		}
		if(isset($where['plant'])){
			$initialObj->whereHas('plantObj', function($q) use($where)
			{
			    $q->where('name', 'ilike', "%".$where['plant']."%");
			});
		}
		if(isset($where['lpn'])){
			//$whereRaw = "lpn LIKE '%".$where['lpn']."%'";
			$initialObj->where('lpn','like', "%".$where['lpn']."%");
		}
		if(isset($where['publisherkey'])){
			$initialObj->where('publisherkey','like', "%".$where['publisherkey']."%");
		}
		if(isset($where['sourcepointkey'])){
			$initialObj->where('sourcepointkey','like', "%".$where['sourcepointkey']."%");
		}
		if(isset($where['date'])){
			//$whereRaw = "date LIKE '%".$where['date']."%'";
			$initialObj->where('date','like', "%".$where['date']."%");
		}
		if(isset($where['partnumber'])){
			//$whereRaw = "partnumber LIKE '%".$where['partnumber']."%'";
			$initialObj->where('partnumber','like', "%".$where['partnumber']."%");
		}
		if(isset($where['name'])){
			$initialObj->where('posts_converted.name','ilike', "%".$where['name']."%");
		}
		
		//-----------Start and End date range Filters --------------------------------
		if(isset($arrFilterData['startDate'])){
			//$whereRaw = "partnumber LIKE '%".$where['partnumber']."%'";
			//$initialObj->whereBetween('posts_converted.created_at', array($arrFilterData['startDate']." 00:00:00", $arrFilterData['endDate']." 23:59:59"))->first();
			//$fromDate = date("Y-m-d", $arrFilterData['startDate'])." 00:00:00";
			$initialObj->where('date', '>=', $arrFilterData['startDate']." 00:00:00");
		//	$toDate = date("Y-m-d", $arrFilterData['endDate'])." 23:59:59";
			$initialObj->where('date', '<=', $arrFilterData['endDate']." 23:59:59");
		}
		
		
		//-----------Exact Match Filters --------------------------------
		if(!empty($exactMatchFilters) && count($exactMatchFilters) > 0){
			if(isset($exactMatchFilters['plant_id'])){
				$initialObj->where('plant_id','=', $exactMatchFilters['plant_id']);
			}
			if(isset($exactMatchFilters['names'])){
				$initialObj->whereIn('posts_converted.name', $exactMatchFilters['names']);
			}
			if(isset($exactMatchFilters['from_date']) && $exactMatchFilters['from_date'] != ''){
				$exactMatchFilters['from_date'] = str_replace('-','/',$exactMatchFilters['from_date']);
				$timestamp = strtotime($exactMatchFilters['from_date']);
				$fromDate = date("Y-m-d", $timestamp)." 00:00:00";
				$initialObj->where('date', '>=', $fromDate);
			}
			if(isset($exactMatchFilters['to_date']) && $exactMatchFilters['to_date'] != ''){
				$exactMatchFilters['to_date'] = str_replace('-','/',$exactMatchFilters['to_date']);
				$timestamp = strtotime($exactMatchFilters['to_date']);
				$toDate = date("Y-m-d", $timestamp)." 23:59:59";
				$initialObj->where('date', '<=', $toDate);
			}
		}
		
		if($doCount){
			return $initialObj->count();;
		}
		
		if($sidx!='' && $sord!=''){
			if($sidx == 'company'){
				$initialObj->join('companies', 'companies.id', '=', 'posts_converted.company_id');
				$sidx = "companies.name";
			}
			if($sidx == 'plant'){
				$initialObj->join('plants', 'plants.id', '=', 'posts_converted.plant_id');
				$sidx = "plants.name";
			}
			if($sidx == 'name'){
				$sidx = "posts_converted.created_at";
			}
			$initialObj = $initialObj->orderBy($sidx,$sord);
		}else{
			$initialObj->orderBy('created_at', 'DESC');
		}
		$selectArray = array();
		$selectArray[] = 'posts_converted.id';
		$selectArray[] = 'company_id';
		$selectArray[] = 'plant_id';
		$selectArray[] = 'lpn';
		$selectArray[] = 'publisherkey';
		$selectArray[] = 'sourcepointkey';
		$selectArray[] = 'posts_converted.created_at as postsDate';
		$selectArray[] = 'partnumber';
		$selectArray[] = 'posts_converted.name';
		$selectArray[] = 'posts_converted.productiondate';
		$selectArray[] = 'posts_converted.productiontime';
		
		$postsOriginal = $initialObj->get($selectArray);
		//print_r(DB::getQueryLog());exit;
		return $postsOriginal;
	}
	
	function show($lpn){
		$postObj = PostOriginal::where('lpn','=',$lpn)->get();
		$row = $postObj[0];
		$data['row'] = $row;
		return View::make('posts.show',$data);
	}
}

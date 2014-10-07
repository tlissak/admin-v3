<?

class AdminMvc extends AdminList{
	
	public $viewtype = 'SIMPLE'; 
	
	public $image  =array();
	public $_sort =array();
	
	public $initializedViewList = false ;
	
	public $_r_imgp = array() ;
	public $_r_kys = array() ;	
	
	
	
	
	public function getTableHtml(){
		
		global $tbl;
		
		
		
		$h = '<div class="context" 
					data-tbl="'.$tbl.'" 
		 			data-contexttbl="'.  (isset($this->keys['name']) ? $this->keys['name'] :  $this->name ) .'" 
					data-id="'.$this->id.'" >' ;
					
		if (isset($this->keys['name']) && ! $this->protected){
				$h .= '<a  class="relation-add btn-orange"  data-id="0" >
						<i class="icon-plus"></i>'.l('new').' : '. l($this->keys['name']) .'</a>' ;
			
		}
		
		$h .= $this->getTablePaging() ;
		
		$h .= $this->getTableState();
		
		
		
		
		$h .= '<table class="tbl">';
		
		$h .= '<thead><tr>';
		if (($this->viewtype == "SELECT" || $this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT") && ! $this->protected){			
			$h .= '<th style="width:4%">&#9745;</th>';
		}
		foreach ($this->view as $k=>$v){
				$h .= '<th>'. $v .'</th>';
		}
		if (($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT") && ! $this->protected){
			$h .= '<th style="width:4%">&#x270E;</th>';
		}
		$h.= '</tr>';
		
		$h .= $this->getTableControlHtml();
		
		$h .='</thead>' ;
		
		$h .= '<tbody class="list-'. $this->name .'">' ;			

		$h .= $this->getTableBody() ;		
		$h .= '</tbody></table>';
		$h .= $this->getTablePaging() ;
		
		$h .= "</div>" ;		
		return $h ;
	}
	
	public function getTableState(){
		$h = '';
        if ($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT"){		
			$h .= '<div class="list-state" data-viewtype="'.$this->viewtype.'" data-cache="'.implode(',',$this->selected).'"  ';
			if ($this->viewtype == "SELECT-EDIT"){
				$h .= '	data-fieldname="'.$this->name.'[]" '  ;
			}
			if ($this->viewtype == "SELECT-ONE-EDIT"){
				$h .= '	data-fieldname="'.$this->keys['left_key'].'" '  ;
			}
			$h .= '>';
			
			if (count($this->selected ) == 0 && $this->viewtype == "SELECT-ONE-EDIT"){
				$h .= '<input type="hidden" name="'.$this->keys['left_key'].'"  id="fld_'.$this->name .'" value="0"   checked="checked" />'  ;
			}
			
			foreach($this->selected as $id){
				if ($this->viewtype == "SELECT-EDIT"){
					$h .= '<input type="hidden" name="'.$this->name.'[]" value="'.$id .'"    />'  ;
				}
				if ($this->viewtype == "SELECT-ONE-EDIT"){
					$h .= '<input type="hidden" name="'.$this->keys['left_key'].'"  id="fld_'.$this->name .'" value="'.$id .'" />'  ;
				}
			}
			
			$h .= '</div>' ;
		} 
		return $h ;
	}
	
	public function getTableControlHtml(){
		$out = '';
		$out .= '<tr class="form-filter" >';
		
		if (($this->viewtype == "SELECT" || $this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT" ) && ! $this->protected){			
			$out .= '<th style="width:4%">&nbsp;</th>';
		}
		
        foreach( $this->view as $key=>$title){
			$out .= '<th>';                
			
			$used_key_relation = false;
			foreach($this->relations as $r){
				if (isset($r->keys['left_key']) && $r->keys['left_key'] .'_inner' == $key){
						$used_key_relation = $r;
						break;
				}
			}


			/*if ($used_key_relation && count($used_key_relation->_list) < 30) {
                $out .= '<select name="filter[' . $used_key_relation->keys["left_key"] . ']" class="filter-select" >';
                $out .= '<option value="">-</option>';
                foreach ($used_key_relation->_list as $li) {
                    $out .= '<option value="' . $li['id'] . '">' . $li[$used_key_relation->fld_title] . '</option>';
                }
                $out .= '</select>';
            }else{*/
            $out .=  '<input type="text" name="filter['.  $key .']" class="filter-search" />' ;
            //}
			
			$out .= '<select name="order['.$key.']" class="order-sort">
                                	<option value=""></option>
                                	<option value="ASC">'.l('sort direction A first').'</option>
                                    <option value="DESC">'.l('sort direction Z first').'</option>
                                </select>';
								
			$out .= '</th>' ;
		}
		
		if (($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT" ) && ! $this->protected){
			$out .= '<th style="width:4%">&nbsp;</th>';
		}
		
		$out .= '</tr>';
		return $out;
	}
	
	function getTablePaging(){
		if ($this->pages == 1 || $this->num_results == 0)
			return '';

		$ho = '<div class="paging">' ;
		$params = 'tbl='.$this->name  ; 
		
		if ($this->pages_show_first){
			$ho .= '<a class="btn" href="?page=0&'. $params .'" data-page="0" title="First page"> &laquo; </a>';
			$ho .= '<span class="btn-disable">..</span>';
		}
		if ($this->pages_show_prev){
            $ho .= '<a class="btn" href="?page='. ($this->page-1) .'&'.$params .'" data-page="'.($this->page-1).'"  title="Prev page">&laquo; '.l('prev').'</a>' ;
        }
		
		for($i=$this->pages_range_start ;$i<$this->pages_range ; $i++ ){ 
               if ($i == $this->page){
            	    $ho .= '<span class="btn-disable">'. ($i+1) .'</span>';
               }else{ 
          	      $ho .= '<a class="btn" href="?page='.$i .'&'.  $params .'" data-page="'.$i.'" title="Page '.  $i .'">'. ($i+1).'</a>';
               } 
        } 
		if ($this->pages_show_next){
            $ho .= '<a class="btn" href="?page='. ($this->page+1) .'&'.$params .'" data-page="'.($this->page+1).'"  title="Next page"> '.l('next').' &raquo;</a>' ;
        }
		if ($this->pages_show_last){
			$ho .= '<span class="btn-disable">..</span>';
			$ho .= '<a class="btn" href="?page='. ($this->pages-1) .'&' . $params. '" data-page="'.($this->pages-1).'" title="Last page"> &raquo; </a>' ;
		} 
		$ho .= '<br />' ;
		$ho .= l('results').' :'. $this->results  . l(' of ') . $this->num_results .' - '.  l('page').' '.($this->page+1) .l(' of '). $this->pages ." " .l('pages')  ;
        $ho .= '</div>' ;		
		return $ho ;
	}
	
	function getTableBody(){ // created for paginig w ajax
		$this->initViewList() ; // has protection
		
		$oh = '';		
				
		if ($this->viewtype == "SELECT-ONE-EDIT"){ // add empty line
			$empty_row = array('id'=>0,''=>0) ;
			foreach($this->view as $k=>$v){	$empty_row[$k]	 = '&nbsp;-&nbsp;';		}
			$empty_row['id'] = 0 ;
			$empty_row['_selected'] = 0 ;
			$oh .=  $this->getTableRow( $empty_row ) ;
		}		
	
		foreach($this->_list as $l){
			$oh .=  $this->getTableRow($l) ."\n" ; 
		}
		return $oh;
	}
	
	public function getTableRowById($id){
		if (isset($this->data['id']) && $this->data['id']== $id ){
			//this function called by set relation form get row  set selected ok ! 
			$this->data['_selected'] = 1 ;
			return $this->getTableRow($this->data) 	;
		}else{
			fb($this->id);
			fb($this->data);	
			fb($id);
			_die('Out of range called  #4-67');
		}		
	}
	
	function getTableRow($dt = array()){
		
			$this->initViewList();
			
			
			if (!isset($dt['_selected'])){				
				Debug("Selected not seted "); 
			}
			
			if (!isset($dt['id'])){
				Debug('no id field in db/list ??');
			}
			
			$id = $dt['id'] ;			
			
			$tr = '';
			$tr .= '<tr data-id="' . $id .'" data-tbl="'.$this->name .'" '  ;
			$tr .= ' class="row-' . $this->name . '-'. $id  ;
			if ($this->viewtype == "SIMPLE"){
				$tr .=  ($dt['_selected'] ? ' selected ' : '')  ;
			}
			$tr .=  '" >' ;
				
			if ($this->viewtype == "SELECT-EDIT" && !$this->protected){
				$tr .= '<td><input class="relation-cb relation-cbx" type="checkbox"  id="_fld_'.$this->name.'_'.$id .'" value="'.$id .'"  '. ($dt['_selected'] ? 'checked="checked"' : '') .'   /></td>'  ;
			}
			if (($this->viewtype == "SELECT" || $this->viewtype == "SELECT-ONE-EDIT" )  && !$this->protected){
				$tr .= '<td><input class="relation-cb" type="radio"   id="_fld_'.$this->name.'_'.$id .'" value="'.$id .'"   '. ($dt['_selected'] ? 'checked="checked"' : '') .'   /></td>'  ;
			}
			
			if( count($this->_r_imgp )){
				foreach($this->_r_imgp as $img_k){
					foreach($this->view as $vw_k=>$vw_v){
						if ($vw_k == $img_k){
							$dt[$img_k] = $this->thumb($dt[$img_k]) ; 
						}
					}
				}
			}
			
			if( count($this->_sort )){
				foreach($this->_sort as $sort_k){
					foreach($this->view as $vw_k=>$vw_v){
						if ($vw_k == $sort_k){
							$dt[$sort_k] = $this->sortable($sort_k,$dt[$sort_k]) ; 
						}
					}
				}
			}

			foreach ($this->view as $k=>$v){
				if ( ! array_key_exists ($k,$dt)){
					if (DEV_MODE)
						Debug (' Check The row dosent contains key ' . $k);
					$tr .= '<td>'.$id .'!</td>' ;
				}else{
					if ($dt[$k] != null){
						$tr .= '<td>'. $dt[$k] .'</td>' ;
					}else{
						$tr .= '<td>&nbsp;</td>' ;
					}
				}
			}
			
		 	if ( ($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT" ) && !$this->protected){
					$tr .= '<td>' ;
					if ($id>0){					
						$tr .='<span class="relation-mod btn"  data-id="' . $id .'" ><i class="icon-editalt"></i></span>' ;
					}else{
						$tr .= '&nbsp;';
					}
					$tr .= '</td>'  ;
			}
				 
				 
			$tr .= "</tr>";
		return $tr ;	;
	}
	
	function thumb($img){
		if (V2_IMG)
			return 	'<img src="../'. ((strpos($img,'photos/') === 0 )? $img : 'photos/'. $img ) .'?height=40" style="max-height:40px;max-width:100px;"  alt="[--]" />'; 
		if ( is_image( P_PHOTO . str_replace('/',DS,$img)) ){
			return 	'<img src="'. U_PHOTO . $img .'?height=40" style="max-height:40px;max-width:100px;"  alt="[--]" />'; 
		}else{
			return '<i class="icon-picture" title="'.$img.'"></i>';
		}
	}
	function sortable($k,$v){
		return 	'<input type="number" data-name="'. $k .'" value="'.$v.'" style="width:50px;" class="sortable_field" />'; 
	}
	function initViewList(){ 
			if ($this->initializedViewList) return ; 	$this->initializedViewList = true ; //protect
			if (count($this->image )){ $this->_r_imgp = $this->image  ; }
			
			foreach($this->formFields as $fld){
				if ($fld['type']=='sort')	{
					$this->_sort[] = 	$fld['name'] ;
				}
			}
	}
	
	
	function getFilterOrderParam(){
		$order = post('order') ? post('order') : get('order');
		if ($order && is_array($order) && count($order)){
			foreach($order as $k=>$v){
				if($v){	$this->order_val[$k] =  $v;	}
			}
		}
		$filter = post('filter') ? post('filter') : get('filter');

		if ($filter && is_array($filter) && count($filter)){
			foreach($filter as $k=>$v){
				if ($v){$this->filter_val[$k] = $v;	}
			}
		}
	}
	
}

?>
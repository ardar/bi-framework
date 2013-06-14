<?php
class UIHtmlTable extends UIWidget
{
    const TABLETR_EXT = "TABLETR_EXT";
    
	public $viewFile = "widgets/uiHtmlTable.php";
    
	public $_fields = array();
	public $_navbar;
	public $_tabBar;
	public $_headbar;
	public $_searcher;
	public $_quickSearcher;
	public $_footer;
	public $_totalrowcount;
	public $_rows = array();
	public $_pager;
	public $_class;
	public $_noform = false;
	public $_params;
	public $_isAllFieldsPostback = false;
	public static $_table_count = 0;
	
	public function __construct($title, $id=null, $class='', $params=array())
	{
		$this->_title = $title;
		$this->_class = $class;
		$this->_id = $this->_name = $id ? $id : 'fwtable_'.(++self::$_table_count);
		$this->_params = $params;
	}
	
	public function ChangeTemplete($templete)
	{
		$this->viewFile = $templete;
	}
	
	public function GetFields()
	{
		return $this->_fields;
	}
	
	public function GetData()
	{
		return $this->_rows;
	}
	
	public function GetNavbar()
	{
		return $this->_navbar;
	}
	
	public function GetToolbar()
	{
		return $this->_toolbar;
	}
	
	public function BindHeadbar($item)
	{
		$this->_headbar .= $item;
		return $this;
	}
	
	public function BindQuickSearch($field='search', $hint='输入查询关键字',
			$buttonLabel='查找',$inputType='text',$defaultValue=null, $inputProperties=null)
	{
		$this->_quickSearcher[] = array(
				'field'=>$field,'hint'=>$hint,
				'label'=>$buttonLabel, 'type'=>$inputType,
				'properties'=>$inputProperties,
				'defaultvalue'=>$defaultValue,
				);
		return $this;
	}
	
	protected function buildQuickSearcher()
	{
		$label = "查找";
		$ret = '';
		if($this->_quickSearcher)
		{
			foreach($this->_quickSearcher as $item)
			{
				$hint = $item['hint'];
				$field = $item['field'];
				$type = $item['type'];
				$properties = $item['properties'];
				$label = $item['label'] ? $item['label'] : $label;
				$searchValue = 
						Fw::app()->GetRequest()->GetInput($field)  
							? Fw::app()->GetRequest()->GetInput($field)
							:$item['defaultvalue'];
				$ret.="
				<input class=\"input-medium\" type=\"$type\" placeholder=\"$hint\" 
				name='$field' value='$searchValue'>\n
				";
			}
			$controller = Fw::app()->GetRequest()->GetInput(FIELD_CONTROLLER);
			$action = Fw::app()->GetRequest()->GetInput(FIELD_ACTION);
// 			foreach($gets as $field=>$val)
// 			{
// 				if($field!==null)
// 				{
// 					$ret.=Fw::Html()->Input('',$field,$val,'hidden');
// 				}
// 			}
			$ret.= "<button class=\"btn btn-primary\" type=\"submit\">$label</button>\n";
			$ret.= "<input type=\"hidden\" name=\"controller\" value=\"{$controller}\">\n";
			$ret.= "<input type=\"hidden\" name=\"action\" value=\"{$action}\">\n";
		}
		return $ret;
	}
	
	public function BindAdvSearcher($field, $label, $defValue='', 
			$edittype='text', $rules=null, $properties=null)
	{
		$formElement = new UIFormElement($field, $label, $edittype, $defValue, $rules, $properties);
		$this->_searcher[] = $formElement;
		return $this;
	}
	
	public function BindTabbar($item)
	{
		$this->_tabBar .= $item;
		return $this;
	}
	
	public function BindNavbar($item)
	{
		$this->_navbar .= $item;
		return $this;
	}
	
	public function BindFootbar($item)
	{
		$this->_footer .= $item;
		return $this;
	}
	
	public function BindFieldEx($field,$title='',$type='',$style='',$sorttype='', $params='',$align='left')
	{
		$this->_fields[] = array('title'=>$title,'field'=>$field,'type'=>$type,
				'style'=>$style,'sorttype'=>$sorttype,'align'=>$align,'params'=>$params);
		return $this;
	}
	
	public function BindField($field,$title='',$type='',$sorttype='',$align='left',$properties=null)
	{
		$this->_fields[] = array('title'=>$title,'field'=>$field,'type'=>$type,
				'sorttype'=>$sorttype,'align'=>$align,'params'=>$properties);
		return $this;
	}
	
	public function BindData(&$rows, $datatype='dataset')
	{
		$this->_rows = $rows ? $rows : array();
		return $this;
	}
	
	public function BindParams($params)
	{
		$this->_params = array_merge($this->_params, $params);
		return $this;
	}
	
	public function BindParam($field, $value)
	{
		$this->_params[$field] = $value;
		return $this;
	}
	
	public function BindPager($totalcount, $perpage=10, $currpage)
	{
		$this->_totalrowcount = $totalcount;
		$this->_page = '';//TODO: ::Pager($totalcount, $perpage, $currpage);
		return $this;
	}
	
	public function ParseTplValue()
	{
	    $table = array();
		$table['_class'] = $this->_params['class'] ? $this->_params['class'] : 'data_table';
		$table['_width'] = $this->_params['width'] ? $this->_params['width'] : '100%';
		$table['_params'] = $this->_params;
		$fieldcount = 0;
		$table['_fields'] = array();
		foreach ($this->_fields as $fieldkey=>$field) 
		{
			$fieldId = $field['params']['fieldname']
				?$field['params']['fieldname'] : $field['field'];
			$field['puretitle'] = $field['title'];
			switch ($field['type'])
			{
			    case 'checkbool':
			    $field['title'] = "<label><INPUT id='chkAll_".$fieldId."' onclick=\"CheckAllBool(this.form,'".$fieldId."[]')\" type=checkbox value=".$fieldId." name='chkAll_".$fieldId."[]'>"
				. $field['title'] ."</label>" ;
				break;
				case 'checkbox':
				$script = "function htmltable_checkall(name,ischeck){
					$(\"[name='\"+name+\"']\").attr('checked',ischeck);};\n";
				$script .= "function htmltable_checkone(name,ischeck){
					if(!ischeck)
					$('#CHECKALL_'+name).attr('checked',false);};\n";
				Fw::Html()->Header('js', $script);
				$field['title'] = "<INPUT id='CHECKALL_".$fieldId."'
				 		onclick=\"htmltable_checkall('{$fieldId}[]',this.checked)\" 
				 		type=checkbox value=".$fieldId." >";
				break;
				case 'hidden':
					continue;
				default:
				if ($field['sorttype']=='asc'||$field['sorttype']=='desc') 
				{
					$ico = "ico-menu";
					$sortfield='';
					foreach ($_GET as $key=>$val)
					{
						if ($key!='sortfield'&&$key!='sorttype') 
						{
							$sortfield .= "&$key=$val";
						}
						elseif ($key=='sorttype')
						{
						}
					}
					if ($_GET['sortfield']==$fieldId) 
					{							
						$field['sorttype'] = ($_GET['sorttype']=='desc')?'asc':'desc';

						$ico = ($_GET['sorttype']=='desc')?"icon-arrow-down":"icon-arrow-up";
					}
					$ico = "<i class='$ico'></i>";					
					$sortfield = '?sortfield='.$fieldId.'&sorttype='
						.$field['sorttype'] .$sortfield;
					$field['title'] = Fw::Html()->Link('', $field['title'].$ico,$sortfield);
				}
			}
			$fieldcount ++;
			$table['_fields'][$fieldkey] = $field;
		}
		$hiddenval = '';
		foreach ($this->_rows as $rowkey=>$rs) 
		{
		    $table['_rows'][$rowkey]=array();
		    $table['_rows'][$rowkey]['TABLETR_EXT'] = $rs['TABLETR_EXT'];
			foreach ($this->_fields as $fieldkey=>$field)
			{
				$fieldId = $field['params']['fieldname']
					?$field['params']['fieldname'] : $field['field'];
				$fieldName = $field['params']['fieldname']
					? str_replace('[]',"[{$rowkey}]", $fieldId) : $fieldId.'[]';
				$fieldValue = $rs[$field['field']];
				switch ($field['type'])
				{
					case 'hidden':
					$hiddenval .= Fw::Html()->Input($fieldId.$rowkey, $fieldName,$fieldValue,'hidden');
					continue;
					case 'checkbox':
					$val = "<input id='check_{$fieldId}_{$fieldkey}' 
    					onClick=\"htmltable_checkone('".$fieldId."',this.checked)\" 
    					type='checkbox' value='".$fieldValue."' 
    					name='$fieldName'>\n";
					break;
					case 'checkbool':
					$checkid = "checkhidden_".$fieldId."_".$rowkey;
					$val = "<input id='".$fieldId."' 
    					onClick=\"unselectall(this.form,'$fieldName');if(this.checked)$checkid.value=1;else $checkid.value=0;\" 
    					type='checkbox' value='1' ".($fieldValue?"checked":'')." 
    					name='checkbool_$fieldName'>\n";
				    $val .="<input type=hidden id='".$checkid."' 
				    	name='$fieldName' value='".($fieldValue?"1":'0')."'>\n";
					break;
					case 'link':
					$val = "<a href='".$fieldValue."' >".$fieldId."</a>\n";
					break;
        			case 'date_picker':
    				$val = Fw::Html()->DatePicker($fieldId."_".$rowkey, $fieldName, $fieldValue, '', 'readonly '.$field['ext']);
    				break;
        			case 'time_picker':
    				$val = Fw::Html()->TimePicker($fieldId."_".$rowkey,$fieldName, $fieldValue, '', 'readonly '.$field['ext']);
    				break;
        			case 'datetime_picker':
    				$val = Fw::Html()->DatetimePicker($fieldId."_".$rowkey,$fieldName, $fieldValue, '', 'readonly '.$field['ext']);
    				break;
        			case 'datehour_picker':
    				$val = Fw::Html()->DateHourPicker($fieldId."_".$rowkey,$fieldName, $fieldValue, '', 'readonly '.$field['ext']);
    				break;
					case 'text':
						$field['params']['style'].= "width:100%;";
						$val = Fw::Html()->Input($fieldId, $fieldName,$fieldValue,'text',
							$field['params']);
						break;
					case 'number':
					$val = Fw::Html()->Input($fieldName,$fieldValue,'number',$field['ext']." style='width:100%'");
					break;
					case 'textarea':
					$val = Fw::Html()->Input($fieldName,$fieldValue,'textarea',$field['ext']." style='width:100%'");
					break;
					case 'select':
					$val = Fw::Html()->ListBox($fieldName,$fieldValue,$field['ext']['options'],$field['ext']['optionfield'],$field['ext']['optiontext'],$field['ext']['nulltitle']);
					break;
					case 'image':
					$val = Fw::Html()->Image($fieldValue,$field['ext']['link'],$field['ext']['target'],$field['ext']['width'],$field['ext']['height']);
					break;
					case 'cp':
						//controller,action,paramfield,?,class,paramname
						$val = "";
						if ($field['params'])
						{
							foreach ($field['params'] as $btn=>$params)
							{
								//$params[3] need verify
								$extclick = '';
								if($params[3])
								{
									$extclick = "onclick=\"if(!confirm('{$params[3]}'))return false;\"";
								}
								if(is_array($params))
								{
								$urlparamname = $params[5] ? $params[5] : $params[2];
								$controller = $params[0] ? $params[0] : $_REQUEST[FIELD_CONTROLLER];
								$url = urlhelper('action', $controller, $params[1], 
										"&$urlparamname=".$rs[$params[2]]);
								}
								else
								{
									$url = $params;
								}
								$class = $params[4] ? $params[4] : 'btn btn-mini';
								$val .= " ".Fw::Html()->Link('',$btn, $url, '',$class, $extclick);
							}
							//$table['_fields'][$fieldkey]['align'] = 'center';
						}
					break;
					case 'buttons':
						$val = "";
						if ($field['params'])
						{
							foreach ($field['params'] as $btn=>$params)
							{
								if(is_array($params))
								{
								$urlparamname = $params['paramname'] ? $params['paramname'] : $params['field'];
								$controller = $params['controller'] ? $params['controller'] : $_REQUEST[FIELD_CONTROLLER];
								$action = $params['action'];
								$url = urlhelper('action', $controller, $action, "&$urlparamname=".$rs[$params[2]]);
								}
								else
								{
									$url = $params;
								}
								$val .= " ".Fw::Html()->Link('', $btn, $url, '','btn btn-mini btn-info', '');
							}
							//$table['_fields'][$fieldkey]['align'] = 'center';
						}
					break;
					default:
					$val = Fw::Html()->HtmlData($fieldValue,$field['type']);
					if($this->_isAllFieldsPostback)
					{
						$val.=Fw::Html()->Input($fieldId, $fieldName, $fieldValue, 'hidden');
					}
					break;
				}
				$table['_rows'][$rowkey][$fieldkey] = $val;
			}
		}
		$table['_searcher'] = $this->_searcher;
		$table['_hidden'] = $hiddenval;
		$table['_fieldcount'] = $fieldcount;
		$table['_id'] = $this->_id;
		$table['_title'] = $this->_title;
		$table['_pager'] = $this->_pager;
		$table['_headbar'] = $this->_headbar;
		$table['_tabBar'] = $this->_tabBar;
		$table['_footer'] = $this->_footer;
		$table['_noform'] = $this->_noform;
		
		// QuickSearch\
		$table['_quickSearcher'] = $this->buildQuickSearcher();
		// AdvSearchDialog
		$searchDialog = Fw::app()->getView()->GetOutput(
				'widgets/uiAdvSearchDialog.php',array('fields'=>$this->_searcher));
		Fw::Html()->Header('html', $searchDialog);
		return $table;
	}
	
	public function GetBody()
	{
		try
		{
			$table = $this->ParseTplValue();
			$view = Fw::app()->GetView();
			return $view->getOutput($this->viewFile, array('widget'=>$table));
		}
		catch(ErrorException $e)
		{
			TRACE($e);
		}
		catch(Exception $e)
		{
			TRACE($e);
		}
	}
	
	public function __toString()
	{
		return $this->GetBody();
	}
}
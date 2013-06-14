<?php

class UIFormElement extends UIElement{
	const StatusInit=0;
	const StatusValid = 1;
	const StatusError = 2;
	
	public $name;
	public $ori_value;
	public $value;
	public $type;
	public $element;
	public $properties;
	public $hint;
	public $status = self::StatusInit;
	public $errors;
	public $rules = array();
	
	public function __construct($id, $label, $type, $value, $rules=null, $properties=null, $parent=null)
	{
		$labelarr = explode('|', $label);
		$label = $labelarr[0];
		$hint = $labelarr[1];
		parent::__construct($id, $label, $parent);
		$this->id = $id;
		$this->name = $properties['name']?$properties['name']:$id;
		$this->value = $this->ori_value = $value;
		$this->type = $type;
		$this->label = $label;
		$this->hint = $hint?$hint:$properties['hint'];
		$properties['hint']=null;
		$this->properties = $properties;
		if(is_array($rules))
		{
			$this->rules = $rules;
		}
		elseif($rules!=null)
		{
			$this->rules = explode('|', $rules);
		}
	}
	
	public function AssignValue($value)
	{
		$this->value = $value;
	}
	
	public function GetValue()
	{
		return $this->value;
	}
	
	public function HasRule($rule)
	{
		return in_array(strtolower($rule), $this->rules);
	}
	
	public function Validate($value)
	{
		//echo "validating ".$this->id." value:$value <BR>\n";
		try 
		{
			$value =  IValidator::GetInstance()->Validate($this->label, $value, $this->rules);
			if(!$this->HasRule('skip'))
			{
				$this->value = $value;
			}
		}
		catch(ArgumentException $e)
		{
			//trace($e);
			$this->errors[] = $e->getMessage();
		}
		
		$this->status = $this->errors==null ? self::StatusValid : self::StatusError;
		return $this->status == self::StatusValid;
	}
	
	protected function getPlainValue($val)
	{
		return "<div style='min-height:28px;padding-top:5px;'>".$val."</div>";
	}
	
	public function SupportTypes()
	{
		return array('','ipstr','datetime','duration','ip','label','bool','html',
				'select','radiolist','radioset','checklist','checkset',
				'checkbool','checkbox','textarea','htmledit',
				'datepicker','colorpicker','attachimage',
				'ip_input','ipstr_input','text','number','password','email','hidden',
				'datatable');
	}
	
	public function GetBody()
	{
		$html = Fw::html();
		switch($this->type)
		{
			case '':
			case 'ipstr':
				return $this->getPlainValue($this->value);
			case 'datetime':
				$val = $this->value>0 ? date("Y-m-d H:i:s", $this->value) : '-';
				return $this->getPlainValue($val);
			case 'duration':
				$val = OpenDev::GetDurationDesc($this->value);
				return $this->getPlainValue($val);
			case 'ip':
				$val = $this->value!==null ? long2ip($this->value) : '';
				return $this->getPlainValue($val);
			case 'label':
				$class = $this->properties['class'];
				$val = "<label class='$class'>{$this->value}</label>";
				return $this->getPlainValue($val);
			case 'bool':
				$val = $this->value? "<i class='ico-checkmark'></i>" : "<i class='ico-cancel-2'></i>";
				return $this->getPlainValue($val);
				break;
			case 'html':
				return $this->value;
				break;
			case 'select':
				return $html->Select($this->id, $this->name, $this->value, $this->properties);
				break;
			case 'radiolist':
			case 'radioset':
				return $html->Radios($this->id, $this->name, $this->value, $this->properties);
			case 'checklist':
			case 'checkset':
				return $html->Checkboxes($this->id, $this->name, $this->value, $this->properties);
			case 'checkbool':
				$this->properties['checked'] = $this->value?true:false;
				return $html->Checkbox($this->id, $this->name, 1, $this->properties);
				break;
			case 'checkbox':
				return $html->Checkbox($this->id, $this->name, $this->value, $this->properties);
				break;
			case 'textarea':
				return $html->TextArea($this->id, $this->name, $this->value, $this->properties);
				break;
			case 'htmledit':
				return $html->HtmlEdit($this->id, $this->name, $this->value, $this->properties);
				break;
			case 'datepicker':
				return $html->DatePicker($this->id, $this->name, $this->value,$this->properties);
				break;
			case 'colorpicker':
				return $html->ColorPicker($this->id, $this->name, $this->value, $this->properties);
				break;
			case 'attachimage':
				return Fw::Html()->FileInput($this->id, $this->name, $this->value,$this->properties);
			case 'ip_input':
				$value = $this->value!==null ? long2ip($this->value) : '';
				$this->properties['placeholder'] = '格式：xxx.xxx.xxx.xxx';
				return $html->Input($this->id, $this->name, $value, 'text', $this->properties);
			case 'ipstr_input':
				$this->properties['placeholder'] = '格式：xxx.xxx.xxx.xxx';
				return $html->Input($this->id, $this->name, $this->value, 'text', $this->properties);
				break;
			case 'text':
				return $html->Input($this->id, $this->name, $this->value, 'text', $this->properties);
				break;
			case 'password':
			case 'number':
			case 'hidden':
				return $html->Input($this->id, $this->name, $this->value, $this->type, $this->properties);
				break;
			case 'datatable':
				return $this->dataTableField($this->id, $this->name, 
					$this->value, $this->properties);
				break;
			case 'file':
				return $html->FileInput($this->id, $this->name, $this->value,$this->properties);
			
			case 'multifile':
				return $html->MultiFileInput($this->id, $this->name, $this->value,$this->properties);
				
			case 'autocomplete':				
				/*
				HtmlHelper::AddHeader('jsfile', "ui/autocomplete.js");
				return "<span id='autocompletebox_$this->id' >"
				."<input name='".$this->name."' type='$this->type'  class='inputbox' id='".$this->id."' placeholder='$this->_placeholder' value='".$this->value."'></span>\n"
						."<script language='javascript'>InitAutoComplete('$this->id', '$this->_extval');</script>";
				*/
				break;
			default:
				throw new FwException("Unknown FormField type {$this->type}");
				break;
		}
		return $this->GetId();
	}
	
	protected function dataTableField($id, $name, $value, $properties)
	{
		//trace($properties);
		$table = new HtmlTable($name, $id,
				'',$properties['properties']);
		$table->_noform = true;
		$table->_isAllFieldsPostback = true;
		if($properties['fields'] && is_array($properties['fields']))
		{
			foreach($properties['fields'] as $field)
			{
				//field, fieldname, type, sorttype,align,properties
				$fieldid = $field[0];
				$field[5]['fieldname'] = "{$id}[][{$field[0]}]";
				$table->BindField($fieldid,$field[1],$field[2],$field[3],
						$field[4],$field[5]);
			}
		}
		$table->BindData($value);
		return $table->GetBody();
	}
	
	protected function BuhuoTable()
	{
		return array(
			'properties'=>array(),//tableproperteis
			'fields'=>array(
				//field, fieldname, type, sorttype,align,properties
				array('FSCID','商品编号'),		
				array('FSBARCODE','条码'),		
				array('FSCDA','货号'),		
				array('FSNAME','商品'),		
				array('stock','当前库存'),		
				array('ordersugguest','建议订货量'),	
				array('order','实际订货量','text'),	
				array('FSUS_NAME','计量单位'),	
			),
			'label'=>'补货量表格',
		);
	}
}
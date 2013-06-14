<?php

class UIForm extends UIWidget{
	const StatusInit=0;
	const StatusValid = 1;
	const StatusError = 2;
	
	public static $classMap = array(
			'input'=>'UIInput',
			'radios'=>'UIRadioSet',
			'checks'=>'UICheckSet',
			'date_picker'=>'UIDataPicker',
			'time_picker'=>'UITimePicker',
			'datetime_picker'=>'UIDateTimePicker',
			);

	public $viewFile = 'uiForm.php';
	public $postUrl = '';
	public $postMethod = 'POST';
	public $encryptType = "multipart/form-data";
	
	public $elements = array();
	public $buttons = array();
	public $model = null;
	public $successMessage='表单提交成功';
	public $description='';
	public $errors = array();
	public $groups = array();
	public $returnUrl = '';
	public $status = self::StatusInit;
	public $properties = null;
	public $tabBar = '';
	public $labelTags = '';
	
	public function __construct($id, $label, $properties=null, $parent=null)
	{
		parent::__construct($id, $label, $parent);
		$this->properties = $properties;
		//$this->returnUrl = AppHost::GetApp()->GetEnv('referer');
		$this->BindGroup('DEFAULT', '', true);
		
	}
	
	public function End()
	{
		if($this->properties['tabble'])
		{
			$this->viewFile = "uiForm_Tabble.php";
		}
		return parent::End();
	}
	
	public function BindTabBar($tabBar)
	{
		$this->tabBar .= $tabBar;
	}
	
	public function IsPostback()
	{
		return ($_POST['_fw_formid'] == $this->GetId());
	}

	/**
	 * Binds a form group which can be collapsed.
	 * @param string $groupid
	 * @param string $label
	 * @param bool $isshow  whether displaed by default.
	 */
	public function BindGroup($groupid, $label, $isshow=false)
	{
		if(!$groupid)
		{
			$groupid='DEFAULT';
		}
		$this->groups[$groupid] = array('id'=>$groupid, 'label'=>$label, 'isshow'=>$isshow);
		return $this;
	}
	
	protected function parseGroupId($group_or_field_id)
	{
		if(strpos($group_or_field_id, '/')>0)
		{
			$arr = explode('/', $group_or_field_id);
			$groupid = $arr[0];
		}
		if(!$groupid)
		{
			$groupid='DEFAULT';
		}
		return $groupid;
	}
	
	public function parseFieldId($group_or_field_id)
	{
		if(strpos($group_or_field_id, '/')>0)
		{
			$arr = explode('/', $group_or_field_id);
			$fieldId = $arr[1];
			return $fieldId;
		}
		else
		{
			return $group_or_field_id;
		}
	}
	
	public function BindElement($groupid, IUIFormElement $element)
	{
		$groupId = $this->parseGroupId($groupid);
		$this->elements[$groupId][$element->GetId()] = $element;
		return $this;
	}
	
	public function BindFieldEx($id, $label, $value, $type='', $rules=null, $properties=null)
	{
		$groupId = $this->parseGroupId($id);
		$fieldId = $this->parseFieldId($id);
		
		$element = new UIFormElement($fieldId, $label, $type, $value, $rules, $properties, null);
		//echo "$groupId - $fieldId<BR>";
		
		$this->elements[$groupId][$fieldId] = $element;
		return $this;
	}
	
	/**
	 * 
	 * @param string $id	the field id or field with group id as format GroupId/FieldId
	 * @param string $label
	 * @param string $type
	 * @param array $properties  properties including isrequired(bool), width(ps:14%/300px), hint:string, 
	 */
	public function BindField($id, $label, $type='', $rules=null, $properties=null)
	{
		$groupId = $this->parseGroupId($id);
		$fieldId = $this->parseFieldId($id);
		if($properties['value']===null )
		{
			$value = is_array($this->model) ? $this->model[$fieldId] : $this->model->$fieldId;
		}
		else
		{
			$value = $properties['value'];
		}
		
		$element = new UIFormElement($fieldId, $label, $type, $value, $rules, $properties, null);
		//echo "$groupId - $fieldId<BR>";
		
		$this->elements[$groupId][$fieldId] = $element;
		return $this;
	}
	
	public function BindButton($button)
	{
		$this->buttons[] = $button;
		return $this;
	}
	
	public function BindTags($tags)
	{
		$this->labelTags .= $tags;
	}
	
	public function BindModel(&$model)
	{
		$this->model = &$model;
		return $this;
	}
	
	public function BindDescription($desc)
	{
		$this->description = $desc;
		return $this;
	}
	
	public function BindSuccess($desc)
	{
		$this->status = self::StatusValid;
		$this->successMessage = $desc;
		return $this;
	}
	
	public function BindError($id, $error)
	{
		if($id)
		{
			$groupId = $this->parseGroupId($id);
			$fieldId = $this->parseFieldId($id);
			$element = $this->elements[$groupId][$fieldId];
			$this->$element[$groupId][$id]->errors[] = $error;
		}
		else
		{
			$this->errors = $error;
		}
		$this->status = self::StatusError;
		return $this;
	}
	
	public function Validate(IRequest $request)
	{
		//print_r($this->elements);
		foreach($this->elements as $elementid => $element)
		{
			/**
			 * @var $element UIFormElement
			 */
			if($element instanceof UIFormElement)
			{
				if($element->type=='')
				{
					continue;
				}
				$fieldname = $element->GetName();
				$value = $request->GetInput($fieldname);
				$result = $element->Validate($value);
				if(!$result)
				{
					$this->status = self::StatusError;
				}
				if( $element->HasRule('skip'))
				{
					continue;
				}
				if(is_array($this->model))
				{
					$this->model[$fieldname] = $element->GetValue();
				}
				else
				{
					$this->model->$fieldname = $element->GetValue();
				}
			}
			elseif(is_array($element))
			{
				foreach($element as $subElementId => $subElement)
				{
					if($subElement instanceof UIFormElement)
					{
						if($subElement->type=='')
						{
							continue;
						}
						$fieldname = $subElement->name;
						$result = $subElement->Validate($request->GetInput($fieldname));
						if(!$result)
						{
							$this->status = self::StatusError;
							//$this->errors = array_merge($this->errors, $subElement->errors);
						}
						if( $subElement->HasRule('skip'))
						{
							continue;
						}
						if(is_array($this->model))
						{
							$this->model[$fieldname] = $subElement->GetValue();
						}
						else
						{
							$this->model->$fieldname = $subElement->GetValue();
						}
					}
				}
			}
		}
		
		return $this->status != self::StatusError;
	}
	
}
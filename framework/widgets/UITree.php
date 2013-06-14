<?php
class UITree extends UIWidget
{
	public $viewFile = 'uiTree.php';
	
	public $isSimpleData = true;
	public $isAsync = false;
	public $controller = null;
	public $action = null;
	public $data = null;
	public $jsonData = null;
	public $value = null;
	public $checkEnabled = false;
	
	
	public function __construct($id, $selected_value, $properties=null, $parent=null)
	{
		parent::__construct($id, null, $parent);
		$this->value = $selected_value;
		$this->isSimpleData = $properties['isSimpleData'] ? $properties['isSimpleData'] : $this->isSimpleData;
		$this->isAsync = $properties['isAsync'] ? $properties['isAsync'] : $this->isAsync;
		$this->checkEnabled = $properties['checkEnabled'] ? $properties['checkEnabled'] : $this->checkEnabled;
	}
	
	public function AddList($listData, $idField, $nameField, $parentField=null, $paramFields=null)
	{
		foreach($listData as $rs)
		{
			$pId = $parentField ? $rs[$parentField] : null;
			$params=null;
			if(is_array($paramFields))
			{
				foreach($paramFields as $key=>$param)
				{
					if(strpos($param,'field:')===0)
					{
						$param = substr($param, strlen('field:'));
						$params[$key] = $rs[$param];
					}
					else
					{
						$params[$key] = $param;
					}
				}
			}
			$this->AddItem($rs[$idField], $rs[$nameField], $pId, $params);
		}
	}
	
	public function AddItem($id, $name, $pId=null, $params=null)
	{
		if($this->isSimpleData)
		{
			$this->data[$id] = array('id'=>$id, 'name'=>$name);
			if($pId!==null)
			{
				$this->data[$id]['pId'] = $pId;
			}
			if(is_array($params))
			{
				foreach($params as $key=>$param)
				{
					$this->data[$id][$key] = $param;
				}
			}
		}
		else
		{
			if($pId>0)
			{
				$this->data[$pId]['children'][$id] = array('name'=>$name, 'id'=>$id);
				if(is_array($params))
				{
					foreach($params as $key=>$param)
					{
						$this->data[$pId]['children'][$id][$key] = $param;
					}
				}
			}
			else
			{
				$this->data[$id] = array('name'=>$name, 'id'=>$id);
				if(is_array($params))
				{
					foreach($params as $key=>$param)
					{
						$this->data[$id][$key] = $param;
					}
				}
			}
		}
	}
	
	private function toJsData($data)
	{
		$result = "[\n";
		if($this->isSimpleData && $data)
		{
			//{id:1, pId:0, name: "父节点1"},
			foreach($data as $rs)
			{
				$result .= "{";
				foreach($rs as $fieldname=>$fieldval)
				{
					if(is_array($fieldval))
					{
						$result .= $this->toJsData($fieldval);
					}
					elseif(is_numeric($fieldval) || is_bool($fieldval))
					{
						$result .= "$fieldname:$fieldval,";
					}
					else
					{
						$result .= "$fieldname:'$fieldval',";
					}
				}
				if($this->checkEnabled && !isset($rs['checked']))
				{
					if($rs['id']==$this->value 
							|| (is_array($this->value) && in_array($rs['id'], $this->value)))
					{
						$result .= "checked:true,";
					}
				}
				$result .= "},\n";
			}
		}
		else
		{
			if($data)
			{
				//{ name:"父节点1 - 展开", open:true, children: [
				foreach($data as $rs)
				{
					$result .= "{";
					foreach($rs as $fieldname=>$fieldval)
					{
						if(is_array($fieldval))
						{
							$result .= $this->toJsData($fieldval);
						}
						elseif(is_numeric($fieldval))
						{
							$result .= "$fieldname:$fieldval,";
						}
						else
						{
							$result .= "$fieldname:'$fieldval',";
						}
					}
					$result .= "},\n";
				}
			}
		}
		$result .= ']';
		return $result;
	}
	
	public function Begin()
	{
		ob_start();
	}
	
	public function End()
	{
		$content = ob_get_contents();
		ob_end_clean();
		$this->jsonData = $this->toJsData($this->data);
		$view = Fw::GetApp()->GetView('native');
		//TRACE($this->childs);
		$this->render($this->viewFile, array('content'=>$content));
		return $this;
	}
}
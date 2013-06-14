<?php
class UIElement 
{
	public $id;
	public $label;
	public $body;
	public $parent;
	/**
	 * @var IUIElement
	 */
	public $childs = null;
	public $initScript;
	
	public function __construct($id, $label, $parent=null)
	{
		$this->id = $id;
		$this->label = $label;
		$this->parent = $parent;
	}
	
	public function GetId()
	{
		return $this->id;
	}
	public function GetType($type=__CLASS__)
	{
		return $this->type;
	}
	public function GetBody()
	{
		return $this->body;
	}
	
	public function GetChilds()
	{
		return $this->childs;
	}
	public function AddChild(IUIElement $child)
	{
		$this->childs[$child->GetId()] = $child;
		return $this;
	}
	/**
	 * @return IUIElement
	*/
	public function GetParent()
	{
		return $this->parent;
	}
	
	public function __toString()
	{
		return $this->GetBody();
	}
}
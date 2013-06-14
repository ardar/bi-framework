<?php
class UIAccordion extends UIWidget
{
	public $viewFile = "uiAccordion.php";
	public $width = '100%';
	public $heightStyle = 'fill';
	public $selectedIndex = 0;
	
	public function __construct($id, $properties=null, $parent=null)
	{
		parent::__construct($id, null, $parent);
		$this->width = $properties['width'] ? $properties['width'] : $this->width;
		$this->heightStyle = $properties['heightStyle'] ? $properties['heightStyle'] : $this->heightStyle;
		$this->selectedIndex = $properties['selectedIndex'] ? $properties['selectedIndex'] : $this->selectedIndex;
	}
	
	public function Select($groupId)
	{
		$index = 0;
		foreach($this->childs as $childId=>$child)
		{
			if($childId==$groupId)
			{
				$this->selectedIndex = $index;
				break;
			}
			$index++;
		}
		return $index;
	}
	
	public function AddGroup($groupId, $groupLabel, $content)
	{
		if($content instanceof UIElement)
		{
			$child = new UIElement($groupId, $groupLabel, null);
			$child ->body = $content->GetBody();
			$child ->label = $groupLabel;
			
			return parent::AddChild($child);
		}
		else
		{
			$child = new UIElement($groupId, $groupLabel, null);
			$child ->body = $content;
			$child ->label = $groupLabel;
			
			return parent::AddChild($child);
		}
	}
}
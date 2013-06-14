<?php

class UIShortCutPanel extends UIPanel
{
	public $shortCuts = array();


	public function renderContent()
	{
		//echo $this->GetId()."renderContent\n";
		echo "<div style='width:100%;text-align:center'>";
			foreach($this->shortCuts as $rs)
			{
					$shortcut = new UIShortCut();
					$shortcut->label = $rs['label'];
					$shortcut->icon = $rs['icon'];
					$shortcut->badge = $rs['badge'];
					$shortcut->badgeClass = $rs['badgeClass'];
					$shortcut->labelClass = $rs['labelClass'];
					$shortcut->type = $rs['type'] ? $rs['type'] : $shortcut->type;
					$shortcut->onclick = $rs['onclick'];
					$shortcut->Begin();
					$shortcut->End();
			}
		echo "</div>";
	}
}
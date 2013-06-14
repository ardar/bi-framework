<?php
class AppMenu
{
	public function toArray()
	{
		$rootlist = array();
		$totalcount = 0;
		//$filter_options[] = array('field'=>'hidden','value'=>'0','op'=>'=');
		$roots = Fw::app()->getModule()->getMenu();
		$acc = Fw::app()->getUser();
		
		if ($roots)
		{
			$id = 0;
			foreach ($roots as $item)
			{
				$root = array();
				$root['menuid'] = $id++;
				$root['image'] = $item['menu'][0];
				$root['title'] = $item['menu'][1];
				$root['url'] = $item['menu'][2];
				$root['module'] = $item['menu'][3];
				$root['showflag'] = 1;
				if ( $root['module'] && !$acc->hasPrivilege($root['module']))
				{
					continue;
				}
				$sublist = array();
				if($item['sub'])
				{
					foreach ($item['sub'] as $subitem)
					{
						$sub = array();
						$sub['menuid'] = $id++;
						$sub['image'] = $subitem[0];
						$sub['title'] = $subitem[1];
						$sub['url'] = $subitem[2];
						$sub['module'] = $subitem[3];
						$sub['showflag'] = 1;
							
						if ( $sub['module'] && !$this->_hasSubPrivilege($sub['module']) )
							continue;
						$sublist[] = $sub;
					}
				}
				if (count($sublist)==0) {
					//continue;
				}
				$root['subs'] = $sublist;
				$rootlist[] = $root;
			}
		}
		return $rootlist;
	}
}
<?php

static $EnumUrlType = array(
	'index'		=>	'',
	'home'		=>	'index.php',
	'ucp'		=>	'index.php',
	'frame'		=>	'index.php',
	'misc'		=>	'misc.php?proc={1}{2}{3}{4}{5}',
	'confirmcode'		=>	'index.php?controller=Account&action=code',
	'login'		=>	'index.php?controller=Site&action=login',
	'logout'		=>	'index.php?controller=Site&action=logout',
	'register'		=>	'index.php?controller=Site&action=register',
	'profile'		=>	'index.php?controller=User&action=profile&username={1}',
	'action'		=>	'index.php?controller={1}&action={2}{3}{4}{5}',
	'attach'		=>	'attach.php?fileid={1}',
//	'newscategory'	=>	'index.php?controller=NewsPortal&action=category&categoryid={1}&page={2}',
//	'newstopic'		=>	'index.php?controller=NewsPortal&action=topic&topicid={1}&page={2}',
//	'newsentity'	=>	'/index.php?controller=NewsPortal&action=entity&entityid={1}',
);

function urlhelper($type='other',$param1='',$param2='',$param3='',$param4='',$param5='')
{
	global $EnumUrlType;
	
	if(isset($EnumUrlType[$type])){
		$urlp = $EnumUrlType[$type];
		$url = str_replace(array('{1}','{2}','{3}','{4}','{5}'),array($param1,$param2,$param3,$param4,$param5),$urlp);
	}
	else{
		$url = $param1;
	}

	$usage = Fw::app()->GetRequest()->GetInput('usage');
	if($usage)
	{
		if(strpos($url, "&usage=")===false && strpos($url, "?usage=")===false)
		{
			if(strpos($url, "?")>=0)
			{
				$url.="&usage=$usage";
			}
			else
			{
				$url.="?usage=$usage";
			}
		}
	}
	
	return $url;
}
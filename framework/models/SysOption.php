<?php
/**
 * 系统配置参数和用户配置参数
 * @author ardar
 * @since 2.0
 */
class SysOption extends DataModel
{	
    const ScopeGlobal = "GLOBAL";
    const ScopeRole = "ROLE";
    const ScopeAccount = "ACCOUNT";
    
    public static $Scopes = array(
        self::ScopeGlobal => '全局',
        self::ScopeRole => '角色',
        self::ScopeAccount => '用户',
    );
    
	public static function getTable()
	{
		return TABLEPREFIX.'sys_option';
	}
	
	public static function getPrimaryKey()
	{
		return 'optionid';
	}
	
	public static function getModelName()
	{
		return '系统参数';
	}
	
	public static function getSysOptionData($module)
	{
		$filter[] = DBHelper::FilterOption('', 'scope', self::ScopeGlobal, '=');
		$filter[] = DBHelper::FilterOption('', 'module', $module, '=');
		$totalCount = 0;
		$rss =  DBHelper::GetJoinRecordSet(
				self::GetTable(), null, $filter, null, 0, -1, $totalCount);
		$result = array();
		if($rss)
		{
			foreach($rss as $rs)
			{
				$result[$rs['field']] = $rs;
			}
		}
		return $result;
	}
	
	public static function getAccOptionData($userid, $module)
	{
		$filter[] = DBHelper::FilterOption('', 'module', $module, '=');
		$filter[] = DBHelper::FilterOption('', 'scopeid', $userid, '=');
		$filter[] = DBHelper::FilterOption('', 'scope', self::ScopeAccount, '=');
		$totalCount = 0;
		$rss =  DBHelper::GetJoinRecordSet(
				self::GetTable(), null, $filter, null, 0, -1, $totalCount);
		//global $sql;echo $sql;
		$result = array();
		if($rss)
		{
			foreach($rss as $rs)
			{
				$result[$rs['field']] = $rs;
			}
		}
		return $result;
	}
}
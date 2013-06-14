<?php
/**
 * 系统基础角色类, 实现IRole接口
 * @author ardar
 * @since 2.3
 * @property string $role_id 
 * @property string $role_name 
 * @property string $role_desc
 * @property int $role_is_active 
 */
class Role extends DataModel implements IRole
{
	public static function getTable()
	{
		return TABLEPREFIX."role";
	}
	
	public static function getPrimaryKey()
	{
		return "role_id";
	}
	
	public static function getModelName()
	{
		return "角色";
	}
	
	/**
	 * 权限串分割符
	 * @var string
	 */
	const PRIVILEGE_DELIMITER = '/';
	const PRIVILEGE_ALL = "ALL";
	
	public function getPrivileges()
	{
		
	}
	
	/**
	 * 检查是否具有指定的权限项
	 * 权限项格式为  moduleRight/controllerRight/actionRight 三层结构
	 * 权限项为ALL时代表全部权限
	 * @return boolean
	 */
	public function hasPrivilege($privilegeItem)
	{
		$privileges = $this->GetPrivileges();
		// 全部权限
		if(in_array(self::PRIVILEGE_ALL, $privileges))
		{
			return true;
		}
		// 分别判断每个层次结构上的权限
		$parentPart = '';
		$items = explode(self::PRIVILEGE_DELIMITER, $privilegeItem);
		foreach ($items as $item)
		{
			$thisPart = $parentPart 
				? $parentPart.self::PRIVILEGE_DELIMITER.$item : $item;
			if(in_array($thisPart, $privileges))
			{
				return true;
			}
			$parentPart .= $parentPart 
				? self::PRIVILEGE_DELIMITER.$item : $item;
		}
		return false;
	}
	
}
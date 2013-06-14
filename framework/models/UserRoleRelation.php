<?php
/**
 * 用户角色关系
 * @author ardar
 * @property int $user_id
 * @property int @role_id
 */
class UserRoleRelation extends DataModel
{
	public static function getTable()
	{
		return TABLEPREFIX."user_role";
	}
	
	public static function getPrimaryKey()
	{
		return array("user_id", "role_id");
	}
	
	public static function getModelName()
	{
		return "用户角色";
	}
}
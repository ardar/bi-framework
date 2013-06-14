<?php
/**
 * 角色对象接口
 * @author ardar
 *        
 */
interface IRole {
	/**
	 * 获取角色Id
	 */
	public function getId();
	
	/**
	 * 获取角色名
	 */
	public function getName();
	
	/**
	 * 获取角色权限列表，列表项如带有下属权限项目， 以2维或多维数组表示。
	 */
	public function getPrivileges();
	
	/**
	 * 检查角色是否有该权限
	 * @param string $privilegeItem
	 * @return bool 返回是否具备权限
	 */
	public function hasPrivilege($privilegeItem);
}

?>
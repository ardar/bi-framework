<?php

/**
 * 系统用户接口
 * @author ardar
 *        
 */
interface IUser {
	/**
	 * 获取用户标识
	 */
	public function getId();
	
	/**
	 * 获取用户名
	 */
	public function getName();
	
	/**
	 * 获取所属角色列表接口
	 * @return array the IRole
	 */
	public function getRoles();
	
	/**
	 * 检查是否具有指定的权限项
	 * @param string $privilegeItem
	 */
	public function hasPrivilege($privilegeItem);
	
	/**
	 * 检查账号是否已经通过认证.
	 * @return bool
	*/
	public function isAuthenticated();
	
	/**
	 * 执行账号认证（读取Cookie并验证）
	 */
	public function authByCookie();
	
	/**
	 * 登陆验证
	 * @param string $identity 用户标识（用户名或邮箱等）
	 * @param string $password 用户密码（参数为明文）
	 * @param string $senario  登陆场景（web,webservice,client等）
	 */
	public function login($identity, $password, $senario='web');
	
	/**
	 * 退出登陆 
	 */
	public function logout();
	
	/**
	 * 获取属于某个模块的账号设置
	 * @param string $moduleId
	 * @param string $fieldname
	 * @return array 返回的账号设置队形等的调整。
	 */
	public function getAccOption($moduleId, $fieldname);
}

?>
<?php


/**
 * 当前运行时对象
 * @author ardar
 *        
 */
interface IRuntime {
	/**
	 * 获取当前模块Id
	 */
	public function getModuleId();
	
	/**
	 * 获取当前控制器Id
	 */
	public function getControllerId();
	
	/**
	 * 获取当前actionId
	 */
	public function getActionId();
	
	/**
	 * 获取当前时间戳
	 */
	public function getTimestamp();
	
	/**
	 * 获取当前客户端Ip
	 */
	public function getClientIp();
	
	/**
	 * 获取引用页
	 */
	public function getReferer();
}

?>
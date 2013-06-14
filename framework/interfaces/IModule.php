<?php
interface IModule
{
	/**
	 * 获取模块Id
	 */
	public function getId();
	
	/**
	 * 获取模块名称
	 */
    public function getName();
    
    /**
     * 初始化模块
     * @param array $options
     */
	public function init($options);
	
	/**
	 * 分发执行请求
	 * @param array $route  包含请求路由信息的数组
	 */
	public function dispatch($route);
	
	/**
	 * 获取模块依赖的其他模块列表
	 * @return array
	 */
	public function getDependModules();
	
	/**
	 * 获取模块菜单定义
	 * @return array
	 */
	public function getMenu();
	
	/**
	 * 获取模块包含的权限定义
	 * @return array
	 */
	public function getPriveleges();
	
	/**
	 * 显式读取类文件定义
	 * @param unknown $className
	 */
	public function loadClass($className);
	
	/**
	 * 获取当前控制器对象
	 * @return FwController
	 */
	public function getController();
	
	// SysOption methods.
	public function getSysOptionFields();
	public function getSysOptions();
	public function getSysOption($field);
	public function setSysOption($field, $fieldvalue);
	
	// AccOption methods.
	public function getAccOptionFields();
	public function getAccOptions($userid);
	public function getAccOption($userid, $field);
	public function setAccOption($userid, $field, $fieldvalue);
	
	/**
	 * 系统统一搜索接口
	 * @param 搜索关键词 $keyword
	 * @param 搜索参数 $parameters
	 * @return array 返回统一行使的搜索结果 SearchResult
	 */
	public function search($keyword, $parameters=null);

}
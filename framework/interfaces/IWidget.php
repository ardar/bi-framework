<?php
/**
 * 页面组件元素接口
 * @author ardar
 * @since 2.3
 */
interface IWidget 
{
	/**
	 * 获取元素唯一Id
	 */
	public function getId();
	/**
	 * 获取元素数据值
	 */
	public function getValue();
	/**
	 * 获取元素主题html展现代码
	 */
	public function getBody();
}
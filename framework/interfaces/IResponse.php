<?php
/**
 * 输出对象接口
 * @author ardar
 * @since 2.3
 */
interface IResponse
{
	/**
	 * 开始输出
	 */
	public function begin();

	/**
	 * 停止输出
	 */
	public function end();

	/**
	 * 获取当前已输出buffer
	 */
	public function getBuffer();
	
	/**
	 * 向Response对象输出结果或页面
	 * @param unknown $content
	 */
	public function send($content);
	
	/**
	 * 跳转
	 * @param string $location
	 */
	public function redirect($location);
}
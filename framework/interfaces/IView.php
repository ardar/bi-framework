<?php
interface IView
{
	/**
	 * 初始化视图对象
	 * @param array $setting
	 */
	public function init($setting);
	
	/**
	 * 清理视图对象
	 */
	public function clean();
	/**
	 * 获取布局文件名
	 * @return string
	 */
	public function getLayout();
	/**
	 * 设置布局文件名
	 * @return IView
	 */
	public function setLayout($layout);
	/**
	 * 向视图模板添加变量
	 * @param string $field
	 * @param mixed $value
	 * @return IView
	 */
	public function assign($field, $value);

	/**
	 * 向视图模板添加引用变量
	 * @param string $field
	 * @param mixed $value
	 * @return IView
	 */
	public function assignByRef($field, &$value);

	/**
	 * 输出视图模板，并调用默认布局
	 * @param string $template
	 * @param array $params
	 * @param string $cacheid
	 * @return IView
	 */
	public function display($template, $params=null, $cacheid=null);

	/**
	 * 输出视图模板，不调用默认布局
	 * @param string $template
	 * @param array $params
	 * @param string $cacheid
	 * @return IView
	 */
	public function displayPartial($template, $params=null, $cacheid=null);

	/**
	 * 获取视图模板的输出，并调用默认布局
	 * @param string $template
	 * @param array $params
	 * @param string $cacheid
	 * @return IView
	 */
	public function getOutput($template, $params=null, $cacheid=null);

}
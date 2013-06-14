<?php

/** 
 * 用于Http的输出对象
 * @author ardar
 * @since 2.3
 */
class HttpResponse implements IResponse 
{
	public function begin()
	{
		ob_start();
	}
	
	/**
	 * 停止输出
	*/
	public function end()
	{
		ob_flush();
	}
	
	/**
	 * 获取当前已输出buffer
	*/
	public function getBuffer()
	{
		ob_get_contents();
	}
	
	/**
	 * 向Response对象输出结果或页面
	 * @param unknown $content
	*/
	public function send($content)
	{
		echo $content;
	}
	
	/**
	 * 跳转
	 * @param string $location
	*/
	public function redirect($location)
	{
		header("Location: $location");
		Fw::App()->end();
	}
}

?>
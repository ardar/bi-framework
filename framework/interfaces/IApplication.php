<?php


/**
 * 应用程序接口
 * @author ardar
 *        
 */
interface IApplication {
	/**
	 * 初始化应用程序
	 * @param array $setting
	 */
	public function init(AppSetting $setting);
	
	/**
	 * 运行应用程序
	 */
	public function run();
	
	/**
	 * 清理应用程序
	 */
	public function clean();
	
	/**
	 * 终端应用程序
	 */
	public function end();
	
	/**
	 * 获取当前用户接口实例
	 * @return IUser
	*/
	public function getUser();
	
	/**
	 * 设置当前角色;
	 * @param IUser $acc
	*/
	public function setUser(IUser $acc);
	
	/**
	 * Gets the AppSetting instance.
	 * @return AppSetting
	*/
	public function getSetting();
	
	/**
	 * 获取输入参数对象实例
	 * @return IRequest
	*/
	public function getRequest();
	
	/**
	 * 获取输出参数对象实例
	 * @return IResponse
	*/
	public function getResponse();
	
	/**
	 * 获取视图对象实例
	 * @return IView
	*/
	public function getView();
	
	/**
	 * 获取缓存接口对象实例
	 * @return ICache
	*/
	public function getCache();
	
	/**
	 * 获取运行时对象实例.
	 * @return Runtime
	*/
	public function getRuntime();
	
	/**
	 * 获取数据库对象实例.
	 * @return IDatabase
	*/
	public function getDb();
	
	/**
	 * 获取模块实例.
	 * @param string $moduleId 参数为空则返回当前模块实例
	 * @return IModule
	 */
	public function getModule($moduleId='');
		
	/**
	 * 从目录导入类定义文件列表
	 * @param string $path 目录路径
	*/
	public function import($path);
}

?>
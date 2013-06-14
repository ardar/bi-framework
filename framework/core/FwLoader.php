<?php
class FwLoader
{
	private $_fwRootDir;
	
	/**
	 * class file map
	 * @var array
	 */
	protected $_classMap = array();
	
	public function init($rootDir)
	{
		$this->_fwRootDir = $rootDir;
	}

	/**
	 * Auto load class according to the class map.
	 * @param unknown $classname
	 * @return boolean
	 */
	public function autoLoader($className)
	{
		if($this->_classMap[$className])
		{
			//trace($this->_classfiles);
			$node = $this->_classMap[$className];
			if($node['isLoaded']==false)
			{
				require_once ($node['path']);
				$this->_classMap[$className]['isLoaded'] = true;
				return true;
			}
		}
	}
	
	/**
	 * 导入目录下的PHP文件列表，以文件名作为类名
	 * @param string $path
	 */
	public function import($path)
	{
		if(file_exists($path))
		{
			$dir = new \DirectoryIterator($path);
			foreach ($dir as $fileinfo) {
				$filename = $fileinfo->getFileName();
				if (!$fileinfo->isDot() && strpos($filename, '.')!=0) {
					if($fileinfo->isDir())
					{
						// 递归导入子目录
						$this->import($fileinfo->getRealPath());
					}
					else
					{
						// 读取当前目录下php文件
						$rpos = strrpos($filename,'.php');
						if(strrpos($filename,'.php')==strlen($filename)-4)
						{
							$classname = $fileinfo->getBaseName('.php');
							$this->_classMap[$classname] = array(
									'path'=>$fileinfo->getRealPath(),
									'isLoaded'=>false,
							);
						}
					}
				}
			}
		}
	}
}
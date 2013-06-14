<?php
/**
 * 系统基础用户类
 * @author ardar
 * @since 2.3
 * @property string $user_id 
 * @property string $user_name 
 * @property string $password 
 * @property int $status 
 */
class User extends DataModel implements IUser
{
	const GUEST_USERID = 1;
	
	public static $COOKIE_UID = "__PCFW_UID";
	public static $COOKIE_HASH = "__PCFW_HASH";
	
	protected $_isAuthenticated = false;
	
	public static function getTable()
	{
		return TABLEPREFIX."user";
	}
	
	public static function getPrimaryKey()
	{
		return "user_id";
	}
	
	public static function getModelName()
	{
		return "用户";
	}
	
	/**
	 * 用户所属的角色列表
	 * @var array IRole object list
	 */
	protected $_roles = null;
	
	/**
	 * 保存用户配置参数的数组
	 * @var array
	 */
	public $accOptions = null;
	
	/**
	 * (non-PHPdoc)
	 * @see IUser::getRoles()
	 */
	public function getRoles()
	{
		if(!$this->getId())
		{
			throw new FwException('用户不存在');
		}
		if(!$this->_roles)
		{
			$totalCount = 0;
			$this->_roles = UserRoleRelation::createQuery()
				->where("user_id='".$this->getId()."'")
				->fetchAll();
		}
		return $this->_roles;
	}
	
	/**
	 * 检查是否具有指定的权限项
	 * 权限项格式为  moduleRight/controllerRight/actionRight 三层结构
	 * 权限项为ALL时代表全部权限
	 * @return boolean
	 */
	public function hasPrivilege($privilegeItem)
	{
		return true;
		$roles = $this->getRoles();
		foreach($roles as $role)
		{
			/** @var IRole $role */
			if($role->hasPrivilege())
			{
				return true;
			}
		}
		return false;
	}
	
	public function isAuthenticated()
	{
		return true;
	}
	
	public function getName()
	{
		return $this->user_name;
	}
	
	public function authByCookie()
	{
		$cookieuserid = Fw::app()->GetRequest()->GetInputFrom(
				INPUT_COOKIE, self::$COOKIE_UID);
		$cookiehash = Fw::app()->GetRequest()->GetInputFrom(
				INPUT_COOKIE, self::$COOKIE_HASH);
		if(!$cookieuserid || !$cookiehash)
		{
			$this -> SetAsGuest();
			return $this;
		}
		$this->_id = $cookieuserid;
		if (!$this->GetData(true)) 
		{
			$this->_clearCookie();
			$this -> SetAsGuest();
			return $this;
		}
		if (!$this->CheckPassword($cookiehash, 'cookie'))
		{
			$this->_clearCookie();
			$this -> SetAsGuest();
			return $this;
		}
		$this->_isAuthenticated = true;
		return $this;
	}
	
	private function SetAsGuest()
	{
		$this->_isAuthenticated = true;
		$this->_id = (self::GUEST_USERID);
		$this->_data = null;
	}

	public static function EncryptPassword($inputpw, $cryptkey='')
	{
		return md5($inputpw);
	}
	
	public static function EncryptCookieVal($input, $cryptkey)
	{
		return md5($cryptkey."_PCFW_CONTENT_MANAGER_".$input);
	}
	
	public function CheckPassword($inputPass, $authType='web')
	{
		if ( !$inputPass || !$this->GetData())
		{
			echo 'empty'.$this->GetId().$inputPass;
			return false;
		}
		switch ($authType)
		{
			case 'web':
				if ($this->password!=self::EncryptPassword($inputPass))
				{
					//echo 'web wrong '.$this->GetId().' '.$this->password. ' '.self::EncryptPassword($inputPass);
					return false;
				}
				break;
			case 'cookie':
				if (self::EncryptCookieVal($this->password, $this->GetId())!=$inputPass)
				{
					return false;
				}
				break;
			case 'webservice':
				break;
			case 'client':
				break;
			default;
			throw new FwException("invalid Authentication type.");
			return false;
		}
		$this->_isAuthenticated = true;
		return true;
	}

	public function login($identity, $password, $senario='web')
	{
		$existuser = DBHelper::GetSingleRecord(self::GetTable(), 
				'user_name', $identity);
		if (!$existuser || !$existuser['user_id'])
		{
		    throw new AuthException("用户名密码错误", LOCATION_LOGIN);
		}
		$this->_id = $existuser['user_id'];
		if (!$this->GetData())
		{
			throw new AuthException("用户名密码错误", LOCATION_LOGIN);
		}
		if(!$this->CheckPassword($password, $senario))
		{
			throw new AuthException("用户名密码错误", LOCATION_LOGIN);
		}
		
		$cookietime = 0;
		$cookiepw = self::EncryptCookieVal($this->password, $this->GetId());
		$cookiepath = '/';
		$cookiedomain = "";
		
		setcookie(self::$COOKIE_HASH, $cookiepw, $cookietime, $cookiepath, $cookiedomain);
		setcookie(self::$COOKIE_UID, $this->GetId(), $cookietime, $cookiepath, $cookiedomain);
		
		return true;
	}
	
	private function _clearCookie()
	{
		$cookietime = 0;
		$cookiepath = '/';
		$cookiedomain = "";
		
		setcookie(self::$COOKIE_HASH, null, 0, $cookiepath, $cookiedomain);
		setcookie(self::$COOKIE_UID, null, 0, $cookiepath, $cookiedomain);
	}
	
	public function logout()
	{
		$this->_clearCookie();
		
		return true;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see DataObject::saveData()
	 */	
	public function saveData()
	{
		$result = parent::saveData();
		if($result)
		{
			if($this->accOptions!=null && is_array($this->accOptions))
			{
				foreach($this->accOptions as $moduleId => $moduleOptions)
				{
					$module = Fw::app()->getModule($moduleId);
					if(!$module)
					{
						throw new FwException(
								"没有找到设置的参数所属模块 :".$moduleId);
					}
					foreach($moduleOptions as $field=>$fieldvalue)
					{
						$module->setAccOption($this->GetId(), $field, $fieldvalue);
					}
				}
			}
		}
		return $result;
	}
	

	public function getAccOptions()
	{
		return $this->_accOptions;
	}

	public function getAccOption($moduleId, $fieldname)
	{
		$module = Fw::app()->getModule($moduleId);
		if($module)
		{
			return $module->getAccOption($this->GetId(), $fieldname);
		}
		return null;
	}
}
<?php

class IValidator
{
	private static $instance=null;
	/**
	 * 
	 * @return IValidator
	 */
	public static function GetInstance()
	{
		if(static::$instance==null)
		{
			static::$instance = new IValidator();
		}
		return static::$instance;
	}
	
	public $ruleNames = array(
			'skip','require','positive','negative','nonNegative',
			'int','string','numeric','array','requireArray','file',
			'email','url','ip','ipstr','mobile','date','datetime','time',
			'matchto',
			'dataset',
			);
	
	protected $_customValidator = array();
	
	public function __construct()
	{
		foreach($this->ruleNames as $rule)
		{
			$validator = array($this, "Validate".$rule);
			$this->BindValidator(strtolower($rule), $validator);
		}
	}
	
	public function BindValidator($rule, $validator)
	{
		$this->_customValidator[strtolower($rule)] = $validator;
	}
	
	public function Validate($fieldname, $value, $rules=null, $default=null)
	{
		if(!is_array($rules))
		{
			$rules = explode('|', $rules);
		}
		if(!in_array('require',$rules) && $default!==null && $value===null)
		{
			$value = $default;
		}
		$errors = array();
		foreach($rules as $key=>$rule)
		{
			if($rule)
			{
				if(is_array($rule))
				{
					$ruleParams = $rule;
					$rule = $key;
				}
				if($this->_customValidator[strtolower($rule)])
				{
					$validator = $this->_customValidator[strtolower($rule)];
					$value = call_user_func($validator, $fieldname, $value, $ruleParams);
				}
				else
				{
					trace($this->_customValidator);
					throw new FwException("为参数 $fieldname 指定的验证规则 $rule 无效");
				}
			}
		}
		return $value;
	}
	
	public function validateSkip($fieldname, $value, $ruleParams=null)
	{
		return $value;
	}
	
	public function validateRequire($fieldname, $value, $ruleParams=null)
	{
		if($value===null)
		{
			$fieldname = $ruleParams['label'] ? $ruleParams['label'] : $fieldname;
			throw new ArgumentException("{$fieldname}不能为空");
		}
		return $value;
	}
	
	public function validateInt($fieldname, $value, $ruleParams=null)
	{
		if($value && intval($value)!=$value)
		{
			throw new ArgumentException("{$fieldname}必须为整数");
		}
		return intval($value);
	}
	
	public function validateNumeric($fieldname, $value, $ruleParams=null)
	{
		if($value && !is_numeric($value))
		{
			die("$fieldname $value");
			throw new ArgumentException("{$fieldname}必须为数字");
		}
		return $value;
	}
	
	public function validateString($fieldname, $value, $ruleParams=null)
	{
		return trim($value);
	}
	
	public function validateArray($fieldname, $value, $ruleParams=null)
	{
		if($value && !is_array($value))
		{
			throw new ArgumentException("{$fieldname}必须为数组");
		}
		if($value===null)
		{
			$value=array();
		}
		return $value;
	}
	
	public function validateRequireArray($fieldname, $value, $ruleParams=null)
	{
		if(!$value || !is_array($value) || count($value)==0)
		{
			throw new ArgumentException("{$fieldname}不能为空数组");
		}
		return $value;
	}
	
	public function validatePositive($fieldname, $value, $ruleParams=null)
	{
		if(!$value || $value<=0)
		{
			throw new ArgumentException("{$fieldname}必须为正数");
		}
		return $value;
	}
	
	public function validateNegative($fieldname, $value, $ruleParams=null)
	{
		if(!$value || $value<=0)
		{
			throw new ArgumentException("{$fieldname}必须为负数");
		}
		return $value;
	}
	
	public function validateNonNegative($fieldname, $value, $ruleParams=null)
	{
		if($value<0)
		{
			throw new ArgumentException("{$fieldname}不能为负数");
		}
		if(!$value)
		{
			$value = intval($value);
		}
		return $value;
	}
	
	public function validateEmail($fieldname, $value, $ruleParams=null)
	{
		if($value && !OpenDev::checkemail($value))
		{
			throw new ArgumentException("{$fieldname}不是有效的邮件地址");
		}
		return $value;
	}
	
	public function validateIpStr($fieldname, $value, $ruleParams=null)
	{
		if(!$value || !OpenDev::checkip($value))
		{
			throw new ArgumentException("{$fieldname}不是有效的IP地址");
		}
		return $value;
	}
	
	public function validateIp($fieldname, $value, $ruleParams=null)
	{
		if(!$value || !OpenDev::checkip($value))
		{
			throw new ArgumentException("{$fieldname}不是有效的IP地址");
		}
		$value = ip2long($value);
		//trace($value);exit;
		return $value;
	}
	
	public function validateMobile($fieldname, $value, $ruleParams=null)
	{
		if(!$value || !OpenDev::CheckMobileNumber($value))
		{
			throw new ArgumentException("{$fieldname}不是有效的手机号吗");
		}
		return $value;
	}
	
	public function validateUrl($fieldname, $value, $ruleParams=null)
	{
		if(!$value || (!strpos(strtolower($value),'http://')==0 
				&& !strpos(strtolower($value),'https://')==0
				))
		{
			throw new ArgumentException("{$fieldname}不是有效Url地址");
		}
		return $value;
	}
	
	public function validateDate($fieldname, $value, $ruleParams=null)
	{
		if($value && !strtotime($value))
		{
			throw new ArgumentException("{$fieldname}不是有效的日期");
		}
		$value = strtotime($value);
		return $value;
	}
	
	public function validateDateTime($fieldname, $value, $ruleParams=null)
	{
		if($value && !strtotime($value))
		{
			throw new ArgumentException("{$fieldname}不是有效的日期与时间");
		}
		$value = strtotime($value);
		return $value;
	}

	public function validateMatchTo($fieldname, $value, $ruleParams=null)
	{
		$matchValue = Fw::GetApp()->GetRequest()->GetInput($ruleParams);
		if($value && $matchValue!=$value)
		{
			throw new ArgumentException("{$fieldname} 与 {$ruleParams} 不相同");
		}
		return $value;
	}

	public function validateDataSet($fieldname, $value, $ruleParams=null)
	{
		if(!is_array($value) || !is_array(end($value)))
		{
			throw new FwException("参数 {$fieldname}不是数据集");
		}
		//trace($value); 
		return $value;
	}
}
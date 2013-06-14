<?php

class HttpRequest implements IRequest
{
	protected $_headers;
	protected $_inputs;
	
	public function __construct()
	{
		global  $_COOKIE,$_SESSION,$_SERVER,$_FILES,$_GET,$_POST;
		$this->_headers = array();
		$this->_inputs = array();
		$this->_inputs[INPUT_GET] = OpenDev::daddslashes($_GET);
		$this->_inputs[INPUT_POST] = OpenDev::daddslashes($_POST);
		$this->_inputs[INPUT_COOKIE] = OpenDev::daddslashes($_COOKIE);
		$this->_inputs[INPUT_SESSION] = OpenDev::daddslashes($_SESSION);
		$this->_inputs[INPUT_SERVER] = OpenDev::daddslashes($_SERVER);
		$this->_inputs[INPUT_FILE]= $_FILES;
	}
	
	public function __destruct()
	{
		
	}

	/**
	 * (non-PHPdoc)
	 * @see IRequest::GetRoute()
	 */
	public function getRoute()
	{
		$ctrlname = $this->_getInputParam(FIELD_CONTROLLER);
		if (strpos($ctrlname, '.')!==FALSE)
		{
			throw new FwException("The input param is not correct, ctrl:".$ctrlname);
		}
		$modulename = '';
		if (strpos($ctrlname, '/')>0)
		{
			$namearr = explode('/',$ctrlname);
			$modulename = $namearr[0];
			$ctrlname = $namearr[1];
		}
		if (!$ctrlname)
		{
			$ctrlname = "Index";
		}
		if(!$modulename)
		{
			$modulename = "System";
		}
		
		$action = $this->GetInput(FIELD_ACTION,'string','index');
		
		return array(FIELD_MODULE=>$modulename, 
				FIELD_CONTROLLER=>$ctrlname, 
				FIELD_ACTION=>$action);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRequest::getInput()
	 */
	public function getInput($field, $rules=null, $default=null)
	{
		$value = $this->_getInputParam($field);
		return IValidator::GetInstance()->Validate($field, $value, $rules, $default);
	}
	
	/**
	 * 按SESSION，POST，GET顺序从请求中获取指定参数
	 * @param string $field
	 * @param string $fieldtype
	 * @param string $errMsg
	 * @param string $default
	 * @return null|mixed
	 */
	protected function _getInputParam($field, $fieldtype=null, $errMsg=null, $default=null)
	{
		if( isset($this->_inputs[INPUT_SESSION][$field]) )
		{
			return $this->getInputFrom(INPUT_SESSION,$field,$fieldtype,$errMsg, $default);
		}
		elseif( isset($this->_inputs[INPUT_POST][$field]) )
		{
			return $this->getInputFrom(INPUT_POST,$field,$fieldtype,$errMsg, $default);
		}
		elseif( isset($this->_inputs[INPUT_FILE][$field]) )
		{
			return $this->getInputFrom(INPUT_FILE,$field,$fieldtype,$errMsg, $default);
		}
		else 
		{
			return $this->getInputFrom(INPUT_GET,$field,$fieldtype,$errMsg, $default);
		}
	}
	
	public function getInputFrom($group, $field, $datatype=null,$errmsg=null, $default=null)
	{
		$value = null;
		if(isset($this->_inputs[$group]))
		{
			$value =  $this->_inputs[$group][$field];
		}
		if($value===null)
		{
			if($errmsg)
				throw new ArgumentException($errmsg, LOCATION_BACK);
			else 
				$value=$default;
		}
		switch ($datatype)
		{
			case DT_INT:
				$value = intval($value);
				if(!$value && $errmsg) 
				{
					throw new ArgumentException($errmsg, LOCATION_BACK);
				}
				break;
			case DT_NUMBER:
				if(!is_numeric($value)) 
				{
					if($errmsg)
						throw new ArgumentException($errmsg, LOCATION_BACK);
					else
						$value = null;
				}
				break;
			case DT_ARRAY :
				if (!is_array($value))
				{
					if($errmsg)
						throw new ArgumentException($errmsg, LOCATION_BACK);
					else
						$value = null;
				}
				break;
			case DT_FILE :
				throw new FwException("Unsupported DT_FILE parameter");
				break;
			case DT_STR :
				$value = trim($value);
				if ($errmsg && strlen($value)==0)
				{
					throw new ArgumentException($errmsg, LOCATION_BACK);
				}
				break;
			case DT_DATE:
				$value = trim($value);
				$value = strtotime($value);
				if($errmsg && !$value)
				{
					throw new ArgumentException($errmsg, LOCATION_BACK);
				}
				break;
			default: 
				//$value = $value;//string
				break;
		}
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRequest::setInput()
	 */
	public function setInput($field, $fieldvalue)
	{
		$this->_inputs[INPUT_POST][$field] = $fieldvalue;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRequest::HandleUploadFile()
	 */
	public function handleUploadFile($original_file, $field, 
	    $saveexpath = UploadFile::PathMonthExt, $rename=UploadFile::RenameMd5,
		$checktype=UploadFile::CheckDeny, $checkext = UploadFile::CheckDefaultDenyExt, 
		$minbytes = 0, $maxbytes=2048000, $errmsg=null, $isoverwrite=false)
	{
		$delete = $this->_getInputParam("DELETE_".$field,DT_INT);
		//$uploadedfile = $this->GetInput("UPLOADED_".$field,DT_STR);
		if($delete && $original_file)
		{
		    //$uploadfile->DeleteOriginal();
		    $original_file = null;
		}
		
		$uploadfile = new UploadFile($field, $original_file);
		if(!$uploadfile->HasValue($errmsg))
		{
			return $uploadfile;
		}
		if(!$uploadfile->Validate($checktype, $checkext, 0, $maxbytes, !$errmsg))
		{
		    return null;
		}
		
		$savefilepath = $uploadfile->Save(
		    Fw::GetAppSetting()->UploadDir, 
			$saveexpath, $rename, $isoverwrite);
		
		return $uploadfile;
	}
}
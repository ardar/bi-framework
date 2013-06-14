<?php
class UploadFile
{
	const CheckAllow = 'allow';
	const CheckDeny = 'deny';
	
	const CheckDefaultDenyExt = "phtml|php|php3|asp|aspx|cgi|py";
	const CheckDefaultImageExt = "jpg|jpeg|png|bmp|gif";
	
	const PathNone = 0;
	const PathExtMonth = 1;
	const PathMonthExt = 2;
	const PathMonth = 3;
	const PathExt = 4;
	const PathDate = 5;
	const PathExtDate = 6;
	const PathDateExt = 7;
	
	const RenameNone = 0;
	const RenameTimeRand = 1;
	const RenameNameRand = 2;
	const RenameMd5 = 3;
	
	private $_field;
	private $_tempfile;
	private $_error;
	private $_isCommited = false;
	private $_commitDeleteFile = null;
	
	public $filename;
	public $mimetype;
	public $ext;	
	public $filesize;
	public $savepathroot;
	public $savefilename;
	
	
	
	public function __construct($inputField, $original_file=null)
	{
		global $_FILES;
		$this->filename = $_FILES[$inputField]['name'];
		$this->mimetype = $_FILES[$inputField]['type'];
		$this->filesize = $_FILES[$inputField]['size'];
		$this->_tempfile = $_FILES[$inputField]['tmp_name'];
		$this->_error = $_FILES[$inputField]['error'];
		if(!is_array($this->filename))//multiinputfile
			$this->ext = strtolower(end(explode(".",$this->filename)));
		
		$this->savefilename = $original_file;
		
	}
	
	public function __destruct()
	{
		if (!$this->_isCommited)
		{
			$this->Rollback();
		}
	}
	
	public function HasValue($empty_msg=null)
	{
		if ($this->_error == UPLOAD_ERR_OK && $this->filename)
		{
			return true;
		}
		if ($empty_msg)
		{
		    if(!$this->savefilename)
		    {
			    throw new FwException($empty_msg);
		    }
		}
		if(!$this->savefilename)
		{
		    return false;
		}
	}
	
	public function Validate($extChkType, $extList,$minSize, $maxSizeInK, $silent=false)
	{
		if ($this->_error!= UPLOAD_ERR_OK)
		{
			if(!$silent)
			{
				throw new FwException("上传文件失败, 错误: ".$this->_error);
			}
			else
			{
				return false;
			}
		}
		return $this->CheckExt($extChkType, $extList, $silent) 
			&& $this->CheckSize($minSize, $maxSizeInK, $silent);
	}
	
	public function CheckExt($extChkType, $extList, $silent=false)
	{
		$types = explode("|",$extList);
		if($extChkType==self::CheckAllow)
		{
			if (!in_array($this->ext,$types))
			{
				if($silent)
					return false;
				else
					throw new FwException("文件类型不正确, 仅允许上传 $extList");
			}
		}
		else
		{
			if (in_array($this->ext,$types)){
				if($silent)
					return false;
				else
					throw new FwException("文件类型不正确 , 不允许上传$extList");
			}
		}
		return true;
	}
	
	public function CheckSize($minSize, $maxSizeInK, $silent=false)
	{
		if($maxSizeInK>0 && $this->filesize >($maxSizeInK*1024))
		{
			if($silent)
				return false;
			else
				throw new FwException("文件大小超过限制 , 最大文件大小: $maxSizeInK k");
		}
		if($minSize>0 && $this->filesize < ($minSize*1024))
		{
			if($silent)
				return false;
			else
				throw new FwException("文件大小不符合 , 最小文件大小要达到: $minSize k");
		}
		return true;
	}
	
	public function GetTmpFilename()
	{
	    return $this->_tempfile;
	}
	
	public function DeleteOriginal()
	{
	    $this->_commitDeleteFile = $this->savefilename;
	    $this->savefilename = '';
	}
	
	public function GetSaveFilename()
	{
		return $this->savefilename;
	}
	
	public function GetFullFileName()
	{
		return $this->savepathroot.$this->savefilename;
	}
	
	public function GetBuffer()
	{
		return OpenDev::readfromfile($this->_tempfile);
	}
	
	public function Save($savePath, $exPathMode=self::PathNone, 
	    $renamefile=self::RenameTimeRand, $isoverwrite=false)
	{
		$this->savepathroot = '';
		if(substr($savePath, strlen($savePath)-1)!='/')
		{
		    $savefilepath = $savePath.'/';
		}
		else 
		{
		    $savefilepath = $savePath;
		}
		switch ($exPathMode)
		{
			case self::PathExt:
				$savefilepath .= $this->ext."/";
				break;
			case self::PathMonth:
				$savefilepath .= date("Ym")."/";
				break;
			case self::PathDate:
				$savefilepath .= date("Ymd")."/";
				break;
			case self::PathMonthExt:
				$savefilepath .= date("Ym")."/".$this->ext."/";
				break;
			case self::PathExtMonth:
				$savefilepath .= $this->ext."/".date("Ym")."/";
				break;
			case self::PathDateExt:
				$savefilepath .= date("Ymd")."/".$this->ext."/";
				break;
			case self::PathExtDate:
				$savefilepath .= $this->ext."/".date("Ymd")."/";
				break;
			default:
			    $savefilepath .= $exPathMode; 
			    break;
		}

		if (!is_dir($this->savepathroot.$savefilepath) && !mkdir($this->savepathroot.$savefilepath,0777,true)) 
		{
			throw new FwException("保存上传文件到路径 $savefilepath 失败, 路径不存在或无法写入");
		}
	
		$newfile = "";
		$times = 0;
		while(true)
		{
		    $times++;
			$newfilename = $this->filename;
			switch ($renamefile)
			{
				case self::RenameTimeRand: 
					$newfilename = date("Ymdhns")."_".OpenDev::random(3).".".$this->ext;
					break;
				case self::RenameNameRand:
					$newfilename = $this->savefilename.'_'.OpenDev::random(3).".".$this->ext;
					break;
				case self::RenameMd5:
					$sufix = ($times>0) ? "_$times" : '';
					$newfilename = strtoupper(md5_file($this->_tempfile)).$sufix.".".$this->ext;
					break;
				default: 
					if($renamefile)
					{
						$newfilename = $renamefile;
					}
					break;
			}
			$savefile = $this->savepathroot.$savefilepath.$newfilename;
			if (!$isoverwrite && file_exists($savefile))
			{
				if (++$times<50 )//&& ($renamefile==self::RenameTimeRand || $renamefile==self::RenameNameRand)
				{
					continue;
				}
				else
				{
					throw new FwException("保存上传文件到路径  $savefile 失败, 目录下存在同名文件");
				}
			}
			break;
		}
		if (move_uploaded_file($this->_tempfile, $savefile))
		{
			$this->savefilename = $savefilepath.$newfilename;
			return $this->savefilename;
		}
		else
		{
			throw new FwException("保存上传文件到路径  $savefile 失败");
		}
	}
	
	public function Commit()
	{
		if($this->_commitDeleteFile)
		{
			OpenDev::deletefile($this->_commitDeleteFile);
			$this->_commitDeleteFile = null;
		}
		$this->_isCommited = true;
	}
	
	public function Rollback()
	{
		if($this->_isCommited)
		{
			throw new FwException('UploadFile 已经提交，无法回滚 ');
		}
		if($this->savefilename && file_exists($this->savefilename))
		{
			OpenDev::deletefile($this->savefilename);
		}
		return true;
	}
}
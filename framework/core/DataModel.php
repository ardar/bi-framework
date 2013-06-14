<?php
abstract class DataModel
{
	/**
	 * 以关联数组形式保存对象的数据字段
	 * @var array
	 */
	protected $_data = null;
	/**
	 * 主键值
	 * 对于单主键表，必须定义为自增字段
	 * 对于多主键表，则改属性为数组
	 * @var int|array 
	 */
	protected $_id = null;// For multiple primary key, this is array.
	
	protected $_dirtyFields = array();
	

	/**
	 * 获取对象表名
	 * @return string
	 */
	public static function getTable()
	{
		throw new FwException('must implement getTable()');
	}

	/**
	 * 获取对象表的主键定义，单主键时为主键字段名，多主键时为包含主键字段的数组
	 * @return string|array
	 */
	public static function getPrimaryKey()
	{
		throw new FwException('must implement getPrimaryKey()');
	}
	
	/**
	 * 获取对象实例名称
	 * @return string
	 */
	public function getName()
	{
		throw new FwException("Need Override GetName() function.");
	}

	/**
	 * 获取对象类型名称
	 * @return string
	 */
	public static function getModelName()
	{
		throw new FwException("Need Override getModelName() function.");
	}
	
	/**
	 * 创建一个以本数据表为主表的查询构建器 QueryBuilder
	 * @return QueryBuilder
	 */
	public static function createQuery()
	{
		$query = new QueryBuilder();
		return $query->from(static::getTable());
	}
	
	/**
	 * 传入单值Id或字段数组以初始化本对象
	 * @param string|array $id_or_array
	 * @throws FwException
	 */
	public function __construct($id_or_array=null)
	{
		if(!static::getTable())
		{
			throw New FwException("Not given _table for ".get_class($this));
		}
		if(!static::getPrimaryKey())
		{
			throw New FwException("Not given _pk for ".get_class($this));
		}
		if(is_array(static::getPrimaryKey()))
		{
			if (is_array($id_or_array))
			{
				$matcheds = array();
				foreach($id_or_array as $field => $val)
				{
					if(in_array($field, static::getPrimaryKey()))
					{
						$this->_id[$field] = $val;
						$matcheds[$field] = 1;
					}
				}
				foreach(static::getPrimaryKey() as $pk)
				{
					if($matcheds[$pk]===null)
					{
						trace($id_or_array);
						throw new FwException(__CLASS__." must given $pk as multiple primary keys");
					}	
				}
				$this->_data = $id_or_array;
			}
			elseif($id_or_array!==null)
			{
				throw new FwException(__CLASS__." has multiple primary key, cannot init with one key. $id_or_array");
			}
		}
		else
		{
			if (is_array($id_or_array))
			{
				$this->_id = $id_or_array[$this->getPrimaryKey()];
				$this->_data = $id_or_array;
			}
			else
			{
				$this->_id = $id_or_array;
			}
		}
	}
	
	/**
	 * 获取对象Id
	 * @return int|array
	 */
	public function getId()
	{
		return $this->_id;
	}
	
	/**
	 * reset the id
	 * @param int|array $id
	 */
	protected function _resetId($id)
	{
		if ($id!=$this->_id)
		{
			$this->_data = null;
		}
		$this->_id = $id;
	}
	
	public function __get($property) 
	{
		if(is_array(static::getPrimaryKey()))
		{
			if(in_array($property, static::getPrimaryKey()))
			{
				return $this->_id[$property];
			}
		}
		else
		{
			if (strtolower($property)=="id" || strtolower($property)==strtolower(static::getPrimaryKey()))
			{
				return $this->GetId();
			}
		}
        if (isset($this->_data[$property])) 
        {
            return $this->_data[$property];
        } 
        else 
        {
            return null;
        }
    }
    
    public function __set($property, $value) 
    {
    	if(is_array(static::getPrimaryKey()))
    	{
    		if($this->getId() && in_array($property, static::getPrimaryKey()))
    		{
	    		throw new FwException("Cannot modify the multiple primary key");
    		}
    	}
    	else
    	{
	    	if(strtolower($property)=="id" || strtolower($property)==strtolower(static::getPrimaryKey()))
	    	{
	    		throw new FwException("Cannot modify the id");
	    	}
    	}
    	$this->_dirtyFields[$property] = 1;
        $this->_data[$property] = $value;
    }
    
    /**
     * 获取对象是否被修改过
     * @return boolean
     */
    public function isDirty()
    {
    	return count($this->_dirtyFields)>0 ? true : false;
    }
	
    /**
     * 获取对象数据字段为一个关联数组
     * @param string $refresh
     * @return Ambigous array|NULL
     */
	public function getData($refresh=false)
	{
		if (!$refresh && $this->_data)//$this->GetId() && 
		{
			return $this->_data;
		}
		if (!$this->GetId())
		{
			return null;
		}
		
		return $this->_data = DBHelper::GetSingleRecord(
				static::GetTable(), static::getPrimaryKey(), $this->getId());
	}
	
	/**
	 * 保存对象数据
	 * 如果是新纪录，将插入新的行到数据库， 并将本对象主键设置为新的Id
	 * 如果是已有记录， 直接更新原有记录。
	 * @return bool 返回是否成功
	 */
	public function saveData()
	{
		$result = true;
		$multipk_notsaved = false;
		if(is_array($this->getPrimaryKey()))
		{
			foreach($this->getPrimaryKey() as $pk)
			{
				$pkval = $this->_data[$pk];
				$filter_options[] = DBHelper::FilterOption(null, $pk, $pkval, '=');
			}
			$exist = DBHelper::GetJoinRecord($this->GetTable(), null, $filter_options, null);
			if(!$exist)
			{
				$multipk_notsaved = true;
			}
		}
		if (!$multipk_notsaved && $this->GetId())
		{
			$rs = array();
			foreach ($this->_dirtyFields as $field=>$nouse)
			{
				if(is_array($this->getPrimaryKey()))
				{
					if(!in_array($field, $this->getPrimaryKey()) && isset($this->_data[$field]))
					{
						$rs[$field] = $this->_data[$field];
					}
				}
				else
				{
					if ( $field!=$this->getPrimaryKey() 
						&& isset($this->_data[$field]))
					{
						$rs[$field] = $this->_data[$field];
					}
				}
			}
			$result = DBHelper::UpdateRecord($this->GetTable(), $rs, $this->getPrimaryKey(), $this->GetId());
		}
		else
		{
			$rs = array();
			if($this->_data)
			{
				foreach($this->_dirtyFields as $field=>$nouse)
				{
					$rs[$field] = $this->_data[$field];
				}
				if($multipk_notsaved)
				{
					// save the multiple key when inserting
					foreach($this->getPrimaryKey() as $pk)
					{
						$pkval = $this->_data[$pk];
						$rs[$pk] = $pkval;
					}
				}
				$result = DBHelper::InsertRecord($this->GetTable(), $rs);
			}
			if ($result)
			{
				if(!$this->id && !is_array($this->getPrimaryKey()))
				{
					$this->_id = $result;
				}
				if(!is_array($this->getPrimaryKey()))
				{
					$this->_data[$this->getPrimaryKey()]=$this->_id;
				}
				$this->_dirtyFields = array();
			}
		}
		return $result;
	}
	
	/**
	 * 插入新记录
	 * @param array $fields 包含要插入字段的关联数组
	 * @return number  返回被影响的行数
	 */
	public function insert($fields)
	{
		$result = DBHelper::InsertRecord($this->GetTable(), $fields);
		if( $result)
		{
			$this->_id = $result;
			foreach ($fields as $key=>$value)
			{
    		    $this->_data[$key]= $value;
			}
		}
		return $result;
	}
	
	/**
	 * 更新对象
	 * @param array $fields  包含要更新字段的关联数组
	 * @throws FwException
	 * @return bool
	 */
	public function update($fields)
	{
		foreach ($fields as $field=>$value)
		{
			if(is_array($this->getPrimaryKey()))
			{
				if (in_array($field,$this->getPrimaryKey()))
				{
					throw new FwException("不能修改multiple主键 ".$field." 的值");
				}
			}
			else
			{
				if ($field==$this->getPrimaryKey())
				{
					throw new FwException("不能修改主键 ".$this->getPrimaryKey()." 的值");
				}
			}
		}
		$result = DBHelper::UpdateRecord($this->GetTable(), $fields, $this->getPrimaryKey(), $this->GetId());
		if($result)
		{
    		foreach ($fields as $key=>$value)
    		{
    		    $this->_data[$key]= $value;
    		}
		}
		return $result;
	}
	
	/**
	 * 删除对象
	 * @return boolean
	 */
	public function delete()
	{
		if ($this->GetId() && DBHelper::DeleteRecord($this->GetTable(), $this->getPrimaryKey(), $this->GetId()))
		{
			$this->_data = null;
			return true;
		}
		$this->_data = null;
		return false;
	}
	
	public function __toString()
	{
		return serialize($this);
	}
	
	/**
	 * Find by filter such as array('fieldname:op'=>'fieldvalue',..)
	 * @param array $filterFields
	 * @return array results
	 */
	public static function findByFilters(array $filterFields, $sortBy='')
	{
		$filter_options = array();
		foreach($filterFields as $fieldItem => $value)
		{
			$arr = explode(':', $fieldItem);
			$field = $arr[0];
			$op = $arr[1] ? $arr[1] : '=';
			$filter_options[] = DBHelper::FilterOption(
					static::GetTable(), $field, $value, $op);
		}
		$sort_options[] = $sortBy;
		$icount = 0;
		return DBHelper::GetJoinRecordSet(static::GetTable(), null, 
				$filter_options, $sort_options, 0, 99999, $icount);
	}
	
}
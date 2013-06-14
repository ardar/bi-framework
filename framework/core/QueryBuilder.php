<?php
/**
 * 基于Mysql的查询构造器
 * @author ardar
 * @since 2.0
 */
class QueryBuilder 
{
	protected $selectFields = '';
	protected $fromTable = '';
	protected $fromAlia = '';
	protected $_groupBy = '';
	protected $_groupBySelect = '';
	
	protected $joins = array();
	protected $filters = array();
	protected $sorters = array();
	protected $perpage = -1;
	protected $offset = 0;
	
	/**
	 * 设置查询的字段，默认为*
	 * @param string $selectFields
	 * @return QueryBuilder
	 */
	public function select($selectFields)
	{
		if(!is_array($selectFields))
		{
			$selectFields = array($selectFields);
		}
		foreach($selectFields as $fields)
		{
			$this->selectFields[] = $fields;
		}
		return $this;
	}
	/**
	 * 设置查询的主表
	 * @param string $table 查询的主表名
	 * @param string $alia 表的别名
	 * @return QueryBuilder
	 */
	public function from($table, $alia='')
	{
		$this->fromTable = $table;
		$this->fromAlia = $alia;
		return $this;
	}
	/**
	 * 设置Join查询条件，仅支持left join
	 * @param unknown_type $joinTable
	 * @param unknown_type $alia
	 * @param unknown_type $joinOn
	 * @param unknown_type $selectFields
	 * @return QueryBuilder
	 */
	public function join($joinTable, $alia, $joinOn, $selectFields='')
	{
		$joinSql = " left join $joinTable $alia on ($joinOn)";
		$this->joins[] = array('joinsql'=>$joinSql, 'select'=>$selectFields);
		return $this;
	}
	/**
	 * 设置Join查询条件
	 * @param unknown_type $joinTable
	 * @param unknown_type $alia
	 * @param unknown_type $left_field_table
	 * @param unknown_type $left_field
	 * @param unknown_type $right_field
	 * @param unknown_type $select_fields
	 * @return QueryBuilder
	 */
	public function joinEx($joinTable, $alia, $left_field_table, $left_field, 
			$right_field, $select_fields='')
	{
		$this->joins[] = DBHelper::JoinOption($joinTable, $alia, 
				$left_field_table, $left_field, 
				$right_field, $select_fields);
		return $this;
	}
	
	/**
	 * 设置过滤查询条件
	 * @param string $where
	 * @param string $whereOp
	 * @return QueryBuilder
	 */
	public function where($where, $whereOp = 'and')
	{
		$this->filters[] = array('sql'=>$where,'where_relation'=>$whereOp);
		return $this;
	}
	/**
	 * 设置过滤查询条件
	 * @param string $table
	 * @param string $field
	 * @param string $op
	 * @param string $value
	 * @param string $whereOp
	 * @return QueryBuilder
	 */
	public function whereEx($table, $field, $op='=', $value, $whereOp='and')
	{
		$this->filters[] = DBHelper::FilterOption($table, $field, $value, $op, $whereOp);
		return $this;
	}
	
	/**
	 * 同时设置多个过滤查询条件
	 * @param array $options
	 * @return QueryBuilder
	 */
	public function whereOptions($options)
	{
		if(is_array($options))
		{
			foreach($options as $option)
			{
				$this->filters[] = $option;
			}
		}
		return $this;
	}
	/**
	 * 设置单个过滤查询条件
	 * @param unknown_type $option
	 * @return QueryBuilder
	 */
	public function whereOption($option)
	{
		if(is_array($option))
		{
			$this->filters[] = $option;
		}
		return $this;
	}
	/**
	 * 设置聚合查询的 group by 条件
	 * @param string $groupBy
	 * @param string $groupBySelect
	 * @return QueryBuilder
	 */
	public function groupBy($groupBy, $groupBySelect='')
	{
		$this->_groupBy = $groupBy;
		$this->_groupBySelect = $groupBySelect;
		return $this;
	}
	/**
	 * 设置limit查询条件
	 * @param int $offset
	 * @param int $perpage
	 * @return QueryBuilder
	 */
	public function limit($offset=0, $perpage=-1)
	{
		$this->offset = $offset;
		$this->perpage = $perpage;
		return $this;
	}
	/**
	 * 设置排序方式
	 * @param string $sortField
	 * @param string $sortType
	 * @return QueryBuilder
	 */
	public function orderBy($sortField, $sortType='')
	{
		$this->sorters[] = $sortField.' '.$sortType;
		return $this;
	}
	/**
	 * 设置排序方式
	 * @param string $sortField
	 * @param string $sortType
	 * @return QueryBuilder
	 */
	public function orderOptions($options)
	{
		if(is_array($options))
		{
			foreach($options as $option)
			{
				$this->sorters[] = $option;
			}
		}
		return $this;
	}
	
	/**
	 * 查找全部记录
	 */
	public function fetchAll()
	{
		$totalCount = 0;
		return $this->fetchWithCount($totalCount, 0, 0);
	}
	
	/**
	 * 按照条件查找全部记录, 并获取总记录数
	 * @param int $offset
	 * @param int $perpage
	 */
	public function fetchWithCount(&$totalCount, $offset=0, $perpage=0)
	{
		if($offset) $this->offset = $offset;
		if($perpage) $this->perpage = $perpage;
		
		$table_options['table'] = $this->fromTable;
		$table_options['alia'] = $this->fromAlia;
		$table_options['select'] = $this->selectFields;
		$table_options['groupby'] = $this->_groupBy;
		$table_options['groupbyselect'] = $this->_groupBySelect;
		return DBHelper::GetJoinRecordSet($table_options, 
				$this->joins, $this->filters, $this->sorters,
				$this->offset, $this->perpage, $totalCount);
	}
	/**
	 * 返回记录集的记录数
	 * @return number
	 */
	public function getTotalCount()
	{
		return $this->totalcount;
	}
}
<?php
class DBHelper
{
	/**
	 * The IDatabase Engine instance.
	 * @var IDatabase
	 */
	private static $_db = null;
	public static function InitDB($dbEngine=null)
	{
		if(self::$_db==null || $dbEngine!=null)
		{
			self::$_db = ($dbEngine ? $dbEngine : Fw::app()->getDb());
		}
	}
	
	public static function GetTableState($dbname)
	{
		self::InitDB();
		$sql = 'SHOW TABLE STATUS FROM '.$dbname;
		$rss = self::$_db->FetchAll($sql);
		foreach ($rss as $rs)
		{
			$tables[$rs['Name']] = $rs;
		}
		return $tables;
	}
	
	public static function UpdateRecord($table,$fields,$pk,$pkvalue)
	{
		self::InitDB();
		if(is_array($pk) && is_array($pkvalue))
		{
			foreach($pk as $onepk)
			{
				$val = $pkvalue[$onepk];
				$sql .= "and $onepk='$val'";
			}
			$subquery = " where 1 $sql";
		}
		else
		{
			$subquery = " where $pk ='$pkvalue'";
		}
		return self::$_db->Update($table, $fields, $subquery);
	}
	
	/**
	 * insert into a table name with given array where keys are table columns
	 * @param string $table_name
	 * @param mixed $fields
	 * @return int returns the inserted id.
	 */
	public static function InsertRecord($table, array $fields)
	{
		self::InitDB();
		return self::$_db->Insert($table, $fields);
	}
	
	public static function ExecQuery($sql)
	{
		self::InitDB();
		return self::$_db->Query($sql);
	}
	
	public static function GetQueryRecord($sql)
	{
		self::InitDB();
		return self::$_db->FetchRow($sql);
	}
	
	public static function GetQueryRecords($sql)
	{
		self::InitDB();
		return self::$_db->FetchAll($sql);
	}
	
	public static function GetRecord($table, $subquery)
	{
		self::InitDB();
		$sql = "select * from $table $subquery";
		return self::$_db->FetchRow($sql);
	}
	
	public static function GetSingleRecord($table,$pk,$pkvalue)
	{
		self::InitDB();
		if(is_array($pk) && is_array($pkvalue))
		{
			foreach($pk as $onepk)
			{
				$val = $pkvalue[$onepk];
				$sql .= "and $onepk='$val'";
			}
			$sql = "select * from $table where 1 $sql";
		}
		else
		{
			$sql = "select * from $table where $pk ='$pkvalue'";
		}
		return self::$_db->FetchRow($sql);
	}
	
	public static function GetRecords($table,$filtkey='',$filtvalue='',$sortfield='',$sortvalue='asc')
	{
		if (!$table)
		{
			throw new FwException("DBHelper::GetRecordSet: table is empty");
		}
		self::InitDB();
		$sql = "select * from $table";
		if ($filtkey!='') {
			$sql .= " where $filtkey ='$filtvalue'";
		}
		if ($sortfield!='') {
			$sql .= " order by $sortfield $sortvalue";
		}
		return self::$_db->FetchAll($sql);
	}
	
	public static function JoinOption($table, $alia, 
		$left_field_table, $left_field, $right_field, $select_fields)
	{
		return array(
			'table'=>$table, 'alia'=>$alia, 
			'left_field_table'=>$left_field_table,
			'left_field'=>$left_field, 'right_field'=>$right_field, 
			'select'=>$select_fields);
	}
	
	public static function FilterOption($table, $field, $value, $op, $where_relation='and')
	{
		return array('table'=>$table, 'field'=>$field, 'value'=>$value, 'op'=>$op, 'where_relation'=>$where_relation);
	}
	
	public static function GetJoinRecord($table_options, $join_options, $filter_options, $sort_options)
	{
		if (is_array ( $table_options )) {
			$table = $table_options ['table'];
			if ($table_options ['select']) {
				$select_arr = explode ( ',', $table_options ['select'] );
				foreach ( $select_arr as $select_item ) {
					$maintbl_select .= $maintbl_select ? (",$select_item") : "$select_item";
				}
			} else {
				$maintbl_select = $table . ".*";
			}
			if ($table_options ['groupby']) {
				$groupbysql = "group by " . $table_options ['groupby'];
			}
			if ($table_options ['groupbyselect']) {
				$groupbyselect = $table_options ['groupbyselect'];
				$maintbl_select .= $maintbl_select ? ("," . $groupbyselect) : $groupbyselect;
			}
		} else {
			$table = $table_options;
		}
		$maintbl_select = $maintbl_select ? $maintbl_select : ($table.'.*');
				
		if (!$table)
		{
			throw new FwException("DBHelper::GetRecordSet: table is empty");
		}
		self::InitDB();
		
		$join_left_scope = "";
		if ($join_options)
		{
			foreach ($join_options as $join)
			{
			    if($join['joinsql'])
			    {
			        $joinstr .= $join['joinsql'];
			        $joinselect.= ','.$join['select'];
			        continue;
			    }
				$jointable = $join['table'];
				$alia = $join['alia'];
				$aliatable = $alia ? $alia : $jointable;
				$jointablestr = $alia? (" $jointable as $alia "): $jointable;
				if ($join['select'])
				{
					$selectarray = explode(',', $join['select']);
					foreach ($selectarray as $item)
					{
						$joinselect.= ','.$aliatable.'.'.$item;
					}
				}
				else 
				{
					$joinselect.= ','.$join['table'].'.*';
				}
				$left_field_table = $join['left_field_table'] ? $join['left_field_table'] : $table;
				$left_field = $join['left_field'] ? $join['left_field'] : $join['on_field'];
				$right_field = $join['right_field'] ? $join['right_field'] : $join['on_field'];
				if (!$jointable || !$left_field || ! $right_field)
				{
					throw new FwException("Join 查询参数不正确");
				}
				$join_left_scope .= "(";
				$joinstr .= " left join ".$jointablestr." on ".$left_field_table.".".$left_field.'='.$aliatable.'.'.$right_field.")";
			}
		}
		if ($filter_options)
		{
			foreach ($filter_options as $option)
			{
				$field_table = $option['table'] ? $option['table'] : $table;
				$op = $option['op'] ? $option['op'] : '=';
				if (!$option['field'] || !$option['value']===null)
				{
					if (FW_DEBUG)
					{
						print_r($filter_options);
					}
					throw new FwException("Filter 查询参数不正确 field:".$option['field']);
				}
				$filter .= " and ".$field_table.'.'.$option['field']." $op '".$option['value']."'";
			}
		}
		if ($filter) {
			$filter = " where 1 $filter";
		}
		global $sql;
		$sql = "select $maintbl_select $joinselect from 
			$join_left_scope $table 
			$joinstr 
			$filter limit 1";
		return self::$_db->FetchRow($sql);
	}
	
	public static function GetJoinRecordSet($table_options, 
		$join_options, $filter_options, $sort_options,
		$offset,$perpage,&$icount,$cachetime=-1)
	{
		$table = null;
		$tableAsStr = null;
		$maintbl_select = null;
		$groupbysql = "";
		$groupbyselect = "";
		if (is_array($table_options))
		{
			$tableAsStr = $table = $table_options['table'];
			if($table_options['alia'])
			{
				$tableAsStr = "$table as ".$table_options['alia'];
				$table = $table_options['alia'];
			}
			if ($table_options['select'])
			{
				$select_arr = explode(',', $table_options['select']);
				foreach ($select_arr as $select_item)
				{
					$maintbl_select .= $maintbl_select ? 
						(",$table.$select_item") : "$table.$select_item";
				}
			}
			else 
			{
			    $maintbl_select = $table.".*";
			}
			if ($table_options['groupby'])
			{
				$groupbysql = "group by ".$table_options['groupby'];
			}
			if ($table_options['groupbyselect'])
			{
			    $groupbyselect = $table_options['groupbyselect'];
				$maintbl_select .= $maintbl_select ? 
						(",".$groupbyselect) : $groupbyselect;
			}
		}
		else 
		{
			$table = $table_options;
			$tableAsStr = $table;
		}
		$maintbl_select = $maintbl_select ? $maintbl_select : ($table.'.*');
		
		if (!$table)
		{
			throw new FwException("DBHelper::GetJoinRecordSet: table is empty");
		}
		self::InitDB();
		$join_left_scope = "";
		if ($join_options)
		{
			foreach ($join_options as $join)
			{
			    if($join['joinsql'])
			    {
			        $joinstr .= $join['joinsql'];
			        if($join['select'])
			        {
			        	$joinselect.= ','.$join['select'];
			        }
			        continue;
			    }
				$jointable = $join['table'];
				$alia = $join['alia'];
				$aliatable = $alia ? $alia : $jointable;
				$jointablestr = $alia? (" $jointable as $alia "): $jointable;
				if ($join['select'])
				{
					$selectarray = explode(',', $join['select']);
					foreach ($selectarray as $item)
					{
						$joinselect.= ','.$aliatable.'.'.$item;
					}
				}
				else 
				{
				    if($join['select']!=='')
				    {
					    $joinselect.= ','.$join['table'].'.*';
				    }
				}
				$left_field_table = $join['left_field_table'] ? $join['left_field_table'] : $table;
				$left_field = $join['left_field'] ? $join['left_field'] : $join['on_field'];
				$right_field = $join['right_field'] ? $join['right_field'] : $join['on_field'];
				if (!$jointable || !$left_field || ! $right_field)
				{
					throw new FwException("Join 查询参数不正确 ".$join['table']);
				}
				$join_left_scope .= "(";
				$joinstr .= " left join ".$jointablestr." on ".$left_field_table.".".$left_field.'='.$aliatable.'.'.$right_field.")";
			}
		}
		if ($filter_options)
		{
			//trace($filter_options);
			foreach ($filter_options as $option)
			{
				$whereRelation = $option['where_relation'] ?  $option['where_relation'] : 'and';
				if($option['sql'])
				{
					$filter.=" $whereRelation ".$option['sql'];
				}
				else
				{
					$field_table = $option['table'] ? $option['table'] : $table;
					$op = $option['op'] ? $option['op'] : '=';
					if (!$option['field'])
					{
						if (FW_DEBUG)
						{
							print_r($filter_options);
						}
						throw new FwException("Filter 查询参数不正确 field:".$option['field']);
					}
					$option_value = $op=='in' ? "(".$option['value'].")" : "'".$option['value']."'";
					$filter .= " $whereRelation ".$field_table.'.'.$option['field']." $op $option_value";
				}
			}
		}
		if ($filter) {
			$filter = " where 1 $filter";
		}
		if (is_array($sort_options))
		{
			foreach ($sort_options as $option)
			{
				$sortor .= $sortor ? (','.$option) : $option;
			}
		}
		else
		{
			$sortor .= $sort_options;
		}
		if ($perpage>0) 
		{
			$offset = intval($offset);
			$perpage = intval($perpage);
			$limiter = " limit $offset,$perpage";
		}
		$orderter = $sortor? " order by $sortor":'';
		if($groupbysql)
		{
		    $sql = "select count(*) as count from (select $maintbl_select
			from $join_left_scope $tableAsStr $joinstr $filter $groupbysql) as count_temp";
		}
		else 
		{
		    $sql = "select count(*) as count 
		    from $join_left_scope $tableAsStr $joinstr $filter";
		}
		$rs = self::$_db -> FetchRow($sql,1);
		$icount = $rs['count'];
		global $sql;
		$sql = "select $maintbl_select $joinselect from 
			$join_left_scope $tableAsStr 
			$joinstr 
			$filter 
			$groupbysql
			$orderter 
			$limiter";
		//TRACE($sql);
 		$rss = self::$_db -> FetchAll($sql,$cachetime);
// 		$c_sql = "select FOUND_ROWS() as icount";
// 		$rs = self::$_db -> FetchRow($c_sql);
// 		$icount = $rs['icount'];
		return $rss;
	}
	
	public static function GetRecordSet($table,$offset,$perpage,&$icount,$sortor='',$filter='',$cachetime=-1)
	{
		if (!$table)
		{
			throw new FwException("DBHelper::GetRecordSet: table is empty");
		}
		self::InitDB();
		if ($filter) {
			$filter = " where 1 $filter";
		}		
		if ($perpage>=0) {
			$limiter = " limit $offset,$perpage";
		}
		$orderter = $sortor? " order by $sortor":'';
		/*$sql = "select count(*) as count from $table $filter";
		$rs = self::$_db -> FetchRow($sql,1);
		$icount = $rs['count'];*/
		$sql = "select * from $table $filter $orderter $limiter";		
		$rss = self::$_db -> FetchAll($sql);
		$c_sql = "select FOUND_ROWS() as icount";
		$rs = self::$_db -> FetchRow($c_sql);
		$icount = $rs['icount'];
		return $rss;
	}
	
	public static function GetRecordCount($table,$filter='')
	{
		self::InitDB();
		if ($filter) 
		{
			$filter = " where $filter";
		}	
		$sql = "select count(*) as count from $table $filter";
		$rs = self::$_db -> FetchRow($sql);
		return $rs['count'];
	}
	
	public static function DeleteRecord($table,$pk,$pkvalue)
	{
		self::InitDB();
		if(is_array($pk) && is_array($pkvalue))
		{
			foreach($pk as $onepk)
			{
				$val = $pkvalue[$onepk];
				$sql .= "and $onepk='$val'";
			}
			$subquery = " where 1 $sql";
			self::$_db->Delete($table, $subquery);
		}
		else
		{
			return self::$_db->DeleteSingle($table, $pk, $pkvalue);
		}
	}
	
	public static function DeleteRecords($table,$pk,$pkvalue)
	{
		if(is_array($pk))
		{
			throw new FwException("Not supportted multiple primary key");
		}
		self::InitDB();
		$pkvalues[] = $pkvalue;
		return self::$_db->DeleteList($table, $pk, $pkvalues);
	}
	
	public static function DeleteList($table,$pk,array $pkvalues)
	{
		if(is_array($pk))
		{
			throw new FwException("Not supportted multiple primary key");
		}
		self::InitDB();
		return self::$_db->DeleteList($table, $pk, $pkvalues);
	}
}
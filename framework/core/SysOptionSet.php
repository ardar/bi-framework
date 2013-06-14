<?php
namespace biface;

/**
 * 用于保存系统配置集和用户配置集的 Helper Class
 * @author ardar
 *
 */
class SysOptionSet
{
	private $fieldset = array();
	
	/**
	 * 增加一个配置字段.
	 * @param string $field
	 * @param string $fieldname
	 * @param string $datatype
	 * @param string $editctrl
	 * @param mixed $rules
	 * @param mixed $properties
	 * @param string $defaultvalue
	 * @return SysOptionSet
	 */
	public function AddField($field, $fieldname, $datatype,
			$editctrl, $rules, $properties, $defaultvalue)
	{
		$this->fieldset[$field] = array('field'=>$field, 'fieldname'=>$fieldname,
				'datatype' => $datatype, 'editctrl'=>$editctrl,
				'rules'=>$rules, 'defaultvalue'=>$defaultvalue, 'properties'=>$properties);
		return $this;
	}
	
	/**
	 * 返回设置字段集合数组
	 * @return array:
	 */
	public function GetFields()
	{
		return $this->fieldset;
	}
}
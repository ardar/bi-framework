<?php
interface IHtml
{
	public function Init($cssfile, $params=null);
	
	public function Header($type, $header);
	
	public function GetHeader($type);
	
	public function GetHeaderHtml();
	
	public function Link($id, $text, $url, $target='', $class='');
	
	public function Button($id, $label, $type='button', $action='', $properties=null);
	
	public function Input($id, $fieldname, $value, $params=null);
	
	public function Select($id, $fieldname, $value, $params=null);
	
	public function TreeSelect($id, $fieldname, $selected_values, $data, $params=null);
	
	public function Tree($id, $fieldname, $selected_values, $data, $params=null);
	
	public function FileInput($id, $fieldname, $value, $params=null);
	
	public function ImageInput($id, $fieldname, $value, $params=null);
	
	public function Radios($id, $fieldname, $values, $params=null);
	
	public function Checkboxes($id, $fieldname, $values, $params=null);
	
	public function DatePicker($id, $fieldname, $value, $params=null);
	
	public function TimePicker($id, $fieldname, $value, $params=null);
	
	public function TextArea($id, $fieldname, $value, $params=null);
	
	public function HtmlEdit($id, $fieldname, $value, $params=null);
	
	public function Table($id, $data, $params=null);
	
	public function Form($id, $fields, $data, $params=null);
	
	public function Toolbar($id, $items, $params=null);
}
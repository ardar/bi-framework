<?php
interface IRequest
{
	public function getInput($field, $rules=null, $defaultOrRequireMsg=null);
	
	public function setInput($field, $fieldvalue);
	
	public function getInputFrom(
			$group, $field, $datatype=null,$errmsg=null, $default=null);
	
	/**
	 * Gets the route request
	 * @return array the requesting route parts( module,controller,action)
	 */
	public function getRoute();
	
	/**
	 * 
	 * @param string $original_file
	 * @param string $field
	 * @param string $checktype
	 * @param string $checkext
	 * @param int $minbytes
	 * @param int $maxbytes
	 * @param string $errmsg
	 * @return UploadFile
	 */
	public function handleUploadFile($original_file, $field, 
	    $saveexpath = UploadFile::PathMonthExt, $rename=UploadFile::RenameMd5,
		$checktype=UploadFile::CheckDeny, $checkext = UploadFile::CheckDefaultDenyExt, 
		$minbytes = 0, $maxbytes=2048000, $errmsg=null, $isoverwrite=false);
}
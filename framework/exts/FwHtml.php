<?php
class FwHtml implements IHtml
{
	static $tempElementId = 0;
	
	protected $headers = array();
	
	public function Init($cssfile, $params=null)
	{
		
	}
	
	public function Header($type, $header)
	{
		$this->headers[md5($type."_".$header)] = array('type'=>$type,'header'=> $header);
	}
	
	public function GetHeader($type)
	{
		$result = array();
		foreach($this->headers as $header)
		{
			if($header['type']==$type)
			{
				$result[] = $header['header'];
			}
		}
		return $result;
	}
	
	public function GetHeaderHtml()
	{
		$ret = "<!--htmlhelper headers-->\n";
		if($this->headers)
		{
			foreach($this->headers as $key=>$set)
			{
				$type = $set['type'];
				$item = $set['header'];
				if($item)
				{
					if($type=='cssfile')
					{
						$ret .= "<link rel='stylesheet' type='text/css' href='".$item."' >\n";
					}
					elseif($type=='css')
					{
						$ret .= "<style>\n".$item."\n</style>\n";
					}
					elseif($type=='jsfile')
					{
						$ret .= "<script type='text/javascript' src='".$item."'></script>\n";
					}
					elseif($type=='js')
					{
						$ret .= "<script>\n".$item."\n</script>\n";
					}
				}
			}
		}
		$ret .= "<!--htmlhelper headers end-->\n";
		return $ret;
	}
	
	public function HtmlData($value, $type='', $params=null)
	{
		switch($type)
		{
			case '%':
			case 'percent':
				$val = number_format($value*100,2).'%';
				break;
			case 'color':
				$val = "<i class='ico-stop' style='color:{$value}'></i>";
				break;
			case 'datetime':
				$val = DateTimeHelper::FormatDateTime($value);
				break;
			case 'time':
				$val = DateTimeHelper::FormatTime($value);
				break;
			case 'date':
				$val = DateTimeHelper::FormatDate($value);
				break;
			case 'duration':
				$val = CommonHelper::GetDurationDesc($value);
				break;
			case 'bool':
				$val = $value ? "<i class='ico-checkmark'></i>" : "<i class='ico-cancel-2'></i>";
				break;
			case 'progress':
				$id = ++static::$tempElementId;
				$val = "<a href='javascript:void(0)' id='tooltip_$id' rel='tooltip'
				data-original-title='{$value}%'>
				<div class='progress'><div class='bar' style='width: {$value}%;'></div></div></a>
				<script>$('#tooltip_$id').tooltip();</script>";
				break;
			default:
				$val = $value;
				break;
		}
		return $val;
	}
	
	public function Label($id, $text, $labelClass='', $url='', $target='', $params=null)
	{
		$labelClass = "label ".$labelClass;
		$link = $url?$this->Link($id, $text, $url, $target, $params):$text;
		return "<span class='$labelClass'>$link</span>";
	}
	
	public function ToolTip($id, $text, $tips, $url='', $target='', $params=null)
	{
		$id = $id ? $id : ++self::$tempElementId;
		$url = $url?$url : 'javascript:void(0);';
		$tips = strip_tags($tips);
		return "<a href='$url' id='tooltip_{$id}' rel='tooltip' 
					    data-original-title=\"{$tips}\">
						$text<script>$('#tooltip_{$id}').tooltip();</script>\n";
	}
	
	public function Link($id, $text, $url, $target='', $params=null)
	{
		if(is_array($params))
		{
			foreach($params as $key=>$val)
			{
				$ext .= " $key=\"$val\"";
			}
		}
		else 
		{
			$ext = $params;
		}
		return "<a id='$id' href=\"$url\" target='$target' $ext>$text</a>";
	}

	public function MultiFileInput($id, $fieldname,$value,$params=null)
	{	
		$width = $params['width'] ? $params['width'] : '300px';
		$height =$params['height'] ? $params['height'] : '20px';
		$accept=$params['accept'];
		
		$maxlength=$params['maxlength']?$params['maxlength']:12;
		
		$fieldname=$id."[]";
		$ret ='<script src="3rdparty/multiple-file-upload/jquery.MultiFile.js" type="text/javascript"></script>';
		return $ret.="<input name='".$fieldname."' class='multi' maxlength='".$maxlength."' type='file' accept='".$accept."' style='width:300px ;margin:0 auto; color:gray;border-color:gray; background-color:initial;' id='".$id."' >\n";
	}
	
	
	public function FileInput($id, $fieldname, $value, $params=null)
	{	
		
		$type = $params['type'] ? $params['type'] : 'text';
		$width = $params['width'] ? $params['width'] : '300px';
		$height =$params['height'] ? $params['height'] : '20px';
		$class	=$params['class'];
		
		if ($value!='')
		{
			$ret ='<script src="3rdparty/multiple-file-upload/jquery.MultiFile.js" type="text/javascript"></script>';
			$ret .= "<div style='height:25px'><label>"
					.self::Input("DELETE_$id","DELETE_$fieldname",'1',array('type'=>'checkbox',
							'onclick'=>"if(this.checked)UPLOADED_$id.value='';else UPLOADED_$id.value=ORIGINAL_$id.value;")
							)
							.'删除已上传文件? </label>('.$this->Link('',$value,$value,'_blank').')</div>'.
							"\n";
		}
		$ret.="<input name='ORIGINAL_".$fieldname."' type='hidden' id='ORIGINAL_".$id."' value='$value'>\n";
		$ret.="<input name='UPLOADED_".$fieldname."' type='hidden' id='UPLOADED_".$id."' value='$value'>\n";
		return $ret."<input name='".$fieldname."' class='".$class."' type='file' style='width:300px ;margin:0 auto; color:gray;border-color:gray; background-color:initial;' id='".$id."' >\n";
	}

	public function Tree($id, $fieldname, $selected_values, $data, $params=null)
	{
		
	}
	
	public function CheckBox($id, $fieldname, $value, $params)
	{
		$ext_attrs = '';
		$label = '';
		$class = "checkbox";
		$inputClass = '';
		if($params)
		{
			foreach($params as $key=>$param)
			{
				if($key=='label')
				{
					$label=$param;
				}
				elseif($key=='inputClass')
				{
					$inputClass = $param;
				}
				elseif($key=='class')
				{
					$class = $param;
				}
				elseif($key=='checked')
				{
					$ext_attrs .= $param==true ? " checked" : '';
				}
				else
				{
					$ext_attrs .= " $key=\"$param\"";
				}
			}
		}
		$ret = "<label class=\"$class\">\n";
		$ret .= "<input type='checkbox' name='$fieldname' value='$value' class='$inputClass' $ext_attrs/>\n";
		$ret .= "<span class=\"metro-checkbox\">$label</span>\n";
		$ret .= "</label>\n";
		return $ret;
	}
	
	public function Button($id, $label, $type='button', $action='', $properties=null)
	{
		$class = 'btn';
		$class = $properties['class']?$properties['class']:$class;
		$properties['class']=null;

		$ext_attrs = $this->FetchHtmlOptions($properties);
		
		if($type=='link')
		{
			$action = "window.location.href='$action'";
			$type = 'button';
		}
		if($action)
		{
			$onlick = " onclick=\"$action\"";
		}
		return "<button id='$id' name='$id' type='$type' class='$class'
            $onlick $ext_attrs>$label</button>\n";
	}
	
	public function FetchHtmlOptions($params)
	{
		if($params && is_array($params))
		{
			foreach($params as $key=>$param)
			{
				if($params!==null)
				{
					$ext_attrs .= " $key=\"$param\"";
				}
			}
		}
		else
		{
			$ext_attrs = $params;
		}
		return $ext_attrs;
	}

	public function Input($id, $fieldname, $value, $type='text', $params=null)
	{
		$params['class'] = $params['class'] ? $params['class'] : "input";
		$addon = $params['addon'] ? $params['addon'] : '';
		$params['addon'] = '';
		$ext_attrs = $this->FetchHtmlOptions($params);
		
		$inputCtrl = "<input type='$type' id='$id' name='$fieldname' value='$value' $ext_attrs/>\n";
		if($addon)
		{
			$inputCtrl = "<div class='input-append'>$inputCtrl
			<span class='add-on'>$addon</span>
			</div>";
		}
		return $inputCtrl;
	}

	public function ImageInput($id, $fieldname, $value, $params=null)
	{
		
	}

	public function Radios($id, $fieldname, $values, $params=null)
	{
		$seperator='';
		$optionfield = $params['optionfield'] ? $params['optionfield'] : $fieldname;
		$optiontext = $params['optiontext'] ? $params['optiontext'] : $optionfield;
		$options = $params['options'];
		$params['options'] = null;
		$params['optionfield'] = null;
		$params['optiontext'] = null;
		$value = $values;
		if (is_array($options) && !is_array(end($options)))
		{
			foreach ($options as $key=>$optvalue)
			{
				$opts[] = array('key'=>$key,'value'=>$optvalue);
			}
			$options = $opts;
			$optionfield = 'key';
			$optiontext = 'value';
		}
		$htmlOptions = $this->FetchHtmlOptions($params);
		$val = "";		
		if ($options)
		{
			foreach ($options as $option)
			{
				$val.="<label class='radio inline'>";
				$val.="<input name='".$fieldname."' type='radio' 
					 id='".$fieldname.$option[$optionfield]."' 
					 value='".$option[$optionfield]."' 
					 ".( ($option[$optionfield]==$value) ? ' checked':'')." $htmlOptions>\n";
				$val.="<span class='metro-radio'>".$option[$optiontext]."</span></label>";
			}
		}
		return $val;
	}

	public function Select($id, $fieldname, $value, $params=null)
	{
		$isChosen = $params['component']=='chosen';
		if($isChosen)
		{
			$this->Header('cssfile', '3rdparty/chosen/chosen.css');
			$this->Header('jsfile', '3rdparty/chosen/chosen.jquery.js');
			$exScript = "<script>
			$('#".$id."').chosen({allow_single_deselect:true,no_results_text: '没有符合条件的选项'});
			</script>\n";
			$params['data-placeholder'] = "请选择...";
			$params['class'] .= " chosen-select";
		}
		$fieldid = $id;
		$ismultiple = $params['ismultiple'];
		$field = $ismultiple ? $fieldname.'[]' : $fieldname;
		$options = $params['options'];
		$values = is_array($value) ? $value : array($value);
		$multiplesec = $ismultiple ? 'multiple' : '';
		$class = $params['class'];
		
		$optionfield = $params['optionfield'] ? $params['optionfield'] : $field;
		$optiontext = $params['optiontext'] ? $params['optiontext'] : $optionfield;
		$ext_title = $params['nulltitle'];

		($params['component']=null);
		($params['ismultiple']=null);
		($params['options']=null);
		($params['optionfield']=null);
		($params['optiontext']=null);
		($params['nulltitle']=null);
		($params['class']=null);
		$htmlOptions = $this->FetchHtmlOptions($params);
		
		if (is_array($options) && !is_array(end($options)))
		{
			foreach ($options as $key=>$optvalue)
			{
				$opts[] = array('key'=>$key,'value'=>$optvalue);
			}
			$options = $opts;
			$optionfield = 'key';
			$optiontext = 'value';
		}
		$val="<select $multiplesec name='".$field."' class='$class' id='".$fieldid."'
			 $htmlOptions >\n";
		if ($ext_title)
		{
			if (is_array($ext_title))
			{
				foreach ($ext_title as $nullkey=>$nullname)
				{
					$val .="<option value='$nullkey'>$nullname</option>\n";
				}
			}
			else
			{
				$val .="<option value=''>$ext_title</option>\n";
			}
		}
		if ($options && is_array($options))
		{
			foreach ($options as $option)
			{
				//trace($values);
				$selected = '';
				if($option[$optionfield]==='' || in_array('', $values))
				{
					$selected = in_array($option[$optionfield], $values,true) ? 'selected' : '';
				}
				else
				{
					$selected = in_array($option[$optionfield], $values) ? 'selected':'';
				}
				$disable_str = $option['DISABLED'] ? ' disabled=true ' : '';
				$val.="<option value='".$option[$optionfield]."' ".$disable_str
				.($selected).">".$option[$optiontext]."</option>\n";
			}
		}
		$val.="</select>\n".$exScript;
		return $val;
	}

	public function TreeSelect($id, $fieldname, $selected_values, $data, $params=null)
	{
		
	}

	public function Checkboxes($id, $fieldname, $values, $params=null)
	{
		$field = $id;
		$options = $params['options'];
		$optionfield = $params['optionfield'] ? $params['optionfield'] : $id;
		$optiontext = $params['optiontext'] ? $params['optiontext'] : $optionfield; //print_r($value);var_dump($values);
		$seperator = $params['seperator'] ? $params['seperator'] : ' ';
		$class = $params['class'] ? $params['class'] : 'checkbox';

		$valuearray = array();
		if (is_array($options)>0 && !is_array(end($options)))
		{
			foreach ($options as $key=>$optvalue)
			{
				$opts[] = array('key'=>$key,'value'=>$optvalue);
			}
			$options = $opts;
			$optionfield = 'key';
			$optiontext = 'value';
		}
		//trace($values);
		if($values===null || $values==='')
		{
			$valuearray = array();
		}
		elseif (!is_array($values) && $values!==null && $values!=='')
		{
			$valuearray[0] = $values;
		}
		elseif (is_array($values) && is_array(end($values)) )
		{
			foreach ($values as $value)
				$valuearray[] = $value[$field];
		}
		else $valuearray = $values;
		//trace($valuearray);
		//trace($options);
		$sepeartor='';

		$val = "<div class='checksetdiv'>\n";
		if($params['checkall_button'])
		{
			$inputclass = " CHECKBOXES_INPUT_{$field}";
			$val.=$this->Button('CHECKSET_CHECKALL_'.$field, '全选/取消全选', 'button',
				"$(this).data('checked',!$(this).data('checked'));$('.CHECKBOXES_INPUT_{$field}').attr('checked',$(this).data('checked'));",
				array('class'=>'btn-link'));
		}
		if($options)
		{
			foreach ($options as $optionidx => $option)
			{
				if(!$option[$optionfield] && count($valuearray)==0)
				{
					$checked = false;
				}
				else
				{
					$checked = in_array($option[$optionfield],$valuearray);
				}
				$val .= $this->CheckBox($field.'_'.$optionidx, $field."[]", $option[$optionfield], 
						array(
								'label'=>$option[$optiontext],
								'class'=>$class,
								'checked'=>$checked,
								'inputClass'=>$inputclass,
								));
				$val.= $seperator;
			}
		}
		$val.="</div>\n";
		return $val;
	}

	public function ColorPicker($id, $fieldname, $value, $params=null)
	{
		$this->Header('cssfile', '3rdparty/colorpicker/css/colorpicker.css');
		$this->Header('jsfile', '3rdparty/colorpicker/js/bootstrap-colorpicker.js');
		
		if($params['config']=='simple')
		{
			$script = "<script>\n$(function(){ 
			$('#color_$id').colorpicker().on('changeColor', function(ev){
				$('#color_$id').css('background-color', ev.color.toHex());
				$('#$id').attr('value',ev.color.toHex());
				}); });\n</script>";
			$input = "<input id='$id' type='hidden' class='input' name='{$fieldname}' value='{$value}' >";
			
			return "<div class='input-append color'>$input
				<span class='add-on'><i id='color_$id' 
				data-color-format='hex' data-color='$value' 
				style='background-color:$value'></i></span></div>$script";
		
		}
		else {
			$script = "<script>\n$(function(){ $('#$id').colorpicker().on('changeColor', function(ev){
				$('#color_$id').css('background-color', ev.color.toHex());
				}); });\n</script>";
			$input = "<input id='$id' type='text' class='input' name='{$fieldname}' value='{$value}' >";
			return "<div class='input-append color'>$input
				<span class='add-on'><i id='color_$id' style='background-color:$value'></i></span></div>$script";
		}
	}
	
	public function DatePicker($id, $fieldname, $value, $params=null)
	{
		$this->Header('cssfile', 'css/datepicker.css');
		$this->Header('jsfile', 'js/bootstrap-datepicker.js');
		$class = $params['class']?$params['class']:'input';
		unset($params['class']);
		if($value && !is_numeric($value)&& $params['data-date-minviewmode']=="")
		{
			$value = strtotime($value);
		}
		
		$dataformat	=$params['data-date-format']?$params['data-date-format']:'yyyy-mm-dd';
		$viewmode	=$params['data-date-viewmode']?$params['data-date-viewmode']:'years';		
		$minviewmode=$params['data-date-minviewmode']?$params['data-date-minviewmode']:'days';
		
		if($params['data-date-minviewmode']!="")
		{	
			$value = ($value>0)?$value:'';
						
		}else
		{
			$value = ($value>0) ? date('Y-m-d',$value):'';
		}
		$paramsOption = $this->FetchHtmlOptions($params);
		
		$script = "<script>\n$(function(){ $('#$id').datepicker({
			    format: \"$dataformat\",
			    viewMode: \"$viewmode\", 
			    minViewMode: \"$minviewmode\"
				}
    		); });\n</script>";
		return "<input type=\"text\" id=\"$id\" name=\"$fieldname\" value=\"$value\" class=\"$class\" $paramsOption>$script";
	}

	public function TimePicker($id, $fieldname, $value, $params=null)
	{
		
	}

	public function TextArea($id, $fieldname, $value, $params=null)
	{
		$class = "input-xlarge";
		$rows = '5';
		$ext_attrs = '';
		if($params)
		{
			foreach($params as $key=>$param)
			{
				if($key=='rows')
				{
					$rows = $param;
				}
				elseif($key=='class')
				{
					$class = $param;
				}
				else
				{
					$ext_attrs .= " $key=\"$param\"";
				}
			}
		}
		return "<textarea name='$fieldname' rows='$rows' class='$class' $ext_attrs/>$value</textarea>";
	}
	
	public function HtmlEdit($id, $fieldname, $value, $params=null)
	{
		$field = $fieldname;
		$configfile = $params['config'] ? 'editor_config_'.$params['config'].'.js' : 'editor_config.js';
		$width = $params['width'] ? $params['width']:'100%';
		$height = $params['height'] ? $params['height']:'300';
		
		$this->Header("js","window.UEDITOR_HOME_URL = '".APP_URL."ueditor/';");
		$this->Header("jsfile","ueditor/$configfile");
		$this->Header("jsfile",'ueditor/editor_all.js');
		$this->Header("cssfile",'ueditor/themes/default/ueditor.css');
		
		$paramheight = str_replace(array('px','PX'),'',$height);
		$input = "<textarea id='$field' name='$field' style='width:$width;'>"
				.htmlspecialchars($value,ENT_NOQUOTES,'utf-8')."</textarea>\n
<script type='text/javascript'>
    var ueditor_$field = new UE.ui.Editor({minFrameHeight:$paramheight});
    ueditor_$field.render('$field');
    //ueditor_$field.addListener('contentchange',function(){{$field}.value=this.getContent();});
</script>";
		return $input;
	}

	public function Table($id, $data, $params=null)
	{
		
	}

	public function Form($id, $fields, $data, $params=null)
	{
		
	}

	public function Toolbar($id, $items, $params=null)
	{
		
	}
}
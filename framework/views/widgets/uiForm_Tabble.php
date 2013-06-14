<?php
//@var $widget UIForm
?>
<div class="container-fluid">
<div class="row-fluid">

<?php if($widget->status==UIForm::StatusError):?>
<div class="alert alert-block alert-warning">
<button type="button" class="close" data-dismiss="alert"></button>
<?php if(is_array($widget->errors)):?>
<h4>提交的表单数据有错误</h4>
	<?php if($widget->errors):?>
	<p>
	<ul>
	<?php foreach($widget->errors as $error):?>
	<li><?php echo $error?></li>
	<?php endforeach?>
	</ul>
	</p>
	<?php endif?>
<?php else:?>
<p><?php echo $widget->errors?></p>
<?php endif?>
</div>
<?php elseif($widget->status==UIForm::StatusValid):?>
<div class="alert alert-block alert-success">
<button type="button" class="close" data-dismiss="alert"></button>
<p><?php echo $widget->successMessage;?></p>
</div>
<?php elseif($widget->description!=null):?>
<div class="alert alert-block alert-info">
<button type="button" class="close" data-dismiss="alert"></button>
<p><?php echo $widget->description?></p>
</div>
<?php endif?>
<?php 
if($widget->properties['showBox']):
$this->beginWidget('UIPanel', array('label'=>$widget->label, 'icond'=>'ico-pencil'));
endif;?>

<?php echo $widget->tabBar;?>
<form id='<?php echo $widget->GetId();?>' name='<?php echo $widget->GetId()?>' 
action='<?php echo $widget->postUrl?>' method='<?php echo $widget->postMethod?>'
 enctype='<?php echo $widget->encryptType?>' class="form-horizontal">
 <div class="tabbable tabs-<?php echo $widget->properties['tab_placement']?>"> <!-- Only required for left/right tabs -->

 <ul class="nav nav-tabs <?php echo $widget->properties['tab_class']?>">
<?php 
$widget->properties['activePage'] = $widget->properties['activePage']?$widget->properties['activePage']:'DEFAULT';
foreach($widget->groups as $groupIndex => $group):?>
<li class="<?php if($widget->properties['activePage']==$groupIndex):?>active<?php endif?> ">
				       <a href="<?php echo "#tab_".$widget->GetId()."_{$groupIndex}"?>"  data-toggle="tab">
				       <?php echo $group['label']?$group['label']:$groupIndex;?>
				       </a>
				    </li>
<?php endforeach?>
</ul>

<div class="tab-content <?php echo $widget->properties['content_class']?>">
				
<?php foreach($widget->groups as $groupIndex => $group):?>
<div class="tab-pane <?php if($widget->properties['activePage']==$groupIndex):?>active<?php endif?>"
		        id="tab_<?php echo $widget->GetId()?>_<?php echo $groupIndex?>">
		        
    	<fieldset id="folding_body_<?php echo $group['id']?>" >
    	
        <div id="fieldset_<?php echo $widget->GetId().'_'.$group['id']?>">
	<?php
	if(is_array($widget->elements[$group['id']])):
		foreach($widget->elements[$group['id']] as $elIndex => $field):?>
			<?php if($field->type=='hidden'):?>
			<div class="hidden"><?php echo $field->GetBody()?></div>
			<?php else:?>
			<div class="control-group 
			<?php if($field->status==UIFormElement::StatusError):?>warning<?php endif?> 
			<?php if($field->status==UIFormElement::StatusValid):?>successf<?php endif?>">
        	<label class="control-label" for="input<?php echo $field->GetId()?>">
			<?php if($field->HasRule('require')):?><span class='warning'>*</span><?php endif?><?php echo $field->label?>
        	</label>
        	<div class="controls">
	        <?php echo $field->GetBody();?>
        	<?php if($field->hint){?> <span class="help-inline"><?php echo $field->hint?></span><?php }?>
        	<?php if($field->status==UIFormElement::StatusError && $field->errors!=null):?>
	        	<div style=""><span class="error">
	        	<?php foreach($field->errors as $err):?>
	        	<?php echo $err?>
	        	<?php endforeach?>
	        	</span></div>
			<?php endif?> 
        	</div>
      		</div>
      		<?php endif?>
		<?php endforeach;// elements?>
	<?php endif?>
				</div>
		</fieldset>
		
</div> 
<?php endforeach //foreach groups?>
</div></div>

<div class="form-actions">

<?php 
if ($widget->buttons){?>
<?php
foreach($widget->buttons as $button){
	echo $button;
} // foreach hidden?>
<?php }else{ //if buttons?>

<button name='Submit' type='submit' class='btn btn-primary'
            onclickd="this.form.submit();this.disabled=true;this.value='正在提交...'">提交</button>
<button type="button" onclick="javascript:window.history.back();" class='btn'>返回</button>
<?php }//if buttons?>
<input type="hidden" name="_fw_formid" value="<?php echo $widget->GetId()?>">  
</div>
</form>
<?php 
if($widget->properties['showBox']):
$this->endWidget();
endif;?>
</div>
</div>
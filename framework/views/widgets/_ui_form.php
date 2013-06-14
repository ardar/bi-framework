<style>
.dataform{
	width:100%;
	line-height:150%;
}
.dataform-title{
	font-size:;
	padding:5px;
}
.form-group{
	display:block;
	border-bottom:dashed 1px #eee;
	height:25px;
	background-color:#fefefe;
	font-weight:bold;
	padding:5px;
}
.form-row{
	width:100%;
	display:block;
	border-bottom:dashed 1px #eee;
	padding:2px;
	position:relative;
	line-height:150%;
}
.form-row-label{
	width:15%;
	min-width:120px;
	text-align:right;
	vetical-align:middle;
	font-weight:normal;
	display:inline;
	flaot:left;
}
.form-row-body{
	margin-left:10px;
	width:400px;
	text-align:left;
	vetical-align:top;
	display:inline;
	flaot:left;
}
.form-row-error{
	margin-left:10px;
	display:inline;
	flaot:left;
}
.form-footer{
	display:block;
	background-color:#efefef;
	padding:5px;
}
</style>
<div class="grid-panel">
<div class="grid-header"><strong><?php echo $form->label;?></strong></div>
<div class="form-error"><?php $form->error?></div>
<table border=0 cellpadding=2 cellspacing=1 style="width:100%">
<form id='<?php echo $form->GetId();?>' name='<?php echo $form->GetId()?>' action='<?php echo $form->postUrl?>' 
method='<?php echo $form->postMethod?>'  enctype='<?php echo $form->encryptType?>' >

<?php foreach($form->groups as $groupIndex => $group){?>
    <?php if($group['id']!='DEFAULT'){?>
    	<tr>
        <td class="tdbg fieldtd" style="text-align:right;font-weight: bold;">
        	<?php echo $group['label']?>
        </td>
        <td class="tdbg valuetd">
        	<a style="text-decoration:underline;cursor:hand;"
 href="javascript:if(folding_body_<?php echo $group['id']?>.style.display=='none')folding_body_<?php echo $group['id']?>.style.display='';else folding_body_<?php echo $group['id']?>.style.display='none';">
 展开/收缩选项</a>
        </td>
        </tr>
    <?php }?>
    <tbody id="folding_body_<?php echo $group['id']?>" <?php if(!$group['isshow']){?>style="display:none"<?php }?>>
	<?php
	if(is_array($form->elements[$group['id']])){
		foreach($form->elements[$group['id']] as $elIndex => $field){?>
			<tr class=fieldtr>
        	<td class=fieldtd>
        	<?php echo $field->label?>
        	<?php if($field->hint){?><BR><span class="fielddesc"><?php echo $field->hint?></span><?php }?>
        	</td>
	        <td class=valuetd>
	        <?php echo $field->GetBody();?>
			<span class="form-row-error"><?php echo $field->error?></span>
			</td>
        </tr>
		<?php }//foreach elements?>
	<?php }//if?>
        </tbody>
<?php } //foreach groups?>

<tbody>
<tr > 
<td height=30> </td><td >
<?php 
if($form->hiddens!=null){
foreach($form->hiddens as $hidden){
	echo $hidden->GetBody();
} // foreach hidden
}//if
?>
<?php 
if ($form->buttons){?>
<?php
foreach($form->buttons as $button){
	echo $$button->GetBody();
} // foreach hidden?>
<?php }else{ //if buttons?>
<button name='Submit' type='button' class='jq-button btn-primary'
            onclick="this.form.submit();this.disabled=true;this.value='正在提交...'">确认提交</button>
<button type="button" onclick="javascript:window.history.back();" class='jq-button'>返回上页</button>

<?php }//if buttons?>
</td>
</tr>
</tbody>
</form>
</table>
</div>

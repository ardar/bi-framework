<div id="searchModal" style="display: none;width:620px;" class="modal hide fade" 
tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true" >

<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h3 id="searchModalLabel">高级搜索</h3>
</div>
 <form method="get" action="" class="form-horizontal">
 <div class="searchModal-body row-fluid" style="padding:20px">
 <?php if($fields)
 	foreach($fields as $field):?>
 	<?php if($field->type!='hidden'):?>
 			<div class="control-group">
        	<label class="control-label" for="input<?php echo $field->GetId()?>">
			<?php echo $field->label?>
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
     <?php else:?> 
     <?php echo $field->GetBody();?>
     <?php endif?> 
<?php endforeach;?>      

</div>
               <div class="modal-footer">
                 <button type="submit" class="btn btn-primary">搜索</button>
                 <button class="btn" data-dismiss="modal">关闭</button>
               </div>
</form>   
</div>

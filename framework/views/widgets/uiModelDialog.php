<?php /*@var $widget UIWidget*/?>
<div id="<?php echo $widget->GetId()?>Modal" style="display: none;width:<?php echo $widget->properties['width']?>;height:<?php echo $widget->properties['height']?>;"
 class="modal hide fade" 
tabindex="-1" role="dialog" aria-labelledby="<?php echo $widget->GetId()?>ModalLabel" aria-hidden="true" >

<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h3 id="<?php echo $widget->GetId()?>ModalLabel"><?php echo $widget->label?></h3>
</div>
 <div class="<?php echo $widget->GetId()?>Modal-body row-fluid" style="padding:20px">
<?php echo $content?>
</div>
<?php if(!$widget->properties['nofoot']):?>
               <div class="modal-footer">
                 <button type="submit" class="btn btn-primary btn-large">确定</button>
                 <button class="btn" data-dismiss="modal">关闭</button>
               </div>
<?php endif;?>
</div>
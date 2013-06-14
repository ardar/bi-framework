<button class="shortcut" type="<?php echo $widget->type?>" 
onclick="<?php echo $widget->onclick?>">
	<span class="icon"> <i class="<?php echo $widget->icon?>"></i></span> 
	<span class="label <?php echo $widget->labelClass?>"><?php echo $widget->label?> </span> 
	<?php if ($widget->badge):?>
	<span class="badge <?php echo $widget->badgeClass?>"><?php echo $widget->badge?></span>
	<?php endif?>
</button>
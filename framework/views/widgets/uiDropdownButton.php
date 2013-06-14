<?php
//@var $widget UIDropdownButton
?>
<div class="btn-group">
<button class="btn dropdown-toggle <?php echo $widget->class?>" data-toggle="dropdown">
<?php echo $widget->label?><span class="caret"></span></button>
                   <ul class="dropdown-menu">
                   <?php if($widget->dropdowns) 
                   	foreach($widget->dropdowns as $drop):?>
	                   <?php if(is_array($drop)):?>
	                     <li><a href="<?php echo $drop['link']?>"><?php echo $drop['label']?></a></li>
	                   <?php else:?>
	                     <li class="divider"></li>
	                   <?php endif?>
                   <?php endforeach;?>
                   </ul>
</div>
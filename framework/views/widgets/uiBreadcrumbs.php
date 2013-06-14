<ul class="breadcrumb">
     <li><a href="<?php echo urlhelper('home')?>">首页</a> 
     <span class="divider"><?php echo $widget->seperator?></span></li>
     <?php 
     $index = 0;
     foreach ($widget->links as $title=>$link):?>
     <?php $index++;?>
     <?php if($link):?>
     <li><a href="<?php echo $link?>"><?php echo $title?></a> 
     <?php if($index<count($widget->links)):?><span class="divider"><?php echo $widget->seperator?></span><?php endif;?>
     </li>
     <?php else:?>
     <li class="active"><?php echo $title?>
     <?php if($index<count($widget->links)):?><span class="divider"><?php echo $widget->seperator?></span><?php endif;?>
     
     </li>
     <?php endif?>
     <?php endforeach?>
     
     <li class="pull-right">
     </li>
     
</ul>
<?php
/* @var $widget UINavBar */
?>
<div class="navbar navbar-inversed">
	<div class="navbar-inner" style="border:0;padding-left:0px;padding-bottom:0px;background-color:# ;color:#fff">
         <ul class="nav">
         <?php foreach($widget->items as $item):?>
         <?php if(is_array($item)):?>
         	<?php if($item['type']=='dropdown'):?>
	         	<li class="dropdown">
	           	<a href="<?php echo ($item['url']?$item['url']:'#')?>" 
	           		class="dropdown-toggle" data-toggle="dropdown">
	           	<?php if($item['properties']['icon']):?>
	           		<i class="<?php echo $item['properties']['icon']?>"></i>
	           	<?php endif?>
	           	<?php echo $item['label']?> <b class="caret"></b></a>
             		<ul class="dropdown-menu">
             		<?php if($item['childs']):?>
             		<?php foreach($item['childs'] as $child):?>
             			<?php if($child['type']=='divider'):?>
                          <li class="divider"></li>
             			<?php else:?>
                          <li>
                          	<?php if($child['icon']):?>
	           				<i class="<?php echo $child['icon']?>"></i>
				           	<?php endif?>
				           	<a href="<?php echo $child['url']?>"><?php echo $child['label']?></a>
				          </li>
				        <?php endif?>
                    <?php endforeach?>
                    <?php endif?>
                        </ul>
                      </li>
         	<?php else:?>
         	<li><a href="<?php echo $item['url']?>"><i class="<?php echo $item['properties']['icon']?>" style="font-size:14px"></i><?php echo $item['label']?></a></li>
         	<?php endif?>
         <?php else:?>
         <?php echo $item;?>
         <?php endif?>
         <?php endforeach;?>
      	</ul>
        <form class="navbar-form pull-right hide">
                     <input type="text" class="span2">
                     <button type="submit" class="btn">查找</button>
                     
				     <button class="btn btn-link dropdown-toggle" data-toggle="dropdown" href="#">高级查找</button>
        </form>
	</div>
</div>
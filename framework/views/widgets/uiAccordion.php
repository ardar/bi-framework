
<div id="<?php echo $widget->GetId()?>" class="accordion" >

  <?php if($widget->childs) :foreach($widget->childs as $childid => $child):?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse"
				data-parent="#accordion<?php echo $childid?>" href="#collapse<?php echo $childid?>"><?php echo $child->label?></a>
		</div>
		<div id="collapse<?php echo $childid?>" class="accordion-body in"
			style="height: auto;">
			<div class="accordion-inner">
				<?php echo $child->GetBody()?>
			</div>
		</div>
	</div>
  <?php endforeach; endif;?>
</div>
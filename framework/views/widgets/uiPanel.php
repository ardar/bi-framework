<div class="panel" <?php echo $widget->GetHtmlOptions();?>>
<?php if($widget->icon || $widget->label):?>
	<header class="panel-header">
		<i class="<?php echo $widget->icon?>"></i> <span><?php echo $widget->label?> 
		</span>
		
	</header>
<?php endif;?>
	<div class="content">
	<?php echo $widget->tabBar?>
<?php echo $content?>
	</div>
</div>
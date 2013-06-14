<?php /* @var $controller FwController */ ?>

			
	<?php if($controller->navBar):?>
	<div class="container-fluid ">
		<?php echo $controller->navBar;?>    
    </div>
    <?php endif?>
    
    <!-- breadcrumbs -->
	<?php if($controller->breadcrumbs):?>
		<div class="container-fluid">
		<?php echo $controller->breadcrumbs?>
		</div> 
	<?php endif?>
	
    <!-- Navbar title -->
    <?php if ($controller->showTitleBar):?>
    <div class="nav">
	   <h2>
	   <?php if($controller->backUrl):?>
			<a class="win-command" href="<?php echo $controller->backUrl?>">
				<span style="font-size:16px;line-height:28px;width:28px;height:28px;" class="win-commandimage win-commandring"></span>
			</a>
		<?php endif?>
		<?php echo $controller->pageTitle?> 
		<small><?php echo $controller->subTitle?></small>
	   </h2>
	</div>
	<?php endif;?>
	
	<!-- Tabbar -->
    <?php if ($controller->tabBar):?>
		<?php echo $controller->tabBar?>
	<?php endif;?>
	
	<!-- content -->
	<?php echo $content; ?>
	<!-- content end -->
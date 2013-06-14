
<div class="container-fluid content">
	

<?php 
if($widget->properties['showBox']):
$this->beginWidget('UIPanel', array('label'=>$widget['_title'], 'icond'=>'ico-list'));
endif;?>
<?php echo $widget['_tabBar'];?>
<?php if($widget['_headbar'] || $widget['_quickSearcher'] || $widget['_searcher']):?>
	<div class="row-fluid">
	<div class="span12">
	<div class="pull-left">
	<?php echo $widget['_headbar']?>
	</div>
		<div class="pull-left hide">
	   <h2><?php echo $widget['_title']?></h2>
	   <?php if ($widget['_subtitle']):?><p><?php echo $widget['_subtitle']?></p><?php endif?>
		</div>
		<?php if($widget['_quickSearcher']):?>
				<div class="input-append pull-right">
				<form action='' method='get'>
					<?php echo $widget['_quickSearcher']?>
					<?php if($widget['_searcher']):?>
		            <button data-toggle="modal" href="#searchModal" 
		            class="btn">高级搜索</button>
		            <?php endif?>
				</form>
				</div>
				
			 
		<?php endif?>
	</div>
	
   	</div>
<?php endif?>
   	
	<form id="dataform_<?php echo $widget['_id']?>" name=dataform method=Post>
	<div class="row-fluid">
		<div class="span12">
              <?php echo $widget['_content']?>
			
				<?php if ($widget['_footer']<>''):?>
					<?php echo $widget['_footer']?>
				<?php endif?>
				
				<?php echo $widget['_hidden']?>
				
				<?php if ($widget['_pager']<>''):?>
				<div class="">
					<?php echo $widget['_pager']?>
				</div>
				<?php endif?>
		</div>
	</div>
	</form>
<?php 
if($widget->properties['showBox']):
$this->endWidget();
endif;?>
</div>

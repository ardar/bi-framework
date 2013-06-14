<?php
/* @var $widget UITabs */
?>
<div class="tabbable tabs-<?php echo $widget->placement?>"> <!-- Only required for left/right tabs -->
					<ul class="nav nav-tabs <?php echo $widget->properties['tab_class']?>">
					
				<?php foreach ($widget->pages as $pageId=>$page):
				$toggleFlag = '';
				if(!$page['link'])
				{
					$page['link'] = "#tab_{$widget->id}_{$pageId}";
					$toggleFlag = " data-toggle=\"tab\"";
				}
				?>
						<li class="<?php if($widget->activePage==$pageId):?>active<?php endif?> ">
				       <a href="<?php echo $page['link']?>" <?php echo $toggleFlag?>>
				       <?php if($page['properties']['icon']):?><i class="<?php echo $page['properties']['icon']?>"></i><?php endif?>
				       <?php echo $page['label'];?>
				       <?php if($page['properties']['label']!==null):?>
				       <label class="label <?php echo $page['properties']['labelclass']?>"><?php echo $page['properties']['label']?></label>
				       <?php endif?>
				       </a>
				    </li>
				<?php endforeach?>
					</ul>
				<div class="tab-content <?php echo $widget->properties['content_class']?>">
				<?php foreach ($widget->pages as $pageId=>$page):?>
		       <div class="tab-pane <?php if($widget->activePage==$pageId):?>active<?php endif?>"
		        id="tab_<?php echo $widget->id?>_<?php echo $pageId?>">
		        <?php echo $page['content'];?>
		       </div>
		    <?php endforeach?>
				</div>
</div>
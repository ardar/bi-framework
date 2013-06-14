
<div class="container-fluid">
	

<?php 
if($widget['_params']['showBox']):
$this->beginWidget('UIPanel', array('label'=>$widget['_title'], 'icond'=>'ico-list'));
endif;?>
<?php echo $widget['_tabBar'];?>
<?php if($widget['_headbar'] || $widget['_quickSearcher'] || $widget['_searcher']):?>
	<div class="row-fluid">
	<div class="span12">
	
		<?php if($widget['_quickSearcher'] || $widget['_searcher']):?>
				<div class="input-append pull-right">
				<form action='' method='get'>
					<?php if($widget['_quickSearcher']):?>
						<?php echo $widget['_quickSearcher']?>
					<?php endif;?>
					<?php if($widget['_searcher']):?>
		            <button data-toggle="modal" href="#searchModal" 
		            class="btn"><?php if($widget['_params']['advSearchLabel']):?>
			            <?php echo ($widget['_params']['advSearchLabel']);?>
			            <?php else:?>高级搜索
			            <?php endif?></button>
		            <?php endif?>
				</form>
				</div>
		<?php endif?>
<?php if(!$widget['_noform']):?>
<form id="dataform_<?php echo $widget['_id']?>" name=dataform method=Post>
<?php endif;?>
	<div class="pull-left">
	<?php echo $widget['_headbar']?>
	</div>
		<div class="pull-left hide">
	   <h2><?php echo $widget['_title']?></h2>
	   <?php if ($widget['_subtitle']):?><p><?php echo $widget['_subtitle']?></p><?php endif?>
		</div>
	</div>
</div>
<?php endif?>
<?php if(!$widget['_params']['noContent']):?>
	<div class="row-fluid">
		<div class="span12">
              <?php if($widget['_fields']):?>
              <table class="table table-striped table-condensed " style="margin-top:10px">
				<thead>
					<tr>
					<?php foreach ($widget['_fields'] as $field):?> 
						<?php if ($field['type']<>"hidden"):?>
						<th
						<?php if($field['type']=='checkbox' || $field['type']=='checkbool'):?>
						style="width:20px"
						<?php endif?>
						<?php if($field['params']['class']):?>
						class="<?php echo $field['params']['class']?>"
						<?php endif?>
						><?php echo $field['title']?></th>
						<?php endif?>
					<?php endforeach?>
					</tr>
				</thead>
			<?php if ($widget['_rows'] && count ( $widget['_rows'] ) > 0) :?>
				<tbody>
				<?php
				foreach ( $widget['_rows'] as $index => $rs ) :?>
				<tr>
				    <?php foreach ($widget['_fields'] as $fieldkey=>$field):?> 
					<?php if ($field['type']<>"hidden"):?>
				    <td <?php echo $field['ext']['htmlOptions']?> class="align-<?php echo $field['align']?>">
				    <?php echo $rs[$fieldkey]?>
				    </td>
				    <?php endif?>
				    <?php endforeach?>
				</tr>
				<?php endforeach?>
				</tbody>
			<?php else:?>
			
			<?php endif?>
			</table>
			<?php endif;?>
			
				<?php if ($widget['_footer']<>''):?>
					<?php echo $widget['_footer']?>
				<?php endif?>
				<div class="span6 pull-left hide">
				<button class="btn-large btn-success" type="button"><i class="ico-copy"></i>添加</button>
				<button class="btn-large btn-info" type="button"><i class="ico-copy"></i>复制</button>
			     <button class="btn-large btn-danger" type="button"><i class="ico-remove"></i>删除</button>
				</div>
				<?php echo $widget['_hidden']?>
				
				<?php if ($widget['_pager']<>''):?>
				<div class="">
					<?php echo $widget['_pager']?>
				</div>
				<?php endif?>
		</div>
	</div>
<?php endif?>
<?php if(!$widget['_noform']):?>
	</form>
<?php endif?>
<?php 
if($widget['_params']['showBox']):
$this->endWidget();
endif;?>
</div>

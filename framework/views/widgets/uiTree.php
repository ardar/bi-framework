<?php //@var $widget UITree?>
<link rel="stylesheet" type="text/css" href="3rdparty/ztree/ui.ztree.css">
<script  type="text/javascript" src="3rdparty/ztree/jquery.ztree.js"></script>
<script  type="text/javascript">
<!--
var <?php echo $widget->GetId()?>_setting = {
		<?php if($widget->isSimpleData){?>
		data: {
			simpleData: {enable: true}
		},
		<?php }?>
		<?php if($widget->checkEnabled){?>
		check: {
			enable: true
		},
		callback: {
			onCheck: <?php echo $widget->GetId()?>_onChecked
		},
		<?php }?>
		<?php if($widget->isAsync){?>
		async: {
			enable: true,
			url:"index.php",
			autoParam:["id"],
			otherParam:{"controller":"<?echo $widget->controller?>","action":"<?echo $widget->action?>"},
			dataFilter: filter
		},
		<?php }else{//localdata?>
		<?php }?>
};
var <?php echo $widget->GetId()?>_zNodes = <?php echo $widget->jsonData?>;

function <?php echo $widget->GetId()?>_onChecked(e, treeId, treeNode) {
	var zTree = $.fn.zTree.getZTreeObj(treeId);
	var checkedNodes = zTree.getCheckedNodes(true);
	var checkCount = 0;
	var newval = '';
	for(var i=0;i<checkedNodes.length;i++)
	{
		var treeNode = checkedNodes[i];
		if(treeNode.checked )
		{
			if(!treeNode.disabled)
			{
				if(newval!='')
				{
					newval+=',';
				}
				newval += treeNode.id;
			}
			checkCount++;
		}
	}
	window.document.getElementById('<?php echo $widget->GetId()?>').value = newval;
}

$(document).ready(function(){
$.fn.zTree.init($("#<?php echo $widget->GetId()?>_tree"), <?php echo $widget->GetId()?>_setting, <?php echo $widget->GetId()?>_zNodes);
});
//-->
</script>
<input type="hidden" name="<?php echo $widget->GetId()?>" id="<?php echo $widget->GetId()?>" value="<?php echo $widget->value?>">
<ul id="<?php echo $widget->GetId()?>_tree" class="" style="margin:0px; padding:5px;border:0px;"></ul>
<?php
/* @var $widget UINavBar */
?>
<div class="container-fluid">

	<div class="row-fluid navbar"
		style=" height: 70px; margin-bottom: 8px;">

		<div class="span6">
			<div id="header-container">

				<div style="float:left;display:inline-block;padding-right:15px;margin-top:12px;">
				<button class="win-command">
					<span class="win-commandimage win-commandring"></span>
				</button>
				</div>
				<div style="float:left;display:inline-block">
				<h5>
					<?php echo ($widget->parentTitle);?>
					&nbsp;
				</h5>
				<div class="dropdown">

					<a class="header-dropdown dropdown-toggle accent-color"
						data-toggle="dropdown" href="#"><i
						class="<?php echo $widget->pageIcon?>"></i> <?php echo $widget->pageTitle?>
						<b class="caret"></b> </a> <small><?php echo $widget->subTitle?> </small>
					<ul class="dropdown-menu">
						<li><a href="./hub.html"><i class="ico-user"></i>用户管理</a></li>
						<li><a href="./tiles-templates.html"><i class="ico-cog"></i>角色管理</a>
						</li>
						<li><a href="./listviews.html">ListViews</a></li>
						<li class="divider"></li>
						<li><a href="./index.html">首页</a></li>
					</ul>
				</div>
				</div>
			</div>
		</div>
		<div class="span6 pull-right">
			<div class="pull-right">
		<hr class="win-command hidden">
				<button class="win-command hidden">
					<span class="win-commandimage win-commandring"></span><span
						class="win-label">编辑用户</span>
				</button>
				<button class="win-command hidden">
					<span class="win-commandimage win-commandring"><i class="ico-user"></i>
					</span> <span class="win-label">删除用户</span>
				</button>
				
		</div>
	</div>
	<?php if($widget->description):?>
	<div class="row-fluid ">
		<div class="well">
			<button type="button" class="close" data-dismiss="alert"></button>
			<p>
				<?php echo $widget->description?>
			</p>
		</div>
	</div>
	<?php endif?>
</div>

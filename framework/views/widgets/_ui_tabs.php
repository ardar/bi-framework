<script>
    $(function() {
    	$( "#<?php echo $tabs->GetId()?>_tabs" ).tabs({
            beforeLoad: function( event, ui ) {
                ui.jqXHR.error(function() {
                    ui.panel.html(
                        "无法载入页面" );
                });
            }
        });
        <?php if($tabs->isVertical){?>
        $( "#<?php echo $tabs->GetId()?>_tabs" ).addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#<?php echo $tabs->GetId()?>_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        <?php }//if?>
    });
</script>
<style>
    .ui-tabs-vertical {  width:80em;}
    .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
    .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
    .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
    .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
    .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
</style>
<div id="<?php echo $tabs->GetId()?>_tabs">
    <ul>
    <?php foreach($tabs->tabPages as $pageId=>$page){?>
        <li><a href="<?php if ($page['type']=='url'){echo $page['content'];}else{?>tabpage_<?php echo $tabs->GetId()?>_<?php echo $page['id']?><?php }?>" 
        ><?php echo $page['label']?></a></li>
    <?php }//foreach?>
    </ul>
    <?php foreach($tabs->tabPages as $pageId=>$page){?>
    <?php if($page['type']!='url'){?>
    <div id="tabpage_<?php echo $tabs->GetId()?>_<?php echo $page['id']?>">
    <?php echo $page['content']?>
    </div>
    <?php }//if?>
    <?php }//foreach?>
</div>
<?php /* @var $this FwController */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<title><?php echo $this->pageTitle?> <?php echo $this->subTitle?></title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">


<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<linkd rel="stylesheet" type="text/css" href="css/bootmetro.css" />
<linkd rel="stylesheet" type="text/css" href="css/bootmetro-ui-light.css" />
<link rel="stylesheet" type="text/css" href="css/mainframe.css" />
<link rel="stylesheet" type="text/css" href="css/application.css" />
<link rel="stylesheet" type="text/css" href="css/iconset.css" />
<link rel="stylesheet" type="text/css" href="css/icomoon.css" />
<link rel="stylesheet" type="text/css" href="3rdparty/msggrowl/css/msgGrowl.css" />



<script src="js/json2.js"></script>
<script src="js/jquery.js"></script>
<script src="js/jquery.sparkline.min.js"></script>
<script src="js/spin.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/application.js"></script>
<script src="3rdparty/msggrowl/msgGrowl.js"></script>
<script src="3rdparty/jqchart/jquery.jqChart.js"></script>
<?php echo Fw::Html()->GetHeaderHtml();?>
</head>

<body>
	<div class="topbannar">
				<a class="brand" href="<?php echo urlhelper('home')?>"><span class="inline"></span><img border=0 src="pic/LOGO.jpg" style="margin-top:5px;height:40px;border-radius:5px;" alt=""
					 /> </a>

				<div class="top-menu pull-right">
					<a href="<?php echo $controller->GetUrl('System/Account','profile')?>" class="dropdown-toggle top-menu-item"
						> 
						<img border=0 src="pic/user.png" width="16" height="17" />
						<?php echo $controller->getUser()->username?> (<?php echo $controller->getUser()->groupname?>)
					</a>

					<a href="javascript:void(0)" data-target="msgmenu" class="dropdown-toggle top-menu-item"
						data-toggle="dropdown"> <img border=0 src="pic/msg.png" width="16"
						height="11" /> 短消息(<?php echo '0'?>)
					</a>
					<ul id="msgmenu" role="menu" class="dropdown-menu">
						<li><a style="color:#333" href="<?php echo $controller->GetUrl('Msg','list',array('box'=>''));?>">查看未读消息</a></li>
						<li class="divider"></li>
						<li><a style="color:#333" href="<?php echo $controller->GetUrl('Msg','send');?>">发送新消息</a></li>
					</ul>

					<a href="<?php echo urlhelper('logout');?>" class=" top-menu-item"> <img border=0
						src="pic/loginout.png" width="16" height="18" /> 退出
					</a>
				</div>

	</div>

	<div class="leftbanner">
		<ul id="nav">
		<li><a href="<?php echo urlhelper('home')?>"><img border=0 src="pic/menu/Home.png" alt="" width="28" height="28" />首页</a></li>

<?php 
/*$menu = $controller->GetAppMenu();
if($menu): 
foreach($menu as $index=>$root):?>
    <li><a class="hsubs" href="<?php echo $root['url']?$root['url']:'javascript:void(0);'?>"
     id="hsub<?php echo $index?>">
    	<?php if($root['image']):?>
         <IMG SRC="pic/menu/<?php echo $root['image']?>" BORDER="0" width="28" height="28"/>
      	<?php else:?>
         <IMG SRC="pic/menu/Gear.png" BORDER="0" width="28" height="28"/>
      	<?php endif?><?php echo $root['title']?></a>
        <ul id="sub<?php echo $index?>" class="subs" >
		<?php if($root['subs']): foreach($root['subs'] as $sub):?>
            <li><a href="<?php echo $sub['url']?>" ><?php echo $sub['title']?></a></li>
		<?php endforeach;endif;?>
        </ul>
    </li>
<?php endforeach;
endif;*/?>

		</ul>
	</div>

	<div class="rightbanner">
		<a id="right_add_draft" href="<?php echo $controller->GetUrl('GTD/Draft','add')?>" 
		title="将未能及时处理的工作信息投入信息篮"><IMG
			style="margin: 5px; margin-left: 12px;" SRC="pic/menu/Screenshot.png"
			BORDER="0" width="28" height="28" /> </a>
		<IMG style="margin: 5px; margin-left: 12px;" SRC="pic/menu/MSN.png"
			BORDER="0" width="28" height="28" class="hide"/>
	</div>

	<div class="main" style="overfloat:scroll;">
		<?php echo $content?>
	</div>
	
	
<div class="footbanner">技术支持:成都亿信互动科技有限公司</div>

<?php 
$htmls = Fw::Html()->GetHeader('html');
if($htmls)foreach($htmls as $html){
	echo $html;
}
?>

<div id="systemModal" style="display: none;width:560px;"
 class="modal hide fade" 
tabindex="-1" role="dialog" aria-labelledby="systemModalLabel" aria-hidden="true" >
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h3 id="systemModalLabel"></h3>
	</div>
	<div id="systemModalIFrameBody" class="systemModal-body" style="padding:10px;">
        <iframe src="" id="systemModalIFrame" class=""
         style="width:100%;padding:0px;border:0;overflow:scroll" scrolling="auto" ></iframe>
     </div>
     
	<div id="systemModalBody" class="systemModal-body" style="padding:10px;overflow:scroll"></div>
    
	<div id="systemModalFooter" class="modal-footer">
	                 <button type="submit" class="btn btn-primary">确定</button>
	                 <button class="btn" data-dismiss="modal">关闭</button>
	</div>
</div>

<!-- msg -->
<script>	
TotalDisplayingMsgs = 0;
FetchedMsgIds = '';
function fetchNewMsg()
{
	if(TotalDisplayingMsgs <1)
	$.ajax({
        url: "<?php echo $controller->GetUrl('Msg','getnewmsgjson')?>",
        type: "GET",
        dataType: "json",
        data: {fetchedids:FetchedMsgIds},
        contentType: "application/json; charset=utf-8",
        success: function(json) {
            if(json!=null)
            {
                for(var i=0;i<json.length;i++)
                {
                    var msg = json[i];
		        	$.msgGrowl ({
		        		type: 'info'
		        		, title: msg.title
		        		, text: msg.content
		        		, lifetime: 10000
		        		, sticky: true
		        		, onClose: function(){
		        				TotalDisplayingMsgs--;
		        				$.post("<?php echo $controller->GetUrl('Msg','setread')?>",
		        				  {
		        				    msgid: msg.msgid
		        				  },
		        				  function(data,status){
		        				  }
		        				);
		        			}
		        	});	
		        	TotalDisplayingMsgs++;
		        	FetchedMsgIds+= ","+msg.msgid;
                }
            }
        },
        error: function(x, e) {
            //alert("Error:"+x.responsetText);
        }
    });

    //setTimeout (fetchNewMsg, <?php echo Fw::GetApp()->GetModule('System')->GetSysOption(1, 'msg_refresh_time');?>);
}
$(document).ready(
function(){
	//setTimeout (fetchNewMsg, 3000);	
}
);
</script>
</body>
</html>

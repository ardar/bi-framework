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
<link rel="stylesheet" type="text/css" hredf="css/bootmetro.css" />
<link rel="stylesheet" type="text/css" hredf="css/bootmetro-ui-light.css" />
<link rel="stylesheet" type="text/css" href="css/application.css" />
<link rel="stylesheet" type="text/css" href="css/iconset.css" />
<link rel="stylesheet" type="text/css" href="css/icomoon.css" />

<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/application.js"></script>
<?php echo Fw::Html()->GetHeaderHtml();?>
</head>

<body>
<?php echo $content?>
		

<?php 
$htmls = Fw::Html()->GetHeader('html');
if($htmls)foreach($htmls as $html){
	echo $html;
}
?>

</body>
</html>

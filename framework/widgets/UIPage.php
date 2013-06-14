<?php
class UIPage extends UIWidget
{
	public $viewFile = "uiPage_Default.php";
	
	public $breadcrumbs;
	public $pageTitle;
	public $pageIcon;
	public $parentTitle;
	public $subTitle;
	public $pageMenu;
	public $pageButtons;
	
	public $notice;
	public $error;
	public $warning;
	
	/**
	 * the toolbar control of this page
	 * @var UIToolbar
	 */
	public $toolbar;
	
}
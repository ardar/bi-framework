<?php
/**
 * framework all in one including file.
 */

error_reporting(E_ALL ^ E_NOTICE);

$dir = __DIR__."/";
$dir = str_replace("\\",'/', $dir);
$dir = str_replace('/Core/', '/', $dir);

/**
 * @var string The framework root directory
 */
define("FW_DIR", $dir);


require_once FW_DIR.'core/Constants.php';
require_once FW_DIR.'core/AppSetting.php';
require_once FW_DIR.'interfaces/IApplication.php';
require_once FW_DIR.'interfaces/IView.php';
require_once FW_DIR.'interfaces/IModule.php';
require_once FW_DIR.'interfaces/IRequest.php';
require_once FW_DIR.'interfaces/IResponse.php';
require_once FW_DIR.'interfaces/ILogger.php';
require_once FW_DIR.'interfaces/ICache.php';
require_once FW_DIR.'interfaces/IRuntime.php';
require_once FW_DIR.'interfaces/IRole.php';
require_once FW_DIR.'interfaces/IUser.php';
require_once FW_DIR.'interfaces/IDatabase.php';
require_once FW_DIR.'interfaces/IWidget.php';
require_once FW_DIR.'core/Fw.php';
require_once FW_DIR.'core/FwLoader.php';
require_once FW_DIR.'core/Application.php';
require_once FW_DIR.'core/UrlHelper.php';


<?php

/*
|--------------------------------------------------------------------------
| PATH BASE
|--------------------------------------------------------------------------
*/

define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/AFEC');
define('ASSETS',BASE_URL.'/public/assets');


/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
require_once ROOT_PATH  .   '/app/helpers/view_helper.php';
require_once ROOT_PATH  .   '/app/helpers/section_helper.php';
require_once ROOT_PATH  .   '/app/helpers/TableBuilder.php'; 
require_once ROOT_PATH  .   '/app/helpers/pagination_helper.php';
require_once ROOT_PATH  .   '/app/helpers/modal_helper.php';
require_once ROOT_PATH  .   '/app/helpers/flash_helper.php';
require_once ROOT_PATH  .   '/app/helpers/redirect_helper.php';
require_once ROOT_PATH  .   '/app/core/Autoload.php';

/*
|--------------------------------------------------------------------------
| CORE
|--------------------------------------------------------------------------
*/

require_once ROOT_PATH.'/app/core/Auth.php';
require_once ROOT_PATH.'/app/core/Router.php';

?>
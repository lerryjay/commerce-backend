<?php
date_default_timezone_set('Africa/Lagos');

/**
 *
 * Default error log file
 */

define('APP_ROOT_DIR', 'commerce-backend/');

/*
 *
 * PRODUCTION_MODE
 *
 */
define('PRODUCTION_MODE', false);

/**
 *
 * Default error log file
 */

define('ERROR_LOG_FILE', BASE_PATH . APP_ROOT_DIR . 'error.log');

/*
 *
 * Default loaded libraries
 *
 */
$default_libraries = [
    'request' => 'http/requests',
    'file' => 'files/localfile',
    'encrypt' => 'helpers/encrypt',
    'validator' => 'helpers/validator',
];

define('DB_ENGINE', 'mysql');

define('IMAGE_FILE_FORMAT', [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'apng',
    'svg',
    'bmp',
]);

define('VIDEO_FILE_FORMAT', ['mp4', 'mov', 'mpe', 'mpeg', 'mpa', 'mpg']);

define('DOC_FILE_FORMAT', [
    'ods',
    'xls',
    'xlsm',
    'xlsx',
    'pdf',
    'doc',
    'docx',
    'txt',
]);

define('ZIP_FILE_FORMAT', ['zip', 'rar']);

define('APP_PERMISSIONS', [
    'VIEWORDERS,ADDORDERS',
    'ADDADMIN',
    'VIEWADMIN',
    'SUSPENDADMIN',
]);

/*
 *
 *  DATABASE = mysql,mongodb,postgres
 *
 */

define('DATABASE', 'mysql');

define('LOAD_DATABASE', true);

/*
 *
 *  DATABASE CONFIGURATIONS
 *  wallsan1_wsv1
 *  l)!T3ZsqQI)e
 */

$load_db = true;
$db_server = 'localhost';
$db_user = 'root';
$db_password = '';
$db = 'logistics';

// $load_db     = true;
// $db_server   = 'localhost';
// $db_user 	   = 'tdlexpre_app';
// $db_password = 'G?*O2?h@vsUI';
// $db 		     = 'tdlexpre_app';

/*
 *
 *JWT SECRET KEY
 * GENERATE NEW
 * DO NOT USE THIS DEFAULT KEY
 */

define(
    'JWT_SECRET_KEY',
    '9e162925786c29382325053249eceb4e2e2d0e1de9c95f2bd988f83b250d50b0'
);

define('EMAIL_INFO_ACCOUNT', 'info@tdlexpress.com.ng');
define('VAT_RATE', 10);

define('PAYSTACK_LIVE_SECRET', '');
define(
    'PAYSTACK_TEST_SECRET',
    'sk_test_538d92a8531065f4acd77076b3cc10aa46362792'
);
?>

<?php
date_default_timezone_set('Europe/Warsaw');

define("ROOT_URI", "/eclipse/squash/app/www");
define('ROOT_WWW', 'http://localhost'.ROOT_URI);
define('LOG_DIR', ROOT_DIR . '/app/var/log');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'seled_stuff');
#define('DB_LOG_FILE', LOG_DIR."/db.log");

define('LOG_LEVEL_WWW', LOG_DEBUG);
# OUTPUT_STDOUT = 1; const OUTPUT_FILE = 2;#
define('LOG_OUTPUT_WWW', 2);
define('LOG_AGGREGATE_WWW', false);
define('LOG_FILE_WWW', LOG_DIR .'/flite_app_logs');

define('UNIQUE_COOKIE_NAME', 'flite_unique');
define('SESSION_COOKIE_NAME', 'flite_session');
define('SESSION_EXPIRES', 3600);

#AUTH
#AUTH_TABLE user
#AUTH_LOGIN_FIELD login
#AUTH_PASS_FIELD pass
#AUTH_PASS_HASH md5
//ini_set('display_erorrs', 1);

define('CONTROLLER_MAIN', 'Main');
define('METHOD_MAIN', 'index');

define('DEBUG', true);

require_once ROOT_DIR . "/flite/FLite.class.php";
require_once ROOT_DIR . "/flite/FBase.class.php";
?>
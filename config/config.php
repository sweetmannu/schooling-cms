<?php

define('APP_NAME','Schooling Education');

define('APP_URL','http://localhost/schooling-cms');

define('DB_HOST','localhost');
define('DB_NAME','schooling_cms');
define('DB_USER','root');
define('DB_PASS','');

date_default_timezone_set('Asia/Kolkata');

if(session_status()==PHP_SESSION_NONE){
session_start();
}
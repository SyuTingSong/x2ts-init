<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 2015/11/20
 * Time: 下午3:35
 */

define('X_PROJECT_ROOT', dirname(__DIR__));
define('X_DEBUG', true);

require_once(X_PROJECT_ROOT . '/protected/x2ts/autoload.php');
require_once(X_PROJECT_ROOT . '/protected/common.php');

ini_set('display_errors', X_DEBUG ? 'On' : 'Off');

/**
 * Class ComponentFactory
 * @method static \x2ts\db\Redis redis()
 * @method static \lang\Zh intl()
 */
class X extends x2ts\ComponentFactory {}

X::conf(require(X_PROJECT_ROOT . '/protected/config/debug.php'));
if (!X_DEBUG) 
    X::conf(require(X_PROJECT_ROOT . '/protected/config/release.php'));

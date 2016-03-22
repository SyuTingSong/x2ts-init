<?php
return array(
    'component' => array(
        'router' => array(
            'class' => '\x2ts\app\Router',
            'singleton' => true,
            'conf' => array(
                'gzip' => 5,
                'defaultAction' => '/index',
                'baseUri' => '/'
            ),
        ),
        'db' => array(
            'class' => '\x2ts\db\MySQL',
            'singleton' => true,
            'conf' => array(
                'host' => 'localhost',
                'port' => 3306,
                'user' => 'test',
                'password' => 'test',
                'dbname' => 'test',
                'charset' => 'utf8mb4',
                'persistent' => false,
            ),
        ),
        'model' => array(
            'class' => '\x2ts\db\orm\Model',
            'singleton' => false,
            'conf' => array(
                'tablePrefix' => '',
                'dbId' => 'db',
                'enableCacheByDefault' => false,
                'schemaConf' => array(
                    'schemaCacheId' => 'cc',
                    'useSchemaCache' => true,
                    'schemaCacheDuration' => 30,
                ),
                'cacheConf' => array(
                    'cacheId' => 'cache',
                    'duration' => 15,
                ),
            )
        ),
        'cache' => array(
            'class' => '\x2ts\cache\RCache',
            'singleton' => true,
            'conf' => array(
                'host' => 'localhost',
                'port' => 6379, //int, 6379 by default
                'timeout' => 0, //float, value in seconds, default is 0 meaning unlimited
                'persistent' => false, //bool, false by default
                'database' => 7, //number, 0 by default
                'auth' => null, //string, null by default
                'keyPrefix' => 'rc',
            ),
        ),
        'cc' => array(
            'class' => '\x2ts\cache\CCache',
            'singleton' => true,
            'conf' => array(
                'cacheDir' => X_RUNTIME_ROOT . '/cache',
            ),
        ),
        'view' => array(
            'class' => '\x2ts\view\Hail',
            'singleton' => true,
            'conf' => array(
                'tpl_dir' => X_PROJECT_ROOT . '/protected/view',
                'tpl_ext' => 'html',
                'compile_dir' => X_RUNTIME_ROOT . '/compiled_template',
                'enable_clip' => true,
                'cacheId' => 'cc', // string to cache component id or false to disable cache
                'cacheDuration' => 60, // page cache duration, second
            )
        ),
        'redis' => array(
            'class' => '\x2ts\db\Redis',
            'singleton' => true,
            'conf' => array(
                'host' => 'localhost',
                'port' => 6379,
                'timeout' => 0,
                'persistent' => false,
                'database' => 0,
                'auth' => null,
                'keyPrefix' => 'test',
            ),
        ),
        'intl' => array(
            'class' => '\x2ts\i18n\Internationalization',
            'singleton' => false,
            'conf' => array(
                'default' => 'Zh',
            ),
        ),
        'oss' => array(
            'class' => '\lib\oss\AliOSS',
            'singleton' => true,
            'conf' => array(
                'id' => 'eNmkmNQ2ZCyPHPbj',
                'key' => 'WveKOhu5ucRmsbYkxQuItO9rYqNBDm',
                'host' => 'oss-cn-beijing-internal.aliyuncs.com',
            ),
        ),
    ),
);

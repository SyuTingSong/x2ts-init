<?php

use x2ts\app\Action;
use x2ts\Component;

class CommonAction extends Action {
    public function isAjax() {
        return !empty($this->header('X_REQUESTED_WITH')) || $this->query('ajax');
    }

    /**
     * @return \model\User
     * @throws \x2ts\app\ApplicationExitException
     */
    public function loginRequired() {
        //TODO implement this
    }
}

class I18nMessage extends Component {
    protected static $_conf = array(
        'default' => 'zh_CN',
    );
    protected static $messages;
    public static function getInstance($locale = 'zh_CN') {
        if (!static::$messages[$locale] instanceof I18nMessage) {
            static::$messages[$locale] = new I18nMessage();
        }
        return static::$messages[$locale];
    }
    
    public $to = '世界';
}
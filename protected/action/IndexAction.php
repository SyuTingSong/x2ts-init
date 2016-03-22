<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 15/7/26
 * Time: 下午8:13
 */

namespace action;


use CommonAction;
use X;

class IndexAction extends CommonAction {
    public function httpGet() {
        $this->display([
            'to' => X::intl()->to,
        ]);
    }
}
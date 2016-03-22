<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 2016/3/22
 * Time: 下午4:20
 */

namespace action\api;


use CommonAction;

class TestAction extends CommonAction {
    public function httpGet() {
        $this->jsonEcho("Everything is OK!", ['test' => 'test content']);
    }
}
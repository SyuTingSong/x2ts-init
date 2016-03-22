<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 2016/3/22
 * Time: 下午4:22
 */

namespace model;


use x2ts\db\orm\Model;

class User extends Model {
    public function getTableName() {
        return 'user';
    }
}
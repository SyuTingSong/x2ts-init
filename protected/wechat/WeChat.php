<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 15/8/17
 * Time: 下午5:51
 */

namespace wechat;


use x2ts\cache\ICache;
use x2ts\Component;
use x2ts\Toolkit;
use x2ts\ComponentFactory;

/**
 * Class WeChat
 * @package wechat
 *
 * @proerty-read ICache $cache
 */
class WeChat extends Component {
    const SCOPE_BASE = 'snsapi_base';
    const SCOPE_LOGIN = 'snsapi_login';
    const SCOPE_USERINFO = 'snsapi_userinfo';

    protected static $_conf = array(
        'AppID' => '',
        'AppSecret' => '',
        'OpenAppID' => '',
        'OpenSecret' => '',
        'CacheID' => 'cache',
        'Callback' => '',
    );

    public function oauthUrl($scope = '', $state = 0) {
        if ($scope == self::SCOPE_LOGIN) {
            $url = 'https://open.weixin.qq.com/connect/qrconnect?appid='
                . $this->conf['OpenAppID']
                . '&redirect_uri=' . $this->conf['Callback']
                . '&response_type=code&scope=' . $scope
                . '&state=' . $state . '#wechat_redirect';
        } else {
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
                . $this->conf['AppID']
                . '&redirect_uri=' . $this->conf['Callback']
                . '&response_type=code&scope=' . $scope
                . '&state=' . $state . '#wechat_redirect';
        }
        return $url;
    }

    public function getUserAccessToken($code, $open) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='
            . ($open ? $this->conf['OpenAppID'] : $this->conf['AppID'])
            . '&secret=' . ($open ? $this->conf['OpenSecret'] : $this->conf['AppSecret'])
            . '&code=' . $code . '&grant_type=authorization_code';
        var_dump($url);
        $r = json_decode($this->_httpGet($url), true);
        if (isset($r['access_token'])) {
            return $r;
        } else {
            Toolkit::trace(var_export($r, true));
            return false;
        }
    }

    public function getMPAccessToken() {
        $key = md5($this->conf['AppID']);
        $token = $this->getCache()->get($key);
        if (!$token) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .
                $this->conf['AppID'] . '&secret=' . $this->conf['AppSecret'];
            $r = $this->_httpGet($url);
            $a = json_decode($r);
            if (isset($a->access_token)) {
                $token = $a->access_token;
                $this->getCache()->set($key, $token, max($a->expires_in, 10));
            } else {
                Toolkit::trace($r);
                return false;
            }
        }
        return $token;
    }

    public function sendTemplateMessage($to, $templateID, $link, $msg) {
        $m = [
            'touser' => $to,
            'template_id' => $templateID,
            'url' => $link,
            'data' => $msg,
        ];
        $r = $this->_jsonPost(
            'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='
            . $this->getMPAccessToken(),
            $m
        );
        Toolkit::trace($r);
        return $r;
    }

    public function getUserInfo($userAccessToken, $openId) {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' .
            $userAccessToken . '&openid=' . $openId . '&lang=zh_CN';
        $r = json_decode($this->_httpGet($url), true);
        if (isset($r['headimgurl'])) {
            return $r;
        } else {
            Toolkit::trace(var_export($r, true));
            return false;
        }
    }

    protected function _httpGet($url, $options = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($options))
            curl_setopt_array($ch, $options);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    protected function _httpPost($url, $data, $options = []) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
        ]);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    protected function _jsonPost($url, $data, $options = []) {
        if (!isset($options[CURLOPT_HTTPHEADER]))
            $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        else
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        return $this->_httpPost($url, json_encode($data, JSON_UNESCAPED_UNICODE), $options);
    }

    /**
     * @return ICache
     */
    protected function getCache() {
        return ComponentFactory::getComponent($this->conf['CacheID']);
    }

    /**
     * @param string $openid
     * @param string $groupName
     * @return bool|mixed
     */
    public function moveToGroup($openid, $groupName) {
        $groups = $this->getGroups();
        if ($groups === false)
            return false;
        if (!isset($groups[$groupName])) {
            $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=' . $this->getMPAccessToken();
            $r = $this->_jsonPost($url, ['name' => $groupName]);
            $json = json_decode($r);
            if (isset($json->group)) {
                $groups[$json->group->name] = $json->group;
            } else {
                Toolkit::trace($r);
                return false;
            }
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $this->getMPAccessToken();
        $r = $this->_jsonPost($url, [
            'openid' => $openid,
            'to_groupid' => $groups[$groupName]->id,
        ]);
        return json_decode($r);
    }

    public function getGroups() {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . $this->getMPAccessToken();

        $r = $this->_httpGet($url);
        $r = json_decode($r);
        if (isset($r->groups)) {
            $groups = [];
            foreach ($r->groups as $group) {
                $groups[$group->name] = $group;
            }
            return $groups;
        }

        return false;
    }

    public function updateRemark($openid, $remark) {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=' . $this->getMPAccessToken();
        $r = $this->_jsonPost($url, [
            'openid' => $openid,
            'remark' => $remark,
        ]);
        return json_decode($r);
    }
}
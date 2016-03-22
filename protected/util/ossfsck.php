<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 2015/11/20
 * Time: 下午3:38
 */

require_once(dirname(dirname(__DIR__)) . '/webroot/xts.php');

$images = X::model('Image')->many('status_id IS NOT NULL OR ctime >= ?', [time() - 604800]);
$used_image_hash = [];
foreach ($images as $image) {
    $used_image_hash[$image->hash] = 1;
}
unset($images);



$marker = '';
$to_be_deleted = [];
do {
    $o = X::oss()->list_object('fw-img-bj2', [
        'delimiter' => '/',
        'prefix' => 'upload/',
        'max-keys' => 100,
        'marker' => $marker
    ]);

    $a = XML2Array::createArray($o->body);

    foreach ($a['ListBucketResult']['Contents'] as $c) {
        $hash = substr($c['Key'], 7);
        if (empty($hash)) continue;
        if (!isset($used_image_hash[$hash])) {
            $to_be_deleted[] = $c['Key'];
        }
    }
} while (
    $a['ListBucketResult']['IsTruncated'] === 'true' &&
    ($marker = $a['ListBucketResult']['NextMarker'])
);

var_dump($to_be_deleted);

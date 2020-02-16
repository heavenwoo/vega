<?php
error_reporting(1);
// 配置
$secret = 'heaven.vega';
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$signature = 'sha1=e0ec9317f440f3fd47631852ef585c6b2680e8f8';
if (substr_count($userAgent, 'GitHub') >= 1) {
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
} elseif (substr_count($userAgent, 'Coding') >= 1) {
    $signature = $_SERVER['HTTP_X_CODING_SIGNATURE'];
}

list($hash_type, $hash_value) = explode('=', $signature, 2);
$jsonContent = file_get_contents("php://input");
$checkHash = hash_hmac($hash_type, $jsonContent, $secret); // e0ec9317f440f3fd47631852ef585c6b2680e8f8

$fs = fopen('./auto_hook.log', 'a');
$data = json_decode($jsonContent, true);
fwrite($fs, 'Request on [' . date("Y-m-d H:i:s") . '] from [' . $data['pusher']['name'] . ']' . PHP_EOL);
fwrite($fs, 'Data: '.json_encode($data).PHP_EOL);
fwrite($fs, 'Service '.json_encode($_SERVER).PHP_EOL);

// sha1 验证
if ($checkHash && $checkHash === $hash_value) {
    fwrite($fs, '认证成功，开始更新 ' . PHP_EOL);
    $repository = $data['repository']['name'];

    $pwd = getcwd();
    $command = 'cd .. && cd ' . $repository . ' && git pull';
    fwrite($fs, 'command '.$command.PHP_EOL);

    if (!empty($repository)) {
        shell_exec($command);
        fwrite($fs, $repository . ' 更新完成 ' . PHP_EOL);
    }
    $fs and fclose($fs);
}
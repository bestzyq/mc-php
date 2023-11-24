<?php
header('Content-Type: application/json');

$serverIP = "mcs.ecustcic.eu.org";  // 你的Minecraft服务器IP
$serverPort = 53368;  // 你的Minecraft服务器端口

// 创建Socket连接
$socket = @fsockopen($serverIP, $serverPort, $errno, $errstr, 1);

if (!$socket) {
    $serverInfoData = array('error' => '无法连接到服务器');
} else {
    // 设置超时时间为1秒
    stream_set_timeout($socket, 1);
    // 发送查询数据包
    $queryPacket = pack("c*", 0xFE, 0x01);
    fwrite($socket, $queryPacket);

    // 读取服务器响应
    $response = fread($socket, 2048);

    // 关闭Socket连接
    fclose($socket);

    // 尝试使用UTF-8解码
    $data = mb_convert_encoding(substr($response, 3), 'UTF-8', 'UCS-2BE');
    
    // 去除颜色代码
    $cleanedData = preg_replace("/§[0-9a-fklmnor]/", "", $data);

    // 解析接口返回的信息
    $version = substr($cleanedData, 4, 7);
    $hint = substr($cleanedData, 11, -4);
    $onlinePlayers = substr($cleanedData, -4, -3);

    // 将数据转换为关联数组
    $serverInfoData = array(
        'version' => $version,
        'hint' => $hint,
        'onlinePlayers' => $onlinePlayers
    );
}

// 转换为 JSON 格式并输出
echo json_encode($serverInfoData);
?>

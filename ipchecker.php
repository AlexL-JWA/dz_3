<?php

define("MY_IP_API_URL", "https://api.ipify.org");
define("MY_IP_DATA_API_URI", "http://ip-api.com/json");


function doResponse(string $url): string
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        throw new \Exception(curl_error($ch));
    }

    curl_close($ch);

    return $result;
}

function getMyIp(): string
{
    return doResponse(MY_IP_API_URL);
}

function getDataByIp(string $ip): array
{
    // Add $ip validation

    if (!preg_match('/^(?:(?:25[0-5]|2[0-4]\d|1?\d?\d)(?:\.(?!$)|$)){4}$/', $ip)) {
        throw new \Exception('IP no valid ');
    }
    $ip = strip_tags(htmlspecialchars($ip));

    $url = sprintf("%s/%s", MY_IP_DATA_API_URI, $ip);

    $response = doResponse($url);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(json_last_error_msg());
    }

    if ("fail" === $data['status']) {
        throw new \Exception($data['message']);
    }

    return $data;
}

try {
    foreach (getDataByIp(getMyIp()) as $key => $val) {
        printf("%s : %s%s", $key, $val, PHP_EOL);
    }
} catch (\Exception $ex) {
    printf("%s%s", $ex->getMessage(), PHP_EOL);
}

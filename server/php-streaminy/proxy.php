<?php


function CheckMultiProxy($proxies, $timeout, $proxy_type)
{
	$data = [];

	foreach ($proxies as $proxy) {
		$parts = explode(':', trim($proxy));
		$url = strtok(curPageURL(), '?');
		$data[] = $url . '?ip=' . $parts[0] . '&port=' . $parts[1] . '&timeout=' . $timeout . '&proxy_type=' . $proxy_type;
	}

	$results = multiRequest($data);
	$holder = [];

	foreach ($results as $result) {
		$holder[] = json_decode($result, true)['result'];
	}

	$arr = ['results' => $holder];
	echo json_encode($arr);
}

function CheckSingleProxy($ip, $port, $timeout, $echoResults = true, $socksOnly = false, $proxy_type = 'http(s)')
{
	$passByIPPort = $ip . ':' . $port;
	$url = 'http://whatismyipaddress.com/';
	$loadingtime = microtime(true);
	$theHeader = curl_init($url);
	curl_setopt($theHeader, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($theHeader, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($theHeader, CURLOPT_PROXY, $passByIPPort);

	if ($socksOnly) {
		curl_setopt($theHeader, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	}

	curl_setopt($theHeader, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($theHeader, CURLOPT_SSL_VERIFYPEER, 0);
	$curlResponse = curl_exec($theHeader);

	if ($curlResponse === false) {
		if ((curl_errno($theHeader) == 56) && !$socksOnly) {
			CheckSingleProxy($ip, $port, $timeout, $echoResults, true, 'socks');
			return NULL;
		}

		$arr = [
			'result' => [
				'success' => false,
				'error'   => curl_error($theHeader),
				'proxy'   => ['ip' => $ip, 'port' => $port, 'type' => $proxy_type]
			]
		];
	}
	else {
		$arr = [
			'result' => [
				'success' => true,
				'proxy'   => ['ip' => $ip, 'port' => $port, 'speed' => floor((microtime(true) - $loadingtime) * 1000), 'type' => $proxy_type]
			]
		];
	}

	$isproxy = false;

	if ($echoResults) {
		foreach ($arr['result'] as $key => $value) {
			if ($key == 'error') {
				if (strpos($arr['result']['error'], ':') !== false) {
					$error_code = trim(explode(':', $arr['result']['error'])[1]);

					if ($error_code == 'Connection refused') {
						$isproxy = true;
					}
					else if ($error_code == 'No route to host') {
						$isproxy = false;
					}
				}
				else {
					$isproxy = true;
				}
			}

			if ($key == 'success') {
				$success_code = $arr['result']['success'];

				if ($success_code == 1) {
					$isproxy = true;
				}
				else {
					$isproxy = false;
				}
			}
		}
	}

	return $isproxy;
}

function multiRequest($data, $options = [])
{
	$curly = [];
	$result = [];
	$mh = curl_multi_init();

	foreach ($data as $id => $d) {
		$curly[$id] = curl_init();
		$url = (is_array($d) && !empty($d['url']) ? $d['url'] : $d);
		curl_setopt($curly[$id], CURLOPT_URL, $url);
		curl_setopt($curly[$id], CURLOPT_HEADER, 0);
		curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

		if (is_array($d)) {
			if (!empty($d['post'])) {
				curl_setopt($curly[$id], CURLOPT_POST, 1);
				curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
			}
		}

		if (!empty($options)) {
			curl_setopt_array($curly[$id], $options);
		}

		curl_multi_add_handle($mh, $curly[$id]);
	}

	$running = NULL;

	do {
		curl_multi_exec($mh, $running);
	} while (0 < $running);

	foreach ($curly as $id => $c) {
		$result[$id] = curl_multi_getcontent($c);
		curl_multi_remove_handle($mh, $c);
	}

	curl_multi_close($mh);
	return $result;
}

function curPageURL()
{
	$pageURL = 'http';
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
		$pageURL .= 's';
	}

	$pageURL .= '://';

	if ($_SERVER['SERVER_PORT'] != '80') {
		$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	}
	else {
		$pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}

	return $pageURL;
}

set_time_limit(100);

if (!isset($_GET['timeout'])) {
	exit('You must specify a timeout in seconds in your request (checker.php?...&timeout=20)');
}

if (isset($_GET['proxy_type'])) {
	if ($_GET['proxy_type'] == 'socks') {
		$socksOnly = true;
		$proxy_type = 'socks';
	}
	else {
		$proxy_type = 'http(s)';
	}
}
else {
	$proxy_type = 'http(s)';
}

if (isset($_GET['ip'])) {
	CheckSingleProxy($_GET['ip'], $_GET['port'], $_GET['timeout'], true, $socksOnly, $proxy_type);
}

?>
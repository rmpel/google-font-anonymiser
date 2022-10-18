<?php
// example: https://fonts.googleapis.com/css?family=Montserrat:400,400i,500,700

$url = $_SERVER['REQUEST_URI'] ?? false;
if (!$url) {
	die();
}
$url = ltrim($url, '/');

// Local hostname.
$local = 'http' . ( $_SERVER['HTTPS'] ?? false === 'on' || $_SERVER['X_HTTP_FORWARDED_PROTO'] ?? false === 'https' ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

// Remote hostname.
$remote = '';
if (substr($url, 0, 3) === 'css') {
	$remote = "https://fonts.googleapis.com/$url";
}
else {
	$remote = "https://fonts.gstatic.com/$url";
}

// Key the cache with both the local domain as well as the remote; if your local domain changes, the cache is invalid.
$cache = md5( $local . $remote ) . '.php';

// Cache directory.
if (!is_dir(__DIR__ .'/cache')) {
	mkdir(__DIR__ .'/cache');
}

// Build the cache.
if (!is_file(__DIR__ .'/cache/' . $cache)) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remote);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, 'Don\'t track my users');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:105.0) Gecko/20100101 Firefox/105.0');
	$headers = [];
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers) {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) { // ignore invalid headers
			return $len;
		}

		$headers[strtolower(trim($header[0]))][] = trim($header[1]);

		return $len;
	});
	
	$body = curl_exec($ch);
	
	curl_close($ch);
	
	$cache_content = '<?php // '. $remote .'
';
	foreach ($headers as $header_key => $header_values) {
		if (!in_array($header_key, [ 'expires', 'transfer-encoding' ])) {
			$header_key = str_replace("'", "\\'", $header_key);
			foreach ($header_values as $header_value) {
				$header_value = str_replace("'", "\\'", $header_value);
				$cache_content .= "header('$header_key: $header_value'); \n";
			}
		}
	}

	// change the content; css files should refer to the local domain, binary output should not include <? as it would make PHP think it should parse.
	$body = strtr($body, [ 'https://fonts.gstatic.com/' => $local, '<?' => '<<?php ?>?' ]);
	$cache_content .= '?>' . $body;

	file_put_contents(__DIR__ .'/cache/' . $cache, $cache_content);
}
// Serve the cache.
require __DIR__ .'/cache/' . $cache;

// Todo: cache maintenance.

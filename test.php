<?php 
	$local = 'http' . ( ($_SERVER['HTTPS'] ?? false) === 'on' || ($_SERVER['X_HTTP_FORWARDED_PROTO'] ?? false) === 'https' ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST']; 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link href="<?php print $local; ?>/css2?family=Fuzzy+Bubbles&family=IBM+Plex+Mono&family=Nanum+Gothic:wght@400;700&display=swap" rel="stylesheet">
	<style>
		h2 { font-family: 'Fuzzy Bubbles', cursive; font-size: 40px; }
		h4 { font-family: 'Fuzzy Bubbles', cursive; }
		body { font-family: 'Nanum Gothic', sans-serif; }
		pre { font-family: 'IBM Plex Mono', monospace; }
	</style>
</head>
<body>
	<h2>Google Font anonymizing and caching CDN</h2>
	<h4>A proof-of-concept by Remon</h4>
	<p>Normally one would</p>
	<pre>@import url('https://fonts.googleapis.com/css2?family=Fuzzy+Bubbles&family=IBM+Plex+Mono&family=Nanum+Gothic:wght@400;700&display=swap');</pre>
	<p>but now you are sending hits to Google and Google tracks everything.</p>
	<p>Change to</p>
	<pre>@import url('<?php print $local; ?>/css2?family=Fuzzy+Bubbles&family=IBM+Plex+Mono&family=Nanum+Gothic:wght@400;700&display=swap');</pre>
	<p>and it still works :) But now, the data sent to Google is</p>
	<ul>
		<li>Always from the same IP (the one where this tool is hosted)</li>
		<li>Does not include your browser-data (user-agent is one standardised one designed to make Google serve woff2 files.)</li>
		<li>Does not include your page-data (referer data is ignored)</li>
	</ul>
	<pre>Source: <a href="https://github.com/rmpel/google-font-anonymiser">https://github.com/rmpel/google-font-anonymiser</a></pre>
</body>
</html>
<?php 
error_reporting(0);
$banner = 
"+------------------------------------------------+

   [+] MASS WINDSCRIBE ACCOUNT AUTO CREATED [+]

+------------------------------------------------+";
echo $banner;
class curl {
	var $ch, $agent, $error, $info, $cookiefile, $savecookie;	
	function curl() {
		$this->ch = curl_init();
		curl_setopt ($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/530.1 (KHTML, like Gecko) Chrome/2.0.164.0 Safari/530.1');
		curl_setopt ($this->ch, CURLOPT_HEADER, 1);
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($this->ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt ($this->ch, CURLOPT_TIMEOUT, 30);
		curl_setopt ($this->ch, CURLOPT_CONNECTTIMEOUT,30);
	}
	function header($header) {
		curl_setopt ($this->ch, CURLOPT_HTTPHEADER, $header);
	}
	function timeout($time){
		curl_setopt ($this->ch, CURLOPT_TIMEOUT, $time);
		curl_setopt ($this->ch, CURLOPT_CONNECTTIMEOUT,$time);
	}
	function http_code() {
		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
	}
	function error() {
		return curl_error($this->ch);
	}
	function ssl($veryfyPeer, $verifyHost){
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $veryfyPeer);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verifyHost);
	}
	function cookies($cookie_file_path) {
		$this->cookiefile = $cookie_file_path;;
		$fp = fopen($this->cookiefile,'wb');fclose($fp);
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $this->cookiefile);
	}
	function proxy($sock) {
		curl_setopt ($this->ch, CURLOPT_HTTPPROXYTUNNEL, true); 
		curl_setopt ($this->ch, CURLOPT_PROXY, $sock);
	}
	function post($url, $data) {
		curl_setopt($this->ch, CURLOPT_POST, 1);	
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
		return $this->getPage($url);
	}
	function data($url, $data, $hasHeader=true, $hasBody=true) {
		curl_setopt ($this->ch, CURLOPT_POST, 1);
		curl_setopt ($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
		return $this->getPage($url, $hasHeader, $hasBody);
	}
	function get($url, $hasHeader=true, $hasBody=true) {
		curl_setopt ($this->ch, CURLOPT_POST, 0);
		return $this->getPage($url, $hasHeader, $hasBody);
	}	
	function getPage($url, $hasHeader=true, $hasBody=true) {
		curl_setopt($this->ch, CURLOPT_HEADER, $hasHeader ? 1 : 0);
		curl_setopt($this->ch, CURLOPT_NOBODY, $hasBody ? 0 : 1);
		curl_setopt ($this->ch, CURLOPT_URL, $url);
		$data = curl_exec ($this->ch);
		$this->error = curl_error ($this->ch);
		$this->info = curl_getinfo ($this->ch);
		return $data;
	}
}

function fetchCurlCookies($source) {
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $source, $matches);
	$cookies = array();
	foreach($matches[1] as $item) {
		parse_str($item, $cookie);
		$cookies = array_merge($cookies, $cookie);
	}
	return $cookies;
}

function string($length = 15)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function angka($length = 15)
{
	$characters = '0123456789';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
function fetch_value($str,$find_start,$find_end) {
	$start = @strpos($str,$find_start);
	if ($start === false) {
		return "";
	}
	$length = strlen($find_start);
	$end    = strpos(substr($str,$start +$length),$find_end);
	return trim(substr($str,$start +$length,$end));
}

function loop($socks,$timeout) {
	$curl = new curl();
	$curl->cookies('cookies/'.md5($_SERVER['REMOTE_ADDR']).'.txt');
	$curl->ssl(0, 2);
	$curl->timeout($timeout);
    $curl->proxy($socks);

	$page_api = file_get_contents('https://www.fakenamegenerator.com');
	$address = fetch_value($page_api, '<div class="address">','</div>');
	$name = fetch_value($address, '<h3>','</h3>');
	preg_match_all('/<dl class="dl-horizontal">(.*?)<\/dl>/s', $page_api, $user);
	$mail = fetch_value($user[1][8], '<dd>','<div class="adtl">');
	$mail_p = explode('@', $mail);
	$domain = array ('@gmail.com','@yahoo.com','@mail.com','@yandex.com','@gmx.de','@t-online.de','@yahoo.co.id','@yahoo.co.uk');
	$random = rand(0,7);
	$email  = $mail_p[0].angka(4).$domain[$random];
	$uname = fetch_value($user[1][9], '<dd>','</dd>');
	$username = $uname.angka(4);
	$password = string(8);

	$page = $curl->post('https://windscribe.com/signup', 'signup=1&username='.$username.'&password='.$password.'&password2='.$password.'&email='.$email.'&voucher_code=&captcha=&unlimited_plan=1');

	if (stripos($page, 'session_auth_hash')) {
		echo "SUCCESS | Email: ".$email." | Username: ".$username." | Password: ".$password."\n";
		$data =  "SUCCESS | Email: ".$email." | Username: ".$username." | Password: ".$password."\r\n";
		$fh = fopen("success.txt", "a");
		fwrite($fh, $data);
		fclose($fh);
	} else {
		echo "SOCKS DIE | ".$socks."\n";
		flush();
		ob_flush();
	}
}

echo "\nMasukan List File Proxy (Ex: proxy.txt): ";
$namefile = trim(fgets(STDIN));
if ($namefile == "") {
    die ("Proxy Cannot Be Blank!\n");
}
echo "Timeout : ";
$timeout = trim(fgets(STDIN));
if ($timeout == "") {
    die ("Cannot be blank!\n");
}
echo "Please wait ...";
sleep(1);
echo ".";
sleep(1);
echo ".";
sleep(1);
echo ".\n";
$file = file_get_contents($namefile) or die ("File not found!\n");
$socks = explode("\r\n",$file);
$total = count($socks);
echo "Total proxy: ".$total."\n";
 
$i = 0;
foreach ($socks as $value) {
    loop($value, $timeout);
    $i++;
}
?>

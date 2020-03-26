<?php 
error_reporting(0);
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
function instagram_account_creator($socks, $timeout) {
	$curl = new curl();
	$curl->cookies('cookies/'.md5($_SERVER['REMOTE_ADDR']).'.txt');
	$curl->ssl(0, 2);
	$curl->timeout($timeout);
	$curl->proxy($socks);

	$random_user = $curl->post('https://www.fakepersongenerator.com/Index/generate', 'gender=0&age=0&state=0&city=');
	preg_match_all('/<div class="info-detail">(.*?)<\/div>/s', $random_user, $info);
	$mail = fetch_value($info[1][0], "<input type=\"text\" value='","' class='form-control' />");
	$email = string(4).$mail;
	$name = fetch_value($random_user, "<p class='text-center name'><b class='click'>","</b></p>");
	$uname = fetch_value($info[1][50], "<p>","<br>");
	$username = $uname.string(4);
	$password = fetch_value($info[1][51], "<p>","<br>");
	$user_agent = fetch_value($info[1][74], "<p>","</p>");


	$page_signup = $curl->get('https://www.instagram.com/accounts/signup/email');
	$cookies = fetchCurlCookies($page_signup);
	$csrftoken = $cookies['csrftoken'];
	$mid = $cookies['mid'];

	if ($page_signup) {

		$headers = array();
		$headers[] = "Origin: https://www.instagram.com";
		$headers[] = "Accept-Language: en-US,en;q=0.9";
		$headers[] = "X-Requested-With: XMLHttpRequest";
		$headers[] = "User-Agent: ".$user_agent."";
		$headers[] = "Cookie: mid=".$mid."; mcd=3; csrftoken=".$csrftoken."; rur=FRC;";
		$headers[] = "X-Csrftoken: ".$csrftoken."";
		$headers[] = "X-Instagram-Ajax: 3c390ba4b80b";
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		$headers[] = "Accept: */*";
		$headers[] = "Referer: https://www.instagram.com/accounts/signup/email";
		$headers[] = "Authority: www.instagram.com";
		$curl->header($headers);

		$check_mail = $curl->post('https://www.instagram.com/accounts/check_email/', 'email='.$email.'');


		if (strpos($check_mail, '"available": true')) {

			$check_username = $curl->post('https://www.instagram.com/accounts/username_suggestions/', 'email='.$email.'&name='.$name.'');

			if (strpos($check_username, '"status": "ok"')) {

				$create = $curl->post('https://www.instagram.com/accounts/web_create_ajax/', 'email='.$email.'&password='.$password.'&username='.$username.'&first_name='.$name.'&client_id='.$mid.'&seamless_login_enabled=1&tos_version=row');

				if (stripos($create, '"account_created": true')) {
					echo "SUCCESS CREATE | ".$socks." | ".$email." | ".$username." | ".$password."\n";
					$data =  "SUCCESS CREATE | ".$socks." | ".$email." | ".$username." | ".$password."\r\n";
					$date = date('d/y/m');
					$fh = fopen("success_".$date.".txt", "a");
					fwrite($fh, $data);
					fclose($fh);
				} elseif(strpos($create, 'The IP address you are using has been flagged as an open proxy')) {
					echo "IP BLOCKED | ".$socks." | ".$email." | ".$username." | ".$password."\n";
					flush();
					ob_flush();
				} else {
					echo "FAILED | ".$socks." | ".$email." | ".$username." | ".$password."\n";
					flush();
					ob_flush();
				}
			} else {
				echo "FAILED | ".$socks." | ".$email." | ".$username." | ".$password."\n";
				flush();
				ob_flush();
			}

		} else {
			echo "EMAIL ALREADY REGISTER | ".$email."\n";
			flush();
			ob_flush();
		}


	} else {
		$data['httpcode'] = $curl->http_code();
		$error = $curl->error();
		echo "".$socks." | ".$error." | Http code : ".$data['httpcode']."\n";
		flush();
		ob_flush();
	}

}

echo "
=======================================
      #INSTAGRAM ACCOUNT CREATOR#
	     (USE PROXY)
=======================================
    CREATED BY YUDHA TIRA 
    Recode By CJDW
=======================================
\n";

echo "LIST TOOLS\n";
echo "[1] INSTAGRAM ACCOUNT CREATOR\n";
echo "Select tools: ";
$list = trim(fgets(STDIN));
if ($list == "") {
	die ("Cannot be blank!\n");
}

if ($list == 1) {
	echo "INSTAGRAM ACCOUNT CREATOR\n";
	sleep(1);
	echo "Name file proxy (Ex: proxy.txt): ";
	$namefile = trim(fgets(STDIN));
	if ($namefile == "") {
		die ("Proxy cannot be blank!\n");
	}
	echo "Timeout : ";
	$timeout = trim(fgets(STDIN));
	if ($timeout == "") {
		die ("Cannot be blank!\n");
	}
	echo "Please wait";
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

	foreach ($socks as $value) {
		instagram_account_creator($value, $timeout);
	}

} else {
	die ("Command not found!\n");
}

?>
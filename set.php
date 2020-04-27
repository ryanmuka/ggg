<?php
error_reporting(0);
$SET = array();
$SET['token'] = "";
$SET['limit'] = "10";
$SET['react'] = 1; /* 0 LIKE | 1 LOVE | 2 HAHA | 3 WOW | 4 SAD | 5 ANGRY */
$SET['delay'] = 1;
$SET['ulang'] = true;
$SET['bot_komen'] = false; // true bot aktif dan false bot mati
$komennya = array("Jangan Lupa Bahagia","Jangan Lupa Untuk Move On","Haloo","Jangan Lupa Makan","Jangan Pernah Bersedih");
if($SET['ulang'] == true){
	while (true) {
		gass();
		sleep($SET['delay']);
	}
} else {
	gass();
}
function gass(){
	global $SET,$komennya;
	$status = getStatus();
	$reaksi = array("LIKE","LOVE","HAHA","WOW","SAD","ANGRY");
	$react_type = $reaksi[$SET['react']];
	echo "Made by Muhammad Zakir Ramadhan\n";
	echo "Recoded by Niqo Qintharo\n";
	echo "Total Thread ( ".count($status['data'])." )\n";
	sleep($SET['delay']);
	foreach ($status['data'] as $key => $data) {
		$id_post = $data['id'];
		$from = $data['from']['name'];
		$type = $data['type'];
		$token = $SET['token'];
		$ids = explode("_", $id_post);
		$ids = $ids[1];
		echo " [{$ids}] React love pada status {$from} ( {$type} ) ";
		sleep($SET['delay']);
		$url = "https://graph.facebook.com/v2.11/{$id_post}/reactions?";
		$post = "type={$react_type}&access_token={$token}&method=post";
		$respon = _curl($url,$post);
		$result = json_decode($respon,true);
		if($result['success']){
			echo " Berhasil ";
			if($SET['bot_komen'] == true){
				$kata = $komennya[array_rand($komennya)];
				$kata = $kata." ".$from. " :D\n-JustBot";
				$kata = urlencode($kata);
				$kirim_kata = file_get_contents("https://api.facebook.com/method/stream.addComment?post_id={$id_post}&comment={$kata}&access_token={$token}");
				if(preg_match("/stream_addComment_response/", $kirim_kata)){
					echo " ( Sukses Komen ) :p\n";
				} else {
					echo " ( Gagal Komen ) :(\n";
				}
			} else {
				echo "\n";
			}
			
		} else {
			echo " Error\n";
		}
		sleep(5);
	}
}
function getStatus()
{
	global $SET;
	return json_decode(file_get_contents("https://graph.facebook.com/v2.1/me/home?fields=id,from,type,message&limit=".$SET['limit']."&access_token=".$SET['token']),true);
}
function _curl($url,$data)
{
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,20);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31');
    curl_setopt($curl, CURLOPT_COOKIE,'cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEFILE,'cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR,'cookie.txt');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
    $result = curl_exec($curl);
    if(!$result){
    	echo "Retry 10s....";sleep(10);
    }
    return $result;
    curl_close($curl);
}
function banner(){
	
}

<?php

// Auto backup chat Instagram
// Credit: Alfian Ananda Putra

date_default_timezone_set("Asia/Jakarta");
error_reporting(0);

function getCSRF(){
  $fgc    =   file_get_contents("https://www.instagram.com");
  $explode    =   explode('"csrf_token":"',$fgc);
  $explode    =   explode('"',$explode[1]);
  return $explode[0];
}

// Membuat device id Android
function generateDeviceId(){
  $megaRandomHash = md5(number_format(microtime(true), 7, '', ''));
  return 'android-'.substr($megaRandomHash, 16);
}

// Membuat UUID
function generateUUID($keepDashes = true){
  $uuid = sprintf(
    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff)
  );
  return $keepDashes ? $uuid : str_replace('-', '', $uuid);
}

// Membuat signed_body untuk UA : Instagram 24.0.0.12.201 Android
function hookGenerate($hook){
  return 'ig_sig_key_version=4&signed_body=' . hash_hmac('sha256', $hook, '5bd86df31dc496a3a9fddb751515cc7602bdad357d085ac3c5531e18384068b4') . '.' . urlencode($hook);
}

// Fungsi request untuk mengirim data
function request($url,$hookdata,$cookie,$method='GET'){
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, "https://i.instagram.com/api".$url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  if($method == 'POST'){
    curl_setopt($ch, CURLOPT_POSTFIELDS, $hookdata);
    curl_setopt($ch, CURLOPT_POST, 1);
  }else{
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  }

  $headers = array();
  $headers[] = "Accept: */*";
  $headers[] = "Content-Type: application/x-www-form-urlencoded";
  $headers[] = 'Cookie2: _ENV["Version=1"]';
  $headers[] = "Accept-Language: en-US";
  $headers[] = "User-Agent: Instagram 24.0.0.12.201 Android (28/9; 320dpi; 720x1280; samsung; SM-J530Y; j5y17ltedx; samsungexynos7870; in_ID;)";
  $headers[] = "Host: i.instagram.com";
  if($cookie !== "0"){
    $headers[] = "Cookie: ".$cookie;
  }
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);
  $httpcode  = curl_getinfo($ch);
  $header    = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
  $body      = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

  if(curl_errno($ch)){
    echo 'Error:' . curl_error($ch);
  }
  curl_close ($ch);
  return array($header, $body, $httpcode,$result,$url,$hookdata); // body itu response body
}

echo "                                    ____        _
                                   |  _ \      | |
  _ __ ___ _ __ ___   _____   _____| |_) | ___ | |_
 | '__/ _ \ '_ ` _ \ / _ \ \ / / _ \  _ < / _ \| __|
 | | |  __/ | | | | | (_) \ V /  __/ |_) | (_) | |_
 |_|  \___|_| |_| |_|\___/ \_/ \___|____/ \___/ \__|


© Pianjammalam 2020

-----------------------------------------------------------
";

echo 'Selamat datang di removeBot V1 by @pianjammalam.
Bot ini akan menghapus followers kalian yang memenuhi persyaratan. Harap pastikan followers Anda diatas 500 orang!

Siapa aja yang akan di remove? Yang memenuhi KETIGA persyaratan berikut:
- Followers yang Akunnya Private
- Followers yang Followersnya lebih dikit dari Following
- Followers yang Tidak Anda Follow

Username: @';

$usernameIg = trim(fgets(STDIN));

echo 'Password: ';

$passwordIg = trim(fgets(STDIN));

$isTrueOrFalse = true;

$maxId =  '';

if(!empty($usernameIg) and !empty($passwordIg)){
  $genDevId = generateDeviceId();
  $tryLogin = request('/v1/accounts/login/',hookGenerate('{"phone_id":"'.generateUUID().'","_csrftoken":"'.getCSRF().'","username":"'.$usernameIg.'","adid":"'.generateUUID().'","guid":"'.generateUUID().'","device_id":"'.$genDevId.'","password":"'.$passwordIg.'","login_attempt_count":"0"}'),0,"POST");
  if(!empty(json_decode($tryLogin[1],true)['logged_in_user']['pk'])){
    $uid = json_decode($tryLogin[1],true)['logged_in_user']['pk'];
    preg_match_all('%set-cookie: (.*?);%',$tryLogin[0],$d);$cookieInstagram = '';
    for($o=0;$o<count($d[0]);$o++){$cookieInstagram.=$d[1][$o].";";}
    // Menyimpan cookie ke file cookie.txt
    $fwriteFunc = fopen('cookie.txt', 'w');
    fwrite($fwriteFunc, $cookieInstagram);
    fclose($fwriteFunc);
    $cekFirstBefore = request('/v1/users/'.$uid.'/info/', 0, $cookieInstagram, 'GET')[1];

    while($isTrueOrFalse){
      $resultGetFollowers = request('/v1/friendships/'.$uid.'/followers/?search_surface=follow_list_page&order=default&query=&enable_groups=true&rank_token='.generateUUID().'&max_id='.$maxId,0,$cookieInstagram,"GET")[1];
      if(strlen(json_decode($resultGetFollowers, true)['next_max_id']) > 5){
        //print_r($resultGetFollowers);
        foreach (json_decode($resultGetFollowers, true)['users'] as $key => $value) {
          $pkFollowersTarget = json_decode($resultGetFollowers, true)['users'][$key]['pk'];
          $requestCekPrivasiAkun  = request('/v1/users/'.$pkFollowersTarget.'/info/', 0, $cookieInstagram, 'GET')[1];
          if(json_decode($requestCekPrivasiAkun, true)['user']['is_private'] == true){
            echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Private '.PHP_EOL;
            if(json_decode($requestCekPrivasiAkun, true)['user']['follower_count'] <= json_decode($requestCekPrivasiAkun, true)['user']['following_count']){
              $requestCekRelationship  = request('/v1/friendships/show/'.$pkFollowersTarget.'/', 0, $cookieInstagram, 'GET')[1];
              if(json_decode($requestCekRelationship,true)['following']){
                //echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Lu follow ternyata '.PHP_EOL;
              }else{
                echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Bisa kita eksekusi bos '.PHP_EOL;
                request('/v1/friendships/remove_follower/'.$pkFollowersTarget.'/',hookGenerate('{"_csrftoken":"'.getCSRF().'","user_id":"'.$pkFollowersTarget.'","radio_type":"wifi-none","_uid":"'.$uid.'","_uuid":"'.generateUUID().'"}'),$cookieInstagram,"POST");
                //print_r($removeFollowersAsu);
                $fileSave = fopen("listYangKitaRemoveSob-".$usernameIg.".txt", "a");
                fwrite($fileSave, '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].'
');
                fclose($fileSave);
                sleep(3);
              }
            }else{
              //echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Followersnya banyak sob '.PHP_EOL;
            }
          }else{
            //echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Tidak Private '.PHP_EOL;
          }
          sleep(2);
        }
        $GLOBALS['maxId'] = json_decode($resultGetFollowers, true)['next_max_id'];
      }else if(!empty(json_decode($resultGetFollowers, true)['users'][0]['pk'])){
        foreach (json_decode($resultGetFollowers, true)['users'] as $key => $value) {
          $pkFollowersTarget = json_decode($resultGetFollowers, true)['users'][$key]['pk'];
          $requestCekPrivasiAkun  = request('/v1/users/'.$pkFollowersTarget.'/info/', 0, $cookieInstagram, 'GET')[1];
          if(json_decode($requestCekPrivasiAkun, true)['user']['is_private'] == true){
            echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Private '.PHP_EOL;
            if(json_decode($requestCekPrivasiAkun, true)['user']['follower_count'] <= json_decode($requestCekPrivasiAkun, true)['user']['following_count']){
              $requestCekRelationship  = request('/v1/friendships/show/'.$pkFollowersTarget.'/', 0, $cookieInstagram, 'GET')[1];
              if(json_decode($requestCekRelationship,true)['following']){
                //echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Lu follow ternyata '.PHP_EOL;
              }else{
                echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Bisa kita eksekusi bos '.PHP_EOL;
                request('/v1/friendships/remove_follower/'.$pkFollowersTarget.'/',hookGenerate('{"_csrftoken":"'.getCSRF().'","user_id":"'.$pkFollowersTarget.'","radio_type":"wifi-none","_uid":"'.$uid.'","_uuid":"'.generateUUID().'"}'),$cookieInstagram,"POST");
                //print_r($removeFollowersAsu);
                $fileSave = fopen("listYangKitaRemoveSob-".$usernameIg.".txt", "a");
                fwrite($fileSave, '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].'
');
                fclose($fileSave);
                sleep(3);
              }
            }else{
              //echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Followersnya banyak sob '.PHP_EOL;
            }
          }else{
            //echo '['.date("d-M-Y").' '.date("h:i:s").'] @'.json_decode($resultGetFollowers, true)['users'][$key]['username'].' => Tidak Private '.PHP_EOL;
          }
          sleep(2);
        }
        $GLOBALS['maxId'] = '0';
      }else{
        $cekFirstAfter = request('/v1/users/'.$uid.'/info/', 0, $cookieInstagram, 'GET')[1];
        $GLOBALS['isTrueOrFalse'] = false;
        //print_r($resultGetFollowers);
        echo "Followers Sebelum: ".json_decode($cekFirstBefore, true)['follower_count']."\nFollowers Setelah: ".json_decode($cekFirstAfter, true)['follower_count']."\nBot selesai bekerja :)\n";//.print_r($resultGetFollowers);
      }
      sleep(120);
    }
  }else{
    echo "Ada yang salah dengan akunmu! \n". print_r($tryLogin);
  }
}else{
  echo "Jangan ada yang kosong!\n";
}

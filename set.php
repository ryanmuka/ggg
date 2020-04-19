<?php
/**	
 * @release 2020
 * 
 * @author eco.nxn
 */
date_default_timezone_set("Asia/Jakarta");
error_reporting(0);
class curl {
	private $ch, $result, $error;
	
	/**	
	 * HTTP request
	 * 
	 * @param string $method HTTP request method
	 * @param string $url API request URL
	 * @param array $param API request data
	 */
	public function request ($method, $url, $param, $header) {
		curl:
        $this->ch = curl_init();
        switch ($method){
            case "GET":
                curl_setopt($this->ch, CURLOPT_POST, false);
                break;
            case "POST":               
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $param);
                break;
        }
        curl_setopt($this->ch, CURLOPT_URL, 'https://api-servicemotorkuexpress.astra.co.id'.$url);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'okhttp/3.12.1');
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 120);

        $this->result = curl_exec($this->ch);
        $this->error = curl_error($this->ch);
        if(!$this->result){
            if($this->error) {
                echo "[!] cURL Error: ".$this->error."\n";
                sleep(1);
                goto curl;
            } else {
                echo "[!] cURL Error: No Result\n\n";
                die();
            }
        }
        curl_close($this->ch);
        return $this->result;
    }
    
}

class motorku {

    function random_numb($length)
    {
        $data = '0123456789';
        $string = '';
        for($i = 0; $i < $length; $i++) {
            $pos = rand(0, strlen($data)-1);
            $string .= $data{$pos};
        }
        return $string;
    }

    function random_str($length)
    {
        $data = 'euioa';
        $string = '';
        for($i = 0; $i < $length; $i++) {
            $pos = rand(0, strlen($data)-1);
            $string .= $data{$pos};
        }
        return $string;
    }

    function random_string($length)
    {
        $data = 'qwrtyplkjhgfdszxcvbnm';
        $string = '';
        for($i = 0; $i < $length; $i++) {
            $pos = rand(0, strlen($data)-1);
            $string .= $data{$pos};
        }
        return $string;
    }

    function random_token($length)
    {
        $data = 'qwertyuioplkjhgfdsazxcvbnmMNBVCXZASDFGHJKLPOIUYTREWQ';
        $string = '';
        for($i = 0; $i < $length; $i++) {
            $pos = rand(0, strlen($data)-1);
            $string .= $data{$pos};
        }
        return $string;
    }

    /**
     * Get random name
     */
    function randomuser() {
        randomuser:
        $randomuser = file_get_contents('https://uinames.com/api/?ext&amount=100&region=indonesia&gender=random&source=uinames.com');
        if($randomuser) {
            $json = json_decode($randomuser);
            return $json;
        } else {
            sleep(2);
            goto randomuser;
        }
    }

    /**
     * Registrasi akun
     */
    function regis($name, $reff) { 
        $curl = new curl();

        $method   = 'POST';
        $header   =  [
            'authorization: Bearer null',
            'mokita-enc: eyJlbmMtdmVyc2lvbiI6IjEuMS5xMiIsImNpcGhlcnRleHQiOiJnaGJSdVRJeGRndDg0UHVEZTVaM0F3PT0iLCJpdiI6IjE1ODQ5NTQyOTAxNTg0OTU0MjkwMTU4NDk1NDI5MDE1IiwidGltZXN0YW1wIjoxNTg0OTc5NDkwMjcwLCJyZXF1ZXN0VGltZXN0YW1wIjoxNTg0OTc5NDkwLCJ0aW1lc3RhbXBVdGMiOjE1ODQ5NTQyOTAyNzAsInJlcXVlc3RUaW1lc3RhbXBVdGMiOjE1ODQ5NTQyOTAsInNhbHQiOiJjMDIxN2NjZWFkYjE0MzQwNWZiNzBjZmU1ZjhjMjRkYzhmZDAyMDkyNGI5YjU0ZTQ1ODJhZjkxNjhmMTkzZDgyYWE4Y2U3OTk0NTYyYmFhZjY0NzI4NGNiNTkyNjUxNDM2MTY0MzdkOGM4OWY5OWQ3YzBiZWE4NzhjMWQzMWE0NjRmYjI0Y2JhYjkyZGZkZDk2NmVlMmYxYzU4NTY1ZWJjYmFlZTdmYjAzMGY3OTI1OGM1N2ZiZjM1NGEyNjI3NWE2ZmE3MTRjOWM1YWRlYmFjYzJlYjViNjRjZmVlODY4MTM4OTI0NzdkYTMxNjMxODM0NjA2MDVhYWEyNzI1NmUzYzc0YTFjZTE1YzQ3NDdlNDU5NTYzMDNjOWZkNTk0NzZhYzk4N2IyMzQ1ZTRkZDVhOTAyNjA5OGRhZWExNWU2MmIwZjY4Y2ZiYmI4Y2YxMzY1MDYyNmI4NWFjMDJjZDI5NGUyMjc1YTg0NWFhNzE2MzY1YjAxODJjYjkwOWMxZDEyZmU2ZDIyZmMzZjIyMGY0OTY4ZDU1NmY2MzRiOTNlMmFiMDk2ZTVhMzQyMGIwYTY1OTdhMGU1NjlkNjg4ZWY1OTJjMjIwOTYyYmJiNDM2M2M1ZTM5OWE1NmFhMTg4MWI1NDFiNjQ0NmEzZjg2YTA0Yzc5MGU0ZTg5MzNmYWU3NyIsIml0ZXJhdGlvbnMiOjk5OX0=',
            'Content-Type: application/json'
        ];
        $endpoint = '/api/register/q2';

        // $name =  strtoupper($this->random_string(1)).$this->random_str(1).$this->random_string(1).$this->random_str(1);
        $param = '{"name":"'.$name.'","phone_number":"0812'.$this->random_numb(9).'","email":"","latitude":null,"longitude":null,"referral_code":"'.strtoupper($reff).'"}';

        $regis = $curl->request ($method, $endpoint, $param, $header);

        $json = json_decode($regis);

        if($json->status == 1) {
            $token = $json->token;
        } else {
            $token = NULL;
        }
        
        return $this->get_points($token);
    }

    /**
     * Get Points
     */
    function get_points($token) {
        $curl = new curl();

        $method   = 'POST';
        $header   =  [
            'authorization: Bearer '.$token,
            'Content-Type: application/json'
        ];
        $endpoint = '/api/profile/firebaseToken';

        $param = '{"token":"'.$this->random_token(11).':APA91bGoqkIHu5r36F9gAJblzHCV1XIwxh5MhvQXTD9y977rteh_q91GIX44zgHrx7um3SXXijcRU5aaeSm_Hqxe96sjDTCjBQpq6DdyJ_PDt9e7Le7p9r17DdNZfc119BltLGnvdgb"}';

        $get_points = $curl->request ($method, $endpoint, $param, $header);
   
        $json = json_decode($get_points);

        if($json->status == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
}

/**
 * Running
 */

$motorku = new motorku();

echo "by @eco.nxn\n\n";
echo "Masukkan Kode Referer :";
$reff = trim(fgets(STDIN));
echo "\n\n";

$no=1;
while(TRUE) {

    $randomuser = $motorku->randomuser();
    foreach ($randomuser as $value) {
        $firsname  = $value->name;
        $surname  = $value->surname;

        for ($i=0; $i < 2; $i++) { 
            if($i==0) {
                $name = $firsname;
            } else {
                $name = $surname;
            }

            $run = $motorku->regis($name, $reff);
            if($run==true) {
                echo "[".$no++."] ".date('H:i:s')." | Registrasi Berhasil\n";
                
            } else {
                echo "[!] ".date('H:i:s')." | Registrasi GAGAL\n";
            }
            sleep(1);
        }

        
    }
    
}

?>
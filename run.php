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
     	 * @param array $header API request header
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
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 120);

        $this->result = curl_exec($this->ch);
        $this->error = curl_error($this->ch);
        if(!$this->result){
            if($this->error) {
                echo "[!] ".date('H:i:s')." | cURL Error: Gagal terhubung ke Server MotorQX! ...wait...\r\r";
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
        $randomuser = file_get_contents('https://econxn.id/api/v1/randomUser/?quantity=50');
        if($randomuser) {
            $json = json_decode($randomuser);
            if($json->status->code == 200) {
                return $json->result;
            } else {
                echo "[!] ".date('H:i:s')." | GAGAL Menggenerate Nama!\r\r";
                sleep(2);
                goto randomuser;
            }        
        } else {        
            sleep(2);
            goto randomuser;
        }
    }

    /**
     * Registrasi akun
     */
    function regis($name, $reff) { 
        // $reff = 'TOPSMVCN';
        $curl = new curl();

        $provider = ['0812', '0813', '0821', '0857', '0856', '0838', '0877'];
        $phone = $provider[rand(0,6)].$this->random_numb(rand(8,9));

        $method   = 'POST';
        $header   =  [
            'authorization: Bearer null',
            'mokita-enc: eyJlbmMtdmVyc2lvbiI6IjEuMS5xMiIsImNpcGhlcnRleHQiOiJnaGJSdVRJeGRndDg0UHVEZTVaM0F3PT0iLCJpdiI6IjE1ODQ5NTQyOTAxNTg0OTU0MjkwMTU4NDk1NDI5MDE1IiwidGltZXN0YW1wIjoxNTg0OTc5NDkwMjcwLCJyZXF1ZXN0VGltZXN0YW1wIjoxNTg0OTc5NDkwLCJ0aW1lc3RhbXBVdGMiOjE1ODQ5NTQyOTAyNzAsInJlcXVlc3RUaW1lc3RhbXBVdGMiOjE1ODQ5NTQyOTAsInNhbHQiOiJjMDIxN2NjZWFkYjE0MzQwNWZiNzBjZmU1ZjhjMjRkYzhmZDAyMDkyNGI5YjU0ZTQ1ODJhZjkxNjhmMTkzZDgyYWE4Y2U3OTk0NTYyYmFhZjY0NzI4NGNiNTkyNjUxNDM2MTY0MzdkOGM4OWY5OWQ3YzBiZWE4NzhjMWQzMWE0NjRmYjI0Y2JhYjkyZGZkZDk2NmVlMmYxYzU4NTY1ZWJjYmFlZTdmYjAzMGY3OTI1OGM1N2ZiZjM1NGEyNjI3NWE2ZmE3MTRjOWM1YWRlYmFjYzJlYjViNjRjZmVlODY4MTM4OTI0NzdkYTMxNjMxODM0NjA2MDVhYWEyNzI1NmUzYzc0YTFjZTE1YzQ3NDdlNDU5NTYzMDNjOWZkNTk0NzZhYzk4N2IyMzQ1ZTRkZDVhOTAyNjA5OGRhZWExNWU2MmIwZjY4Y2ZiYmI4Y2YxMzY1MDYyNmI4NWFjMDJjZDI5NGUyMjc1YTg0NWFhNzE2MzY1YjAxODJjYjkwOWMxZDEyZmU2ZDIyZmMzZjIyMGY0OTY4ZDU1NmY2MzRiOTNlMmFiMDk2ZTVhMzQyMGIwYTY1OTdhMGU1NjlkNjg4ZWY1OTJjMjIwOTYyYmJiNDM2M2M1ZTM5OWE1NmFhMTg4MWI1NDFiNjQ0NmEzZjg2YTA0Yzc5MGU0ZTg5MzNmYWU3NyIsIml0ZXJhdGlvbnMiOjk5OX0=',
            'Content-Type: application/json'
        ];
        $endpoint = '/api/register/q2';
        
        $param = '{"name":"'.$name.'","phone_number":"'.$phone.'","email":"","latitude":null,"longitude":null,"referral_code":"'.strtoupper($reff).'"}';

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
     * Login akun
     */
    function login($phone) {
        $curl = new curl();

        $method   = 'POST';
        $header   =  [
            'authorization: Bearer null',
            'Content-Type: application/json'
        ];
        $endpoint = '/api/login';

        $param = '{"msisdn":"'.$phone.'"}';

        $login = $curl->request ($method, $endpoint, $param, $header);
   
        $json = json_decode($login);

        if($json->status == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Verify akun
     */
    function verify_login($phone, $otp) {
        $curl = new curl();

        $method   = 'POST';
        $header   =  [
            'authorization: Bearer null',
            'Content-Type: application/json'
        ];
        $endpoint = '/api/otp/consume/login';

        $param = '{"phone_number":"'.$phone.'","otp":"'.$otp.'","key":"'.$otp.'","ahass_id":"'.$otp.'"}';

        $verify = $curl->request ($method, $endpoint, $param, $header);
   
        $json = json_decode($verify);

        if($json->status == 1) {
            return $json->token;
        } else {
            return FALSE;
        }
    }

    /**
     * Progress logging-in
     */
    function loginProgress() {
        login:
        echo "Masukkan No. HP :";
        $phone = trim(fgets(STDIN));
        $login = $this->login($phone);
        if($login===true) {

            verify:
            echo "Masukkan OTP    :";
            $otp = trim(fgets(STDIN));
            $verify = $this->verify_login($phone, $otp);
            if($verify===false) {
                echo "[!] Kode OTP SALAH!\n";
                goto verify;
            } else {
                $owner_token = $verify;
                unlink("token.txt");
                $fh = fopen("token.txt", "a");
                fwrite($fh, $owner_token);
                fclose($fh);
                return $owner_token;
            }

        } else {
            echo "[!] Login GAGAL! Enter R (Coba lagi!), Z (Lanjut tanpa Auto Redeem).\n";
            echo "Choice :";
            $choice = trim(fgets(STDIN));
            if(strtolower($choice) == 'r') {
                echo "\n";
                goto login;
            } elseif(strtolower($choice) != 'z') {
                echo "\n";
                die();
            }
            return FALSE;
        }
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

    /**
     * Get info profile
     */
    function profile($owner_token) {
        $curl = new curl();

        $method   = 'GET';
        $header   =  [
            'authorization: Bearer '.$owner_token
        ];
        $endpoint = '/api/profile';

        $profile = $curl->request ($method, $endpoint, $param=NULL, $header);
   
        $json = json_decode($profile);

        if($json->status == 1) {
            return $json;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Redeem Points
     */
    function voucher($categoryId=[], $token) {
        $curl = new curl();

        $method   = 'GET';
        $header   =  [
            'authorization: Bearer '.$token
        ];

        foreach ($categoryId as $catId) {
            $for = 1;
            for ($i=1; $i <= $for; $i++) { 
                back:
                $endpoint = '/api/deal/category/'.$catId.'?page='.$i;

                $voucher = $curl->request ($method, $endpoint, $param=NULL, $header);
        
                $json = json_decode($voucher);

                if($json->status == 1) {
                                
                    $total_pages = $json->meta->pagination->total_pages;
                    if($for==1) {
                        $for = $total_pages;
                    }
                                
                    foreach ($json->data as $data) { 
                        $vocList[] = [
                            'id'    => $data->id,
                            'name'  => $data->name,
                            'point' => $data->point
                        ];                      
                    }                   

                } else {
                    echo "[!] GAGAL Mendapatkan Daftar Voucher!\n";
                    goto back;
                }           
            }
        }
        
        return json_decode(json_encode($vocList)); 
    }

    /**
     * Redeem Points
     */
    function redeem($token, $item_id) {
        $curl = new curl();

        $method   = 'POST';
        $header   =  [
            'authorization: Bearer '.$token,
            'Content-Type: application/json'
        ];
        $endpoint = '/api/redeem/point';

        $param = '{"item_id":'.$item_id.'}';

        $redeem = $curl->request ($method, $endpoint, $param, $header);
   
        $json = json_decode($redeem);

        return $json;
    }

    /**
     * Inventory
     */
    function inventory($token) {
        $curl = new curl();

        $method   = 'GET';
        $header   =  [
            'authorization: Bearer '.$token
        ];
        $endpoint = '/api/inventory';

        $inventory = $curl->request ($method, $endpoint, $param=NULL, $header);
   
        $json = json_decode($inventory);

        return $json;
    }
 
}

/**
 * Running
 */

$motorku = new motorku();

echo "\nV2.7.1\nby @eco.nxn reCode By CJDW\n\nDisclaimer:\nSegala bentuk resiko atas tindakan ini saya pribadi tidak bertanggung jawab, gunakanlah senormal-nya!\n\n";
echo "Kode Referral :";
$reff = trim(fgets(STDIN));
poin:
echo "Target Poin   :";
$poin= trim(fgets(STDIN));
if(!is_numeric($poin)) {
    goto poin;
} elseif ($poin< 15){
    echo "[i] Masukkan jumlah poin yang diinginkan\n";
    goto poin;
}
echo "\n";

echo "Auto Redeem [Y/N] :";
$auto_redeem = trim(fgets(STDIN));
if (strtolower($auto_redeem)=='y') {

    check_token:
    $file  = "token.txt";
    $list  = explode("\n",str_replace("\r","",file_get_contents($file)));

    if(!empty($list[0])) { 
        $_token = $list[0];

        $status_login = $motorku->profile($_token);
        if($status_login === false) {
            echo "[!] Invalid Token!, Sesi telah habis.\n";
            $loginProgress = $motorku->loginProgress();
            if($loginProgress === false) {
                $validToken = FALSE;
            } else {
                $validToken = TRUE;
                $owner_token = $loginProgress;
            }
        } else {
            $validToken = TRUE;
            $owner_token = $_token;
        } 
    } else {
        $loginProgress = $motorku->loginProgress();
        if($loginProgress === false) {
            $validToken = FALSE;
        } else {
            $validToken = TRUE;
            $owner_token = $loginProgress;
        }
    }
} else {
    $validToken = FALSE;
}

if($validToken === true) {
    /**
     * Profile
     */
    $get_info = $motorku->profile($owner_token);
    $owner_nama  = $get_info->data->name;
    $owner_phone = $get_info->data->phone_number;
    $owner_point = $get_info->data->point;

    echo "[i] Anda sedang login sebagai ".$owner_nama." [".$owner_phone."], Total Poin: ".$owner_point."\n\n";

    echo "Pilih Kategori Voucher Yang Ingin Di Redeem!\n";
    echo "1. Makanan\n";
    echo "2. Belanja\n";
    echo "3. Semua\n";
    pilih:
    echo "Pilih :";
    $categori = trim(fgets(STDIN));
    if (!is_numeric($categori)) {
        goto pilih;
    } elseif($categori > 3) {
        goto pilih;
    }

    echo "\nDaftar Voucher Yang Akan Di Redeem Otomatis:\n";
    switch($categori) {
        case "1":
            $voc_selected = 1;
            $voucher = $motorku->voucher([2], $owner_token);  
        break;
        case "2":
            $voc_selected = 2;
            $voucher = $motorku->voucher([4], $owner_token);
        break;
        case "3":
            $voc_selected = 3;
            $voucher = $motorku->voucher([2,4], $owner_token);
        break;
    }

    $voc_no = 1;
    foreach ($voucher as $dataVoc) {
        $voc_id    = $dataVoc->id; 
        $voc_name  = $dataVoc->name;

        echo "[".$voc_no++."] ".$voc_id." | ".$voc_name."\n";
    }
        

    if($owner_point >= 5000) {
        echo "\n[i] POINT LO SUDAH BANYAK, JANGAN MARUK! LANJUT LANGSUNG REDEEM AJA.\n";

        while(true) {
            foreach ($voucher as $dataVoucher) {
                $item_id    = $dataVoucher->id; 
                $item_name  = $dataVoucher->name;
                $item_point = $dataVoucher->point;
    
                $get_info_point = $motorku->profile($owner_token);
                $owner_point = $get_info_point->data->point;
    
                if($owner_point >= $item_point) {
                    $redeem = $motorku->redeem($owner_token, $item_id); 
                    if($redeem->status == 1) { 
                        echo "[i] ".date('H:i:s')." | Yeeeaaay!!!".$item_name." berhasil di Redeem\n";
                        // Voucher Saya
                        $inventory = $motorku->inventory($owner_token);
                        if($inventory->status == 1) {
                            echo "\n[i] Voucher Saya :\n";
                            foreach ($inventory->data as $inventoryData) {
                                echo "[-] ".$inventoryData->deal->data->name."\n";
                                echo "[-] Kode :".$inventoryData->voucher->code."\n\n";

                                unlink("voucher.txt");
                                $fh = fopen("voucher.txt", "a");
                                fwrite($fh, $inventoryData->deal->data->name." | Kode :".$inventoryData->voucher->code."\n");
                                fclose($fh);
                            }
                          
                        } else {
                            "[!] ".date('H:i:s')." | Voucher :".$inventory->msg."\n\n";
                        }
                    } else {
                        echo "[!] ".date('H:i:s')." | GAGAL Redeem ".$item_name." | ".$redeem->msg."\n";
                    }
                } else {
                    echo "\nDONE!\n\n";
                    die();
                }           
            }

            switch($categori) {
                case "1":
                    $voucher = $motorku->voucher([2], $owner_token);  
                break;
                case "2":
                    $voucher = $motorku->voucher([4], $owner_token);
                break;
                case "3":
                    $voucher = $motorku->voucher([2,4], $owner_token);
                break;
            }
        }     
    }
}


$no=1;
$loop = $poin/15;
while(TRUE) {

    $randomuser = $motorku->randomuser();
    foreach ($randomuser as $value) {
        $firstname = $value->Firstname;
        $lastname  = $value->Lastname;

        for ($i=0; $i < 2; $i++) { 
            if($i==0) {
                $name = $firstname;
            } else {
                $name = $lastname;
            }

            $run = $motorku->regis($name, $reff);
            if($run===true) {
                echo "[".$no++."] ".date('H:i:s')." | Registrasi Berhasil.";

                if($validToken === true) {
                    $profile = $motorku->profile($owner_token);              
                    $owner_point = $profile->data->point;                  

                    echo " Total Poin Sekarang ".$owner_point.".\n";
              
                    foreach ($voucher as $dataVoucher) {
                        $item_id    = $dataVoucher->id;
                        $item_name  = $dataVoucher->name;
                        $item_point = $dataVoucher->point;

                        $get_info_point = $motorku->profile($owner_token);
                        $owner_point = $get_info_point->data->point;

                        if($owner_point >= $item_point) {
                            $redeem = $motorku->redeem($owner_token, $item_id);
                            if($redeem->status == 1) { 
                                echo "[i] ".date('H:i:s')." | Yeeeaaay!!!".$item_name." berhasil di Redeem\n";
                                // Voucher Saya
                                $inventory = $motorku->inventory($owner_token);
                                if($inventory->status == 1) {
                                    echo "\n[i] Voucher Saya :\n";
                                    foreach ($inventory->data as $inventoryData) {
                                        echo "[-] ".$inventoryData->deal->data->name."\n";
                                        echo "[-] Kode :".$inventoryData->voucher->code."\n\n";

                                        unlink("voucher.txt");
                                        $fh = fopen("voucher.txt", "a");
                                        fwrite($fh, $inventoryData->deal->data->name." | Kode :".$inventoryData->voucher->code."\n");
                                        fclose($fh);
                                    }
                                
                                } else {
                                    "[!] ".date('H:i:s')." | Voucher :".$inventory->msg."\n\n";
                                }
                            } else {
                                echo "[!] ".date('H:i:s')." | GAGAL Redeem ".$item_name." | ".$redeem->msg."\n";
                            }
                        }                                  
                    } 
                    
                    switch($categori) {
                        case "1":
                            $voucher = $motorku->voucher([2], $owner_token);  
                        break;
                        case "2":
                            $voucher = $motorku->voucher([4], $owner_token);
                        break;
                        case "3":
                            $voucher = $motorku->voucher([2,4], $owner_token);
                        break;
                    }

                } else {
                    echo "\n";
                }

                if($no > $loop) {
                    echo "\n\nDONE! Target Poin Tercapai.\n\n";
                    die();
                }             
            } else {
                echo "[!] ".date('H:i:s')." | Registrasi GAGAL\n";
            }
        }   
        
        if($validToken === true) {
            if($owner_point >= 5000) {
                echo "\n[i] POINT LO SUDAH BANYAK, JANGAN MARUK! LANJUT LANGSUNG REDEEM AJA.\n";
        
                while(true) {
                    foreach ($voucher as $dataVoucher) {
                        $item_id    = $dataVoucher->id;
                        $item_name  = $dataVoucher->name;
                        $item_point = $dataVoucher->point;
            
                        $get_info_point = $motorku->profile($owner_token);
                        $owner_point = $get_info_point->data->point;
            
                        if($owner_point >= $item_point) {
                            $redeem = $motorku->redeem($owner_token, $item_id);
                            if($redeem->status == 1) {
                                echo "[i] ".date('H:i:s')." | Yeeeaaay!!!".$item_name." berhasil di Redeem\n";
                                // Voucher Saya
                                $inventory = $motorku->inventory($owner_token);
                                if($inventory->status == 1) {
                                    echo "\n[i] Voucher Saya :\n";
                                    foreach ($inventory->data as $inventoryData) {
                                        echo "[-] ".$inventoryData->deal->data->name."\n";
                                        echo "[-] Kode :".$inventoryData->voucher->code."\n\n";

                                        unlink("voucher.txt");
                                        $fh = fopen("voucher.txt", "a");
                                        fwrite($fh, $inventoryData->deal->data->name." | Kode :".$inventoryData->voucher->code."\n");
                                        fclose($fh);
                                    }
                                
                                } else {
                                    "[!] ".date('H:i:s')." | Voucher :".$inventory->msg."\n\n";
                                }
                            } else {
                                echo "[!] ".date('H:i:s')." | GAGAL Redeem ".$item_name." | ".$redeem->msg."\n";
                            }
                        } else {
                            echo "\nDONE!\n\n";
                            die();
                        }           
                    }

                    switch($categori) {
                        case "1":
                            $voucher = $motorku->voucher([2], $owner_token);  
                        break;
                        case "2":
                            $voucher = $motorku->voucher([4], $owner_token);
                        break;
                        case "3":
                            $voucher = $motorku->voucher([2,4], $owner_token);
                        break;
                    }
                }       
            } 
        }         
    }   
}
?>

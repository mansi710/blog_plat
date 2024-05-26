<?php

namespace App\Repositories;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CommonRepository
{
	public function uploadFiles($input, $folder, $type = NULL) 
	{
		$destinationPath = $folder;
		$createFolder    = $this->createFolder($destinationPath);
		$imageName       = $input->getClientOriginalName();
		$ext             = $input->getClientOriginalExtension();
		$dbFileName      = Str::random(30).'.'.$ext;
		$isUpload        = $input->move($destinationPath, $dbFileName);
		if(!$isUpload){
			return false;
		}
		return $dbFileName;
	}
	public function createFolder($destinationPath)
	{
		if(!file_exists($destinationPath)){
			$create = mkdir($destinationPath, 0777, true);
		}
	}
	public function unlinkFile($unlinkpath)
	{
		if($unlinkpath != '' && file_exists($unlinkpath)){
			unlink($unlinkpath);
		}
	}
	public function removeFolder($folderpath)
    {
        if(is_dir($folderpath))
        {
            File::deleteDirectory($folderpath);
        }
    }
	public function translatelanguage($post)
	{
		$api_key = setting('api_key');
		$source  = "en";
 		$target  = $post['langcode'];
		$text    = $post['text'];

		$url  = 'https://www.googleapis.com/language/translate/v2?key=' . $api_key . '&q=' . rawurlencode($text);
		$url .= '&target='.$target;
		$url .= '&source='.$source;

		$response = file_get_contents($url);
        $obj 	  = json_decode($response,true);
        if($obj != null)
		{
			if(isset($obj['error']))
			{
			 	return false;
			}else{
			 	return $obj['data']['translations'][0]['translatedText'];
			}
		}else{
			return false;
		}
	}
    public function sendSMS($mobile_number, $message) 
    {
        $message = urlencode($message);
        $url = "http://sms.bharathonlinemarketing.org/V2/http-api.php?apikey=v6lpuXtOR71AaKYN&senderid=KALOOT&number=".$mobile_number."&message=".$message."&format=json";

        $ch = curl_init();   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
        curl_setopt($ch, CURLOPT_URL, $url);   
        $res = curl_exec($ch);
    }

}

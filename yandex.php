<?php
/*
Библиотека-класс для взаимодействия с Яндекс Диском
v1.2
-Добавлены новые методы:
share - открытие общего доступа к файлу или папке
last-download - список последних загруженных файлов
propertyFile - свойства файла
*/

defined( '_JEXEC' ) or die( 'Restricted access');
class diskClient
{
	function __construct($tok) {
        $this->token = $tok;
    }
	function downloadFile($path,$distination,$name)
	{
	$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/download?path=' . urlencode($path));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);
$res = json_decode($res, true);
if (empty($res['error'])) {
	$file_name = $distination . $name;
	$file = @fopen($file_name, 'w'); 
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $res['href']);
	curl_setopt($ch, CURLOPT_FILE, $file);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$fileContents=curl_exec($ch);
	if($errno = curl_errno($ch)) { //Вывод ошибок при выполнении cURL
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
	}
	curl_close($ch);
	fwrite($file, $fileContents);
	fclose($file);
	return true;
}
else return 'Ошибка: '.$res['message'];
	}
	function directoryContents($path)
	{
		$fields = '_embedded.items.name,_embedded.items.type,_embedded.items.size,_embedded.items.created,_embedded.items.mime_type,_embedded.items.public_url';
		$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode($path) . '&fields=' . $fields.'&sort=-modified' );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, true);
		if(isset($res['error'])) return 'Ошибка: '.$res['message'];
		else{
		$res=$res['_embedded']['items'];
		return $res;
		}
	}
		function propertyFile($path)
	{
		$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode($path) );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, true);
		if(isset($res['error'])) return 'Ошибка: '.$res['message'];
		else{
		//$res=$res['_embedded']['items'];
		return $res;
		}
	}
	function createDirectory($path)
	{
	$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/?path=' . urlencode($path));
	curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);
	$res = json_decode($res, true);
	if(isset($res['error']))
	{
	return 'Ошибка: '.$res['message'];
	}
	}
	function delete($path)
	{
	$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode($path) . '&permanently=true');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);
	$res = json_decode($res, true);
	if(isset($res['error']))
	{
	return 'Ошибка: '.$res['message'].'<br>';
	}
	}
	function uploadFile($path,$file)
	{
	// Запрашиваем URL для загрузки.
	$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/upload?path=' . urlencode($path . basename($file)));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);
 
	$res = json_decode($res, true);
	if (empty($res['error'])) {
	// Если ошибки нет, то отправляем файл на полученный URL.
	$fp = fopen($file, 'r');
 
 	$ch = curl_init($res['href']);
	curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_UPLOAD, true);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
	curl_setopt($ch, CURLOPT_INFILE, $fp);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res=curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$res = json_decode($res, true);
	if(isset($res['error']))
	{
	return 'Ошибка: '.$res['message'];
	} 
} 
	}
	function get_link($path)
	{
		$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/download?path=' . urlencode($path));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);
	$res = json_decode($res, true);
	if(isset($res['error'])) return 'Ошибка: '.$res['message'];
	else return $res['href'];
	}
	function preview($path,$distination,$name){
	$file_name = $distination . $name;
	$file = @fopen($file_name, 'w');
	$ch = curl_init('https://webdav.yandex.ru/'. urlencode($path).'?preview&size=XS');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	$res = curl_exec($ch);
	if($errno = curl_errno($ch)) { //Вывод ошибок при выполнении cURL
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
	}
	curl_close($ch);
	fwrite($file, $res);
	fclose($file);
	return true;
	}
	function last_download($limit)
	{
		$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/last-uploaded?limit='.$limit);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, true);
		if(isset($res['error'])) return 'Ошибка: '.$res['message'];
		else{
		$res=$res['items'];
		return $res;
	}
	}
	function share($path)
	{
		$ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/publish?path=' . urlencode($path));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->token));
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, true);
			if(isset($res['error']))
		{
		return 'Ошибка: '.$res['message'].'<br>';
		}
	}	
}
?>
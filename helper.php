<?php
defined( '_JEXEC' ) or die( 'Restricted access');
use Joomla\CMS\Uri\Uri;
class ModYandexdfHelper
{
	public static function view($preview,$params)
	{
	require_once dirname(__FILE__) . '/yandex.php';
$token=$params->get("token","");
$folder=$params->get("folder","");
$diskClient = new DiskClient($token);
    $path=$folder.$preview;
	$dirContent = $diskClient->directoryContents('/'.$path);
	$files=[];
	$i=0;
	foreach ($dirContent as $dirItem)
	{
	if ($dirItem['type'] === 'dir')	
	$files[$i]['dir']=TRUE;
	else $files[$i]['dir']=FALSE;
	$files[$i]['title']=$dirItem['name'];
	$files[$i]['time']=strtotime($dirItem['created']);
	if(!$files[$i]['dir']) 
	{
	$files[$i]['size']=$dirItem['size'];
	$way=Uri::root();
	$files[$i]['source']=$way.'modules/mod_yandex_disk_file/file.php?file='.substr($preview,1,strlen($preview)).$files[$i]['title'];
	}
	$i++;
	}
	return $files;
}
}
?>
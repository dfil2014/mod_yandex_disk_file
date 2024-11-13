<?php
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/helper.php';
if(isset($_GET['dirCon']))
{
$files = ModYandexdfHelper::view(base64_decode($_GET['dirCon']),$params);
$path_array=explode('/',base64_decode($_GET['dirCon']));
$path_array=array_values(array_diff($path_array,array('')));
$data_content='<table>';
if($params->get('caption','1')=='1')
{
$data_content.='<caption style="font-weight:bold">';
$data_content.='<a onclick="linker(\'\/\')" style="cursor:pointer">/</a>';
$s='/';
for($i=0;$i<count($path_array);$i++)
{
   $s.=$path_array[$i].'/';
   $data_content.='<a title="'.$s.'" onclick="linker(\''.base64_encode($s).'\')" style="cursor:pointer">'.$path_array[$i].'/</a>';
}
$data_content.='</caption>';
}
if($_GET['dirCon']!=base64_encode('/'))
{
    $data_content.='<tr><td><a style="cursor:pointer" onclick="linker(\'';
    $path_link='/';
    for($i=0;$i<count($path_array)-1;$i++)
    {
      $path_link.=$path_array[$i].'/';  
    }
    $data_content.=base64_encode($path_link).'\')">..</a></td></tr>';
}
for($i=0;$i<count($files);$i++)
{
	if(count($files)!=0)
	{
	if(isset($files[$i]['source'])) $link='href="'.$files[$i]['source'].'"'; else $link='onclick="linker(\''.base64_encode(base64_decode($_GET['dirCon']).$files[$i]['title'].'/').'\')"';
	if(isset($files[$i]['size'])) $size=((string)round((int)($files[$i]['size'])/1024)).' КБ'; else $size='';
	$data_content.= '<tr><td><a style="cursor:pointer" '.$link.'>'.$files[$i]['title'].'</a></td><td>'.$size.'</td><td>'.date('Y-m-d в H:i:s',$files[$i]['time']).'</td></tr>';
	}
}
	$data_content.='</table>';
}
else $data_content='';
echo $data_content; 
require JModuleHelper::getLayoutPath('mod_yandex_disk_file');
?>
<?php
function joom_con($site)//Функция подключения к Джумле
{                       //В качестве параметра указать папку с Джумлой
define( '_JEXEC', 1 );
if ( file_exists( __DIR__ . '/defines.php' ) ) {
    include_once __DIR__ . '/defines.php';
}
if ( !defined( '_JDEFINES' ) ) {
    define( 'JPATH_BASE',  $site );
    require_once JPATH_BASE . '/includes/defines.php';
}
require_once JPATH_BASE . '/includes/framework.php';
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
}
if (isset($_GET['file']))
{
    joom_con('../..');
$module = JModuleHelper::getModule('mod_yandex_disk_file');
require_once dirname(__FILE__) . '/yandex.php';
$params=json_decode($module->params);
$token=$params->token;
$folder=$params->folder;
$diskClient = new DiskClient($token);
$path = $folder.'/'.$_GET['file'];
$destination = dirname(__FILE__).'/downloads/';
$name = $_GET['file'];
if(stripos($name,'/'))
do $name=substr(stristr($name,'/'),1);
while (stripos($name,'/')); 
if ($diskClient->downloadFile($path, $destination, $name)) {
$file = $destination . $name;
$row=substr($row, 0, $str);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($file));
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
    if (ob_get_level()) {
      ob_end_clean();
    }
readfile($file);
unlink($file);
exit();
}
}

?>
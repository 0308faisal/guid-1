<?php
// PHP Upload Script for CKEditor:  http://coursesweb.net/
require_once('/var/www/html/guidedoc/rest_v2/config.inc.php');
require_once('/var/www/html/guidedoc/rest_v2/classes/db.class.php');
// HERE SET THE PATH TO THE FOLDER WITH IMAGES ON YOUR SERVER (RELATIVE TO THE ROOT OF YOUR WEBSITE ON SERVER)
$upload_dir = '/uploads/';

// HERE PERMISSIONS FOR IMAGE
$imgsets = array(
 'maxsize' => 2000,          // maximum file size, in KiloBytes (2 MB)
 'maxwidth' => 900,          // maximum allowed width, in pixels
 'maxheight' => 800,         // maximum allowed height, in pixels
 'minwidth' => 10,           // minimum allowed width, in pixels
 'minheight' => 10,          // minimum allowed height, in pixels
 'type' => array('bmp', 'gif', 'jpg', 'jpe', 'png')        // allowed extensions
);

$re = '';

if(isset($_FILES['upload']) && strlen($_FILES['upload']['name']) > 1) {
  $upload_dir = trim($upload_dir, '/') .'/';
  $img_name = basename($_FILES['upload']['name']);

  // get protocol and host name to send the absolute image path to CKEditor
  $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $site = $protocol. $_SERVER['SERVER_NAME'] .'/';

  $uploadpath = $_SERVER['DOCUMENT_ROOT'] .'/'. $upload_dir;       // full file path
  $sepext = explode('.', strtolower($_FILES['upload']['name']));
  $type = end($sepext);       // gets extension
  list($width, $height) = getimagesize($_FILES['upload']['tmp_name']);     // gets image width and height
  $err = '';         // to store the errors

  // Checks if the file has allowed type, size, width and height (for images)
  if(!in_array($type, $imgsets['type'])) $err .= 'The file: '. $_FILES['upload']['name']. ' is of the wrong type.';
  if($_FILES['upload']['size'] > $imgsets['maxsize']*1000) $err .= '\\n Maximum file size must be: '. $imgsets['maxsize']. ' KB.';
  if(isset($width) && isset($height)) {
    if($width > $imgsets['maxwidth'] || $height > $imgsets['maxheight']) $err .= '\\n Width x Height = '. $width .' x '. $height .' \\n The maximum Width x Height must be: '. $imgsets['maxwidth']. ' x '. $imgsets['maxheight'];
    if($width < $imgsets['minwidth'] || $height < $imgsets['minheight']) $err .= '\\n Width x Height = '. $width .' x '. $height .'\\n The minimum Width x Height must be: '. $imgsets['minwidth']. ' x '. $imgsets['minheight'];
  }

  // If no errors, upload the image, else, output the errors
  if($err == '') {
    $renamed = md5($img_name. time());      #rename of the file
    if (FILESTORAGE == 'file') {
      if(move_uploaded_file($_FILES['upload']['tmp_name'], $uploadpath.$renamed.".".$type)) {
        $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
        //$url = $site. $upload_dir .$renamed.".".$type;
        $url = "download.php?file=". $site. $upload_dir .$renamed.".".$type."&filename=";
        $message = $img_name .' successfully uploaded: \\n- Size: '. number_format($_FILES['upload']['size']/1024, 3, '.', '') .' KB \\n- Image Width x Height: '. $width. ' x '. $height;
        $re = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$message')";
      }
      else $re = 'alert("Unable to upload the file")';
    }
    elseif (FILESTORAGE == 'db') {
        $serverconf = HOSTS[$_SERVER['SERVER_NAME']];
        $Db = new Db(
            $serverconf['db_host'],
            $serverconf['db_user'],
            $serverconf['db_password'],
            $serverconf['db_name']
        );
        $path = $_FILES['upload']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', md5($_FILES['upload']['tmp_name'].time()), $ext);
        $filesize = filesize($_FILES['upload']['tmp_name']);
        $guidefile = $filename;
        $guidefilepath = $path;
        $guidefileext = $ext;
        $guideline_id=  isset($_GET['id'])?$Db->mysql_real_escape_equiv($_GET['guidelineid']):0;
        $guidefilecontent = $Db->mysql_real_escape_equiv(file_get_contents($_FILES['upload']['tmp_name']));
        $guidefilesize = $filesize;
        $current_date = date('Y-m-d H:i:s');
        $guidelinefilequery = "INSERT INTO guideline_files (guideline_id,filename,dl_filename,filetype,content,filesize,cdate) VALUES ('{$guideline_id}','{$guidefile}','{$guidefilepath}','{$guidefileext}','{$guidefilecontent}','{$guidefilesize}','{$current_date}')";
        $guidelinefileresult = $Db->execute($guidelinefilequery);

        $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
        $url = BASE_URL."frontend/download.php?file=".$filename."&filename=".$guidefilepath;
        $message = $img_name .' successfully uploaded: \\n- Size: '. number_format($_FILES['upload']['size']/1024, 3, '.', '') .' KB \\n- Image Width x Height: '. $width. ' x '. $height;
        $re = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$message')";
        //$re = 'alert("'.$CKEditorFuncNum.'--'.$url.'--'.$message.'")';
      }
  }
  else $re = 'alert("'. $err .'")';
}
echo "<script>$re;</script>";

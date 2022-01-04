<?php

declare(strict_types=1);
\ob_start();
require_once '../functions.php';
require_once '../rest_v2/classes/db.class.php';
//if (!isset($_COOKIE['token'])) {
//    header('location: register.php');
//}
$new_filename = $_GET['filename'];
//Mime types below
if (FILESTORAGE == 'file') {
	$original_filename = DATA_DIR . '/uploads/' . $_GET['file'];
	$ext = \pathinfo($original_filename, \PATHINFO_EXTENSION);

	switch (\strtolower($ext)) {
		case 'gif': $ctype = 'image/gif';
			break;
		case 'png': $ctype = 'image/png';
			break;
		case 'jpeg':
		case 'jpg': $ctype = 'image/jpeg';
			break;
		case 'pdf': $ctype = 'application/pdf';
			break;
		default: $ctype = 'none';
	}

	if ((isset($_GET['mobile']) && $_GET['mobile'] == 'true') || ($ctype !== 'none' && $ctype !== 'application/pdf')) {
		\header('Content-Disposition: inline; filename="' . $new_filename . '"');
		\header('Content-Type: ' . $ctype);
	} else {
		\header('Content-Disposition: inline; filename="' . $new_filename . '"');
		\header('Content-Type: ' . $ctype);
		//header('Content-Length: ' . filesize($original_filename));
	}
	\readfile($original_filename);
} elseif (FILESTORAGE == 'db') {
	$serverconf = HOSTS[$_SERVER['SERVER_NAME']];
	$Db = new Db(
		$serverconf['db_host'],
		$serverconf['db_user'],
		$serverconf['db_password'],
		$serverconf['db_name']
	);
	$filequery = "  SELECT filename, filetype, filesize, content
    										FROM guideline_files
                        WHERE filename='" . $Db->mysql_real_escape_equiv($_GET['file']) . "'
                        UNION
                        SELECT filename, filetype, filesize, content
                    		FROM network_files
    										WHERE filename='" . $Db->mysql_real_escape_equiv($_GET['file']) . "' limit 1";

	$fileresult = $Db->execute($filequery);

	if ($fileresult !== false) {
		if ($Db->count() > 0) {
			switch (\strtolower($fileresult[0]['filetype'])) {
				case 'gif': $ctype = 'image/gif';
					break;
				case 'png': $ctype = 'image/png';
					break;
				case 'jpeg':
				case 'jpg': $ctype = 'image/jpeg';
					break;
				case 'pdf': $ctype = 'application/pdf';
					break;
				default: $ctype = 'none';
			}

			if ((isset($_GET['mobile']) && $_GET['mobile'] == 'true') || ($ctype !== 'none' && $ctype !== 'application/pdf')) {
				\header('Content-Disposition: inline; filename="' . $new_filename . '"');
				\header('Content-Type: ' . $ctype);
			} else {
				\header('Content-Disposition: inline; filename="' . $new_filename . '"');
				\header('Content-Type: ' . $ctype);
				//header('Content-Length: ' . $fileresult[0]['filesize']);
			}
			\ob_end_clean();
			echo $fileresult[0]['content'];
		} else {
			echo 'Image not found.';
		}
	}
}
exit;

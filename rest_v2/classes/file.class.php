<?php

class File extends API
{

	public function __construct($request = null)
	{
		if(isset($request))
		{
			parent::__construct($request);
		}
	}

  /**
	 * @internal
	 */
	static function getFiles($type, $id = null)
	{
		$files = array();
		$Db=$GLOBALS['Db'];
		$id = isset($id) ? $id : parent::args[0];
		$filequery = "SELECT filename,dl_filename, filetype, filesize,cdate" .
				" FROM " . $type . "_files" .
				" WHERE " . $type . "_id='" . $id . "'";
		$fileresult = $Db->execute($filequery);
		if($fileresult !== false)
		{
			if($Db->count() > 0)
			{
				foreach($fileresult as $row)
				{
					$files[] = $row;
				}
			}
		}
		return $files;
	}
}

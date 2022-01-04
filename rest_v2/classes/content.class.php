<?php
class Content extends API
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
	static function getContent($id = null)
	{
		$Db=$GLOBALS['Db'];
		$id = isset($id) ? $id : parent::args[0];
		$contents=array();
		$contentquery = "SELECT title, content, warning
											FROM guideline_content
											WHERE guideline_id='" . $id . "'";
		$contentresult = $Db->execute($contentquery);
		if($contentresult !== false)
		{
			if($Db->count() > 0)
			{
				foreach($contentresult as $row)
				{
					$contents[] = $row;
				}
			}
		}
		return $contents;
	}
}

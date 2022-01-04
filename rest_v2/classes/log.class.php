<?php

class Log extends API
{

	public function __construct($request = null)
	{
		if(isset($request))
		{
			parent::__construct($request);
		}
	}

	static function getlog($identifier = null)
	{
		$Db=$GLOBALS['Db'];
		$sql=$contents="";
		if(!empty($identifier))
		{
			$sql = " WHERE guideline_id='" . $identifier . "' ";
		}
		$historyquery = "SELECT guideline_id, email, mdate, comment
													FROM log
													JOIN members on members.id=log.user_id" . $sql . "
													ORDER by mdate desc limit 5";
		$historyresult = $Db->execute($historyquery);
		if($historyresult !== false)
		{
			if($Db->count() > 0)
			{
				$contents = array();
				foreach($historyresult as $row)
				{
					$contents[] = $row;
				}
			}
			return $contents;
		}
	}

	static function logcomment($guideid, $userid, $comment)
	{
		$Db=$GLOBALS['Db'];
		$mdate = date('Y-m-d H:i:s');
		$commentquery = "INSERT into log (guideline_id,user_id,mdate,comment) values('{$guideid}','{$userid}','{$mdate}','{$comment}')";
		$Db->execute($commentquery);
	}

}

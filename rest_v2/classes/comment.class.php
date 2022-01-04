<?php

class Comment extends API
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
	static function getComments($id = null)
	{
		$Db=$GLOBALS['Db'];
		$id = isset($id) ? $id : parent::args[0];
		$comments = array();
		$commentquery = "SELECT gc.comment,m.id as member_id,m.fname,m.lname, gc.cdate FROM guideline_comments gc
								JOIN members m on m.id=gc.member_id
								JOIN members mb on mb.id=gc.member_id
								WHERE gc.guideline_id='" . $id . "'";
		$commentresult = $Db->execute($commentquery);
		if($commentresult !== false)
		{
			foreach($commentresult as $row)
			{
				$comments[] = array('comment' => $row['comment'], 'member' => $row['fname'] . ' ' . $row['lname'], 'member_id' => $row['member_id'], 'cdate' => $row['cdate']);
			}
		}

		return $comments;
	}

	protected function addComments()
	{
		if($this->method == 'POST')
		{
				if(isset($this->request['id']) && is_numeric($this->request['id']) && isset($this->request['comment']) && $this->request['comment'] != '')
				{

					$user = User::getuser();
					$today = date('Y-m-d H:i:s');
					$ip_address = $this->_getip();
					$commentquery = "INSERT into guideline_comments (guideline_id,comment,member_id,cdate,ip_address) values('" . $this->request['id'] . "','" . $this->request['comment'] . "','" . $user['id'] . "','" . $today . "','" . $ip_address . "')";
					$commenttresult = $this->Db->execute($commentquery);
					if($commenttresult == false)
					{
						throw new Exception('Database error when creating comment');
					}
					else
					{
						return json_encode(array('status' => 'success',
							'response' => 'Comment added successful',));
					}
				}
				else
				{
					throw new Exception('Guideline ID and comment required');
				}
		}
		else
		{
			throw new Exception('Only accepts POST requests');
		}
	}
}

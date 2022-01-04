<?php
class Community extends API
{
	/**
	 * @internal
	 */
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
	protected function communityrequestlist()
	{
		if($this->method == 'GET')
		{

			$user = User::getuser();
			$networkquery = "SELECT distinct n.id, ca.status,m.email, n.name, c.nicename as country, n.cdate FROM community_access ca" .
					" LEFT JOIN network n on n.id=ca.network_id" .
					" LEFT JOIN members m on m.id=ca.member_id" .
					" LEFT JOIN country c on c.id=n.country_id";

			$networkresult = $this->Db->execute($networkquery);
			if($networkresult !== false)
			{
				if($this->Db->count() > 0)
				{
					return json_encode(array('networks' => $networkresult,
						'status' => 'success',
						'response' => 'Community Networks loaded successfully ',));
				}
			}
			else
			{
				throw new Exception('Database error when retrieving networks ' . $this->Db->getError());
			}
		}
		else
		{
			throw new Exception('Only accepts GET requests');
		}
	}

	/**
	 * @internal
	 */
	protected function updatecommunityaccess()
	{
		if($this->method == 'GET')
		{


				$user = User::getuser();


				if($this->request['status'] == 'approve')
				{
					$userquery = "update community_access ca set status='active' where ca.network_id='" . $this->request['nid'] . "'";
					$userresult = $this->Db->execute($userquery);
				}
				elseif($this->request['status'] == 'freeze')
				{
					$userquery = "update community_access ca set status='freeze' where ca.network_id='" . $this->request['nid'] . "'";
					$userresult = $this->Db->execute($userquery);
				}
				elseif($this->request['status'] == 'pending')
				{
					$userquery = "update community_access ca set status='pending' where ca.network_id='" . $this->request['nid'] . "'";
					$userresult = $this->Db->execute($userquery);
				}
				elseif($this->request['status'] == 'remove')
				{
					$userquery = "delete ca from community_access ca join members m on m.id=ca.member_id where ca.network_id='" . $this->request['nid'] . "'";
					$userresult = $this->Db->execute($userquery);
				}
				if($this->Db->affectedRows() == 1)
				{
					return json_encode(array('status' => 'success',
						'response' => 'Network status updated',));
				}
				else
				{
					throw new Exception('Database error when updating network status - ' . $userquery);
				}
		}
		else
		{
			throw new Exception('Only accepts GET requests');
		}
	}
}

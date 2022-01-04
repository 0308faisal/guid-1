<?php

require_once 'api.class.php';

/**
 * The basic methodology will be:
 * Data retrieval via Curl
 * Data posting via Ajax.
 *
 * If you need to test the API to validate data I suggest the following chrome plugin, works very well:
 * https://chrome.google.com/webstore/detail/advanced-rest-client/hgmloofddffdnphfgcellkdfbfbjeloo?utm_source=chrome-ntp-icon
 *
 * an example call: http://public.guidedoc.co/rest/user?token=XXXXXXX
 */
class Guideapi extends API
{

	/**
	 * @internal
	 */
	protected $User;

	/**
	 * @internal
	 */
	public function __construct($request)
	{
		parent::__construct($request);
	}





	protected function activationlist()
	{
		if($this->method == 'GET')
		{

				$user = User::getuser();
				/* foreach($user['networks'] as $key => $value)
				  {
				  if($value['status']=="active" && $value["manager"]=="true"){
				  $networks[] = $value['network_id'];
				  }
				  } */
				$networks[] = $this->request['n2n'];
				$memberquery = "SELECT m.id,m.email,m.fname,m.lname,n.name as network,mad.activation_code FROM members m
								JOIN members_activation_data mad on mad.member_id=m.id
								JOIN network n on n.id=mad.network_id
								WHERE n.id in (" . implode(",", $networks) . ") and email not in (
								select members.email
								from members
								inner join network on find_in_set(SUBSTRING(members.email, LOCATE('@', members.email) + 1),network.domain)
								)";
				$memberresult = $this->Db->execute($memberquery);
				if($memberresult !== false)
				{
					if($this->Db->count() > 0)
					{
						return json_encode(array('members' => $memberresult,
							'status' => 'success',
							'response' => 'Members loaded successfully',));
					}
					else
					{
						throw new Exception('Wrong network count for query');
					}
				}
				else
				{
					throw new Exception('Database error when retrieving members ');
				}
		}
		else
		{
			throw new Exception('Only accepts GET requests');
		}
	}

	protected function approvallist()
	{
		if($this->method == 'GET')
		{
				$user = User::getuser();
				/* foreach($user['networks'] as $key => $value)
				  {
				  $networks[] = $value['network_id'];
				  } */
				$networks[] = $this->request['n2n'];
				$memberquery = "SELECT m.id,m.email,m.fname,m.lname,n.name as network FROM members m
								inner join network n on find_in_set(SUBSTRING(m.email, LOCATE('@', m.email) + 1),n.domain)
								WHERE n.id in (" . implode(",", $networks) . ")";
				$memberresult = $this->Db->execute($memberquery);
				if($memberresult !== false)
				{
					if($this->Db->count() > 0)
					{
						return json_encode(array('members' => $memberresult,
							'status' => 'success',
							'response' => 'Members loaded successfully',));
					}
					else
					{
						throw new Exception('Wrong network count for query');
					}
				}
				else
				{
					throw new Exception('Database error when retrieving members ');
				}
		}
		else
		{
			throw new Exception('Only accepts GET requests');
		}
	}

	protected function categorylist()
	{
		if($this->method == 'GET')
		{
				$user=User::getuser();
				$categories = array();
				$categoryquery = "SELECT g.categories FROM guideline g
							LEFT JOIN guideline_network_settings gns on (gns.guideline_id=g.id)
							LEFT JOIN network_members nm on nm.network_id=gns.network_id
							LEFT JOIN members m on m.id=nm.member_id
							WHERE m.id='" . $user['id'] . "' and g.categories!=''";
				$categoryresult = $this->Db->execute($categoryquery);
				if($categoryresult !== false)
				{
					if($this->Db->count() > 0)
					{
						foreach($categoryresult as $category)
						{
							if(strpos($category['categories'], ','))
							{
								$categories = array_merge($categories, explode(',', strtolower($category['categories'])));
							}
							else
							{
								$categories[] = strtolower($category['categories']);
							}
						}
						$categories = array_values(array_unique($categories));

						return json_encode(array('category_count' => count($categories),
							'categories' => $categories,
							'status' => 'success',
							'response' => 'Networks loaded successfully',));
					}
				}
				else
				{
					throw new Exception('Database error when retrieving categories ');
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
	protected function getDropdownData()
	{
		if($this->method == 'GET')
		{
			switch($this->request['field'])
			{
				case 'profession':
					$cols = 'id,name';
					$tbl = 'members_occupation_options';
					break;
				case 'speciality':
					$cols = 'id,name';
					$tbl = 'members_speciality_options';
					break;
				case 'grade':
					$cols = 'id,occupation_id,name';
					$tbl = 'members_name_grade_options';
					break;
				case 'country':
					$cols = 'id,name';
					$tbl = 'country';
					break;
			}

			$contentquery = "SELECT ".$cols." FROM " . $tbl;
			if(isset($this->request['occupation_id']) && is_numeric($this->request['occupation_id']))
			{
				$contentquery .= " where occupation_id='" . $this->request['occupation_id'] . "'";
			}
			$contentresult = $this->Db->execute($contentquery);
			if($contentresult !== false)
			{
				if($this->Db->count() > 0)
				{
					foreach($contentresult as $row)
					{
						$contents[] = $row;
					}

					return json_encode(array("data"=>$contents,
						'status' => 'success',
						'response' => 'Fields loaded successfully',));
				}
				else
				{
					$contents = '';

					return json_encode(array($contents,
						'status' => 'success',
						'response' => 'Fields loaded successfully',));
				}
			}
			else
			{
				throw new Exception('Database error when selecting fields'.$contentquery);
			}
		}
		else
		{
			throw new Exception('Only accepts GET requests');
		}
	}











}

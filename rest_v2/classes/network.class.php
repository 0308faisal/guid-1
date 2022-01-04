<?php

declare(strict_types=1);
/**
 * Network API
 *
 * @endpoint /network
 */
class Network extends API
{
    /**
     * @internal
     *
     * @param null|mixed $request
     */
    public function __construct($request = null)
    {
        if (isset($request)) {
            parent::__construct($request);
        }
    }

    /**
     * @OA\Get(
     *     path="/rest_v2/network/getNetworks",
     *     tags={"Get networks"},
     *     summary="Retrieve network listing",
     *     @OA\Response(
     *         response=200,
     *         description="JSON object"
     *     ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public static function getNetworks($identifier = null)
    {
        $Db = $GLOBALS['Db'];
        $subquery = $managerquery = $wherequery = '';

        if (!empty($identifier)) {
            $managerquery = ",nm.status, if(nm.manager=1, 'true', 'false') as manager";
            $subquery = " LEFT JOIN network_members nm on nm.network_id=n.id and nm.member_id={$identifier}";
            $wherequery = " WHERE nm.status='active' and (n.network_type = 'public' or n.network_type = 'private')";
        } elseif (empty($identifier) && Auth::validatetoken() !== false) {
            $user = User::getuser();
            $identifier = $user['id'];
            $managerquery = ',nm.status ';
            $subquery = " LEFT JOIN network_members nm on nm.network_id=n.id and nm.member_id={$identifier}";

            if ($user['admin'] !== 1) {
                $wherequery = " WHERE (n.network_type = 'public' or n.network_type = 'private')";
            }
        } else {
            $wherequery = " WHERE (n.network_type = 'public' or n.network_type = 'private')";
        }

        $networkquery = 'SELECT distinct n.id,(select count(gns.guideline_id) from guideline_network_settings gns where gns.network_id=n.id) as guidelines, n.name,n.description,n.logo,n.network_type, c.nicename as country, n.cdate' . $managerquery . ' FROM network n' . $subquery .
                    ' LEFT JOIN country c on c.id=n.country_id' . $wherequery;
        $networkresult = $Db->execute($networkquery);

        if ($networkresult !== false) {
            if ($Db->count() > 0) {
                foreach ($networkresult as $key => $row) {
                    $networkresult[$key]['logo'] = File::getFiles('network', $row['id']);
                }
                return $networkresult;
            }

            return false;
        }

        throw new Exception('Database error when retrieving networks');
    }

    /**
     * @internal
     */
    protected function approvenetwork()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();

            if ($user['admin'] == '1') {
                $networkquery = "update network n set status='{$this->request['status']}' where n.id='" . $this->request['nid'] . "'";
                $networkresult = $this->Db->execute($networkquery);

                if ($this->Db->affectedRows() == 1) {
                    return \json_encode(['status' => 'success',
                        'response' => 'Network status updated', ]);
                }

                throw new Exception('Database error when updating network status ');
            }

            throw new Exception('Invalid Permissions');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @OA\Get(
     *     path="/rest_v2/network/getnetwork/{id}",
     *     tags={"Single network"},
     *     summary="Get single network details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Network id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
	 *             
     *         )
     *     ),
	 * 
     *     @OA\Response(
     *         response=200,
     *         description="Network loaded successfully"
     *     ),
	 *  security={{"bearerAuth":{}}}
     * )
     */
    protected function getNetwork()
    {
        if ($this->method == 'GET') {
            if (isset($this->args[0]) && \is_numeric($this->args[0])) {
                if (empty($identifier)) {
                    $user = User::getuser();
                    $identifier = $user['id'];
                }
                $manager = 0;

                foreach ($user['networks'] as $key => $network) {
                    if ($network['id'] == $this->args[0] && $network['manager'] == 1) {
                        $manager = 1;
                    }
                }
                $networkquery = 'SELECT distinct n.id, n.name,n.domain,n.clonefrom,n.approve,n.description, n.website,n.logo,n.owner_email,n.network_type,n.network_ga_m,n.network_ga_w';

                if ($user['admin'] !== 1) {
                    $networkquery .= " ,if(nm.manager=1, 'true', 'false') as manager";
                }
                $networkquery .= '  FROM network n';
                $userquery = '';

                if ($user['admin'] !== 1) {
                    $networkquery .= ' JOIN network_members nm on n.id=nm.network_id JOIN members m on m.id=nm.member_id';
                    $userquery = " and m.id='{$identifier}'";
                }
                $networkquery .= " WHERE n.id='" . $this->args[0] . "'" . $userquery . " and n.status='approved'";
                $networkresult = $this->Db->execute($networkquery);

                if ($networkresult !== false) {
                    if ($this->Db->count() == 1) {
                        return \json_encode(['id' => $networkresult[0]['id'],
                            'network_name' => $networkresult[0]['name'],
                            'network_domain' => $networkresult[0]['domain'],
                            'clonefrom' => $networkresult[0]['clonefrom'],
                            'network_approve' => $networkresult[0]['approve'],
                            'description' => $networkresult[0]['description'],
                            'network_website' => $networkresult[0]['website'],
                            'network_logo' => File::getFiles('network', $networkresult[0]['id']),
                            'owner_email' => $networkresult[0]['owner_email'],
                            'network_type' => $networkresult[0]['network_type'],
                            'network_ga_m' => $networkresult[0]['network_ga_m'],
                            'network_ga_w' => $networkresult[0]['network_ga_w'],
                            'is_manager' => $manager,
                            'status' => 'success',
                            'response' => 'Network loaded successfully', ]);
                    }

                    throw new Exception('Wrong network count for query' . $networkquery);
                }

                throw new Exception('Database error when retrieving networks' . $networkquery);
            }

            throw new Exception('Network ID required');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function networkcreate()
    {
        if ($this->method == 'POST') {
            $user = User::getuser();
            $manager = 0;

            $name = $this->request['network_name'];
            $description = $this->request['network_description'];
            $network_type = $this->request['network_type'];
            $network_ga_m = $this->request['network_ga_m'];
            $network_ga_w = $this->request['network_ga_w'];
            $networklogofile = '';

            if (isset($this->file['network_logo']['name']) && $this->file['network_logo']['name'] !== '') {
                $path = $this->file['network_logo']['name'];
                $ext = \pathinfo($path, \PATHINFO_EXTENSION);
                $filename = \sprintf('%s.%s', \md5($_FILES['network_logo']['tmp_name'] . \time()), $ext);
                $filesize = \filesize($_FILES['network_logo']['tmp_name']);

                if (FILESTORAGE == 'file') {
                    if (!\move_uploaded_file($this->file['network_logo']['tmp_name'], DATA_DIR . '/uploads/network_logo/' . $filename)) {
                        throw new RuntimeException('Failed to move uploaded file.');
                    }

                    $networklogofile = $filename;
                    $networklogofilepath = DATA_DIR . '/uploads/network_logo/' . $filename;
                    $networklogofileext = $ext;
                } elseif (FILESTORAGE == 'db') {
                    $networklogofile = $filename;
                    $networklogofilepath = $path;
                    $networklogofileext = $ext;
                    $networklogofilecontent = $this->Db->mysql_real_escape_equiv(\file_get_contents($_FILES['network_logo']['tmp_name']));
                    $networklogofilesize = $filesize;
                }
            }

            $country = $this->request['network_country'];
            $owner_id = $user['id'];
            $owner_email = $user['email'];
            $website = $this->request['network_website'];
            $networkquery = "INSERT INTO network (name,description,network_type,network_ga_m,network_ga_w,approve,logo,country_id,owner_id,owner_email,website) values('{$name}','{$description}','{$network_type}','{$network_ga_m}','{$network_ga_w}','0','{$networklogofile}','{$country}','{$owner_id}','{$owner_email}','{$website}')";
            $networkupdateresult = $this->Db->execute($networkquery);

            if ($networkupdateresult !== false) {
                $network_id = $this->Db->lastInsertID();
                $communityquery = "INSERT INTO community_access (network_id, member_id, status) values('{$network_id}','{$user['id']}','pending')";
                $communityresult = $this->Db->execute($communityquery);
                $networkmembersquery = "INSERT INTO network_members (network_id,member_id,manager,status) values('{$network_id}','{$user['id']}','1','active')";
                $networkmembersresult = $this->Db->execute($networkmembersquery);

                if (isset($networklogofile) && $networklogofile !== '') {
                    $current_date = \date('Y-m-d H:i:s');
                    $networklogofilequery = "INSERT INTO network_files (network_id,filename,dl_filename,filetype,content,filesize,cdate) VALUES ('{$network_id}','{$networklogofile}','{$networklogofilepath}','{$networklogofileext}','{$networklogofilecontent}','{$networklogofilesize}','{$current_date}') ON DUPLICATE KEY UPDATE filename='{$networklogofile}',dl_filename = '{$networklogofilepath}',content='{$networklogofilecontent}',cdate = '{$current_date}'";
                    $networklogofileresult = $this->Db->execute($networklogofilequery);
                }
                return \json_encode([
                    'status' => 'success',
                    'response' => 'Network saved successfully', ]);
            }

            throw new Exception('Database error when saving network ' . $this->Db->getError());
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function networkedit()
    {
        if ($this->method == 'POST') {
            $user = User::getuser();
            $userid = $user['id'];
            $manager = 'false';
            $network_id = $this->args[0];

            foreach ($user['networks'] as $key => $network) {
                if ($network['id'] == $network_id && $network['manager'] == 1) {
                    $manager = 1;
                }
            }

            if ($manager == 'true' && $user['admin'] !== 1) {
                throw new Exception('You do not have permission to edit this network');
            }
            $networkquery = "SELECT n.id,n.logo FROM network n
								WHERE n.id='" . $network_id . "'";
            $networkresult = $this->Db->execute($networkquery);

            if ($networkresult !== false) {
                if ($this->Db->count() == 1) {
                    $name = $this->request['network_name'];
                    $domain = \str_replace(' ', '', $this->request['network_domain']);
                    $clonefrom = $this->request['clonefrom'];
                    $approve = $this->request['network_approve'] == 'on' ? 1 : 0;
                    $description = $this->request['network_description'];
                    $network_type = $this->request['network_type'];
                    $network_ga_m = $this->request['network_ga_m'];
                    $network_ga_w = $this->request['network_ga_w'];
                    $networklogofile = $networkresult[0]['logo'];
                    $fileupload = false;

                    if (isset($this->file['network_logo']['name']) && $this->file['network_logo']['name'] !== '') {
                        $fileupload = true;
                        $path = $this->file['network_logo']['name'];
                        $ext = \pathinfo($path, \PATHINFO_EXTENSION);
                        $filename = \sprintf('%s.%s', \md5($_FILES['network_logo']['tmp_name'] . \time()), $ext);
                        $filesize = \filesize($_FILES['network_logo']['tmp_name']);

                        if (FILESTORAGE == 'file') {
                            if (!\move_uploaded_file($this->file['network_logo']['tmp_name'], DATA_DIR . '/uploads/network_logo/' . $filename)) {
                                throw new RuntimeException('Failed to move uploaded file.');
                            }

                            $networklogofile = $filename;
                            $networklogofilepath = DATA_DIR . '/uploads/network_logo/' . $filename;
                            $networklogofileext = $ext;
                        } elseif (FILESTORAGE == 'db') {
                            $networklogofile = $filename;
                            $networklogofilepath = $path;
                            $networklogofileext = $ext;
                            $networklogofilecontent = $this->Db->mysql_real_escape_equiv(\file_get_contents($_FILES['network_logo']['tmp_name']));
                            $networklogofilesize = $filesize;
                        }
                    }

                    $country = $this->request['network_country'];
                    $owner_email = $this->request['network_email'];
                    $website = $this->request['network_website'];
                    $networkupdatequery = "UPDATE network set name='{$name}',domain='{$domain}',clonefrom='{$clonefrom}',approve='{$approve}', description='{$description}', network_type='{$network_type}', network_ga_m='{$network_ga_m}',network_ga_w='{$network_ga_w}', logo='{$networklogofile}', country_id='{$country}', owner_email='{$owner_email}', website='{$website}' where id='{$network_id}'";
                    $networkupdateresult = $this->Db->execute($networkupdatequery);

                    if ($networkupdateresult !== false) {
                        if (isset($networklogofile) && $fileupload == true) {
                            $current_date = \date('Y-m-d H:i:s');
                            $networklogofilequery = "INSERT INTO network_files (network_id,filename,dl_filename,filetype,content,filesize,cdate) VALUES ('{$network_id}','{$networklogofile}','{$networklogofilepath}','{$networklogofileext}','{$networklogofilecontent}','{$networklogofilesize}','{$current_date}') ON DUPLICATE KEY UPDATE filename='{$networklogofile}',dl_filename = '{$networklogofilepath}',content='{$networklogofilecontent}',cdate = '{$current_date}'";
                            $networklogofileresult = $this->Db->execute($networklogofilequery);
                        }
                        return \json_encode([
                            'status' => 'success',
                            'response' => 'Network saved successfully', ]);
                    }

                    throw new Exception('Database error when saving network ' . $this->Db->getError());
                }
            } else {
                throw new Exception('Database error when retrieving network ');
            }
        } else {
            throw new Exception('Only accepts GET requests');
        }
    }

    /**
     * @internal
     */
    protected function networkaccesslist()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();
            $networkquery = "SELECT distinct n.id as network_id, if(na.request_network_id={$this->request['n2n']}, '1', '0') as member,na.status, n.name,n.network_type, c.nicename as country, n.cdate FROM community_access ca" .
                    " LEFT JOIN network n on n.id=ca.network_id and ca.status='active'" .
                    " LEFT JOIN network_access na on na.network_id=n.id and na.request_network_id={$this->request['n2n']}" .
                    ' LEFT JOIN country c on c.id=n.country_id' .
                    " WHERE n.status='approved'";

            if ($user['admin'] !== 1) {
                $networkquery .= " and (n.network_type = 'public' or n.network_type = 'private')";
            }
            $networkresult = $this->Db->execute($networkquery);

            if ($networkresult !== false) {
                if ($this->Db->count() > 0) {
                    foreach ($networkresult as $key => $row) {
                        $networkresult[$key]['logo'] = File::getFiles('network', $row['network_id']);
                    }
                    return \json_encode(['networks' => $networkresult,
                        'status' => 'success',
                        'response' => 'Networks loaded successfully ', ]);
                }

                throw new Exception('Network not found');
            }

            throw new Exception('Database error when retrieving networks ');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function networkapprovallist()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();
            $networkquery = 'SELECT distinct n.id as network_id, n.name,n.network_type, c.nicename as country, n.cdate, n.status FROM network n' .
                    ' LEFT JOIN country c on c.id=n.country_id';
            $networkresult = $this->Db->execute($networkquery);

            if ($networkresult !== false) {
                if ($this->Db->count() > 0) {
                    foreach ($networkresult as $key => $row) {
                        $networkresult[$key]['logo'] = File::getFiles('network', $row['network_id']);
                    }
                    return \json_encode(['networks' => $networkresult,
                        'status' => 'success',
                        'response' => 'Networks loaded successfully ', ]);
                }
            } else {
                throw new Exception('Database error when retrieving networks ' . $this->Db->getError());
            }
        } else {
            throw new Exception('Only accepts GET requests');
        }
    }

    /**
     * @internal
     */
    protected function networkrequestlist()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();
            $networkquery = 'SELECT distinct na.network_id, na.request_network_id, na.status,m.email, n.name, c.nicename as country, n.cdate FROM network_access na' .
                    ' LEFT JOIN network n on n.id=na.request_network_id' .
                    ' LEFT JOIN members m on m.id=na.member_id' .
                    ' LEFT JOIN country c on c.id=n.country_id' .
                    " WHERE na.network_id='" . $this->request['n2n'] . "' and n.status='approved'";
            $networkresult = $this->Db->execute($networkquery);

            if ($networkresult !== false) {
                if ($this->Db->count() > 0) {
                    foreach ($networkresult as $key => $row) {
                        $networkresult[$key]['logo'] = File::getFiles('network', $row['network_id']);
                    }
                    return \json_encode(['networks' => $networkresult,
                        'status' => 'success',
                        'response' => 'Networks loaded successfully ', ]);
                }
            } else {
                throw new Exception('Database error when retrieving networks ' . $this->Db->getError());
            }
        } else {
            throw new Exception('Only accepts GET requests');
        }
    }

    /**
     * @internal
     */
    protected function memberactivationlist()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();
            $networks[] = $this->request['n2n'];
            $memberquery = 'SELECT m.id,m.email,m.fname,m.lname,n.name as network,mad.activation_code FROM members m
								JOIN members_activation_data mad on mad.member_id=m.id
								JOIN network n on n.id=mad.network_id
								WHERE n.id in (' . \implode(',', $networks) . ") and email not in (
								select members.email
								from members
								inner join network on find_in_set(SUBSTRING(members.email, LOCATE('@', members.email) + 1),network.domain)
								)";
            $memberresult = $this->Db->execute($memberquery);

            if ($memberresult !== false) {
                if ($this->Db->count() > 0) {
                    return \json_encode(['members' => $memberresult,
                        'status' => 'success',
                        'response' => 'Members loaded successfully', ]);
                }

                throw new Exception('Wrong network count for query');
            }

            throw new Exception('Database error when retrieving members ');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function networkinvites()
    {
        if ($this->method == 'GET') {
            if (isset($this->args[0]) && \is_numeric($this->args[0])) {
                $user = User::getuser();
                $networkquery = "SELECT ni.network_id,ni.invite_id,ni.email,ni.idate, ni.signedup FROM network_invites ni
								JOIN network_members nm on ni.network_id=nm.network_id
								JOIN members m on m.id=nm.member_id
								WHERE m.id='" . $user['id'] . "' and ni.network_id='" . $this->args[0] . "'";
                $networkresult = $this->Db->execute($networkquery);

                if ($networkresult !== false) {
                    if ($this->Db->count() > 0) {
                        return \json_encode(['networkinvites' => $networkresult,
                            'status' => 'success',
                            'response' => 'Network loaded successfully', ]);
                    }

                    throw new Exception('No invites found');
                }

                throw new Exception('Database error when retrieving network invites ');
            }

            throw new Exception('Network ID required');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function sendinvites()
    {
        if ($this->method == 'POST') {
            if (isset($this->args[0]) && \is_numeric($this->args[0])) {
                $success = $fail = 0;

                $emails = \explode(',', $this->request['emails']);

                foreach ($emails as $key => $email) {
                    if ($email !== '') {
                        $current_time = \date('Y-m-d H:i:s');
                        $invite_id = \md5(\time() . \mt_rand(100, 999999) . \time());
                        $invitequery = "INSERT INTO network_invites(network_id,invite_id,email,idate) VALUES ('" . $this->args[0] . "','" . $invite_id . "','" . $email . "','" . $current_time . "')";
                        $inviteresult = $this->Db->execute($invitequery);

                        if ($inviteresult !== false) {
                            $to = $email;
                            $from = GUIDEDOC_EMAIL;
                            $subject = $this->request['invitationsubject'];
                            $message = $this->request['invitationtext'];
                            $message .= "<br /><br /><a href='" . BASE_URL . "/frontend/register.php?nid={$this->args[0]}&uid={$invite_id}'>Click Here</a>.<br /><br /><br /><br />";
                            $result = $this->_sendMail($to, GUIDEDOC_EMAIL, $subject, $message);

                            if ($decoded = \json_decode($result, true)) {
                                if (\strpos($decoded['message'], 'Queued') !== false) {
                                    ++$success;
                                } else {
                                    ++$fail;
                                }
                            } else {
                                ++$fail;
                            }
                        } else {
                            ++$fail;
                        }
                    } else {
                        ++$fail;
                    }
                }

                return \json_encode(['status' => 'success',
                    'response' => $success . ' network invites sent successfully, ' . $fail . ' network invites failed', ]);
            }

            throw new Exception('Network ID required');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function validateinvite()
    {
        if ($this->method == 'POST') {
            if (isset($this->request['nid'], $this->request['uid'])) {
                $invitequery = "SELECT network_id,invite_id FROM network_invites WHERE network_id = '" . $this->request['nid'] . "' AND invite_id = '" . $this->request['uid'] . "'";
                $inviteresult = $this->Db->execute($invitequery);

                if ($inviteresult !== false) {
                    if ($this->Db->count() == 1) {
                        return \json_encode(['nid' => $inviteresult[0]['network_id'],
                            'uid' => $inviteresult[0]['invite_id'],
                            'status' => 'success',
                            'response' => 'Network loaded successfully', ]);
                    }

                    throw new Exception('Wrong network count for query');
                }

                throw new Exception('Database error when retrieving invite ');
            }

            throw new Exception('Network and Invite ID required');
        }

        throw new Exception('Only accepts POST requests');
    }

    /**
     * @OA\Get(
     *     path="/rest_v2/network/networkjoin",
     *     tags={"Join network"},
     *     summary="Join specific network",
     *     @OA\Parameter(
     *         name="nid",
     *         in="query",
     *         description="Network id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success - Network request successfully sent"
     *     ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    protected function networkjoin()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();

            if (isset($this->request['nid']) && \is_numeric($this->request['nid'])) {
                $checkrequestquery = "SELECT network_type from network where id='{$this->request['nid']}'";
                $checkrequestresult = $this->Db->execute($checkrequestquery);

                if ($checkrequestresult !== false) {
                    switch ($checkrequestresult[0]['network_type']) {
                            case 'public':
                                $memberstatus = 'active';
                                break;
                            case 'private':
                                $memberstatus = 'pending';
                                break;
                            default:
                                throw new Exception('It is not possible to join that network');
                        }
                } else {
                    throw new Exception('Could not identify network');
                }

                if (!isset($this->request['n2n'])) {
                    $networkquery = "INSERT INTO network_members(network_id,member_id,status)  VALUES  ('" . $this->request['nid'] . "','" . $user['id'] . "','" . $memberstatus . "')";
                } else {
                    $networkquery = "INSERT INTO network_access(network_id,request_network_id,member_id,status)  VALUES  ('" . $this->request['nid'] . "','" . $this->request['n2n'] . "','" . $user['id'] . "','" . $memberstatus . "')";
                }
                $networkresult = $this->Db->execute($networkquery);

                if ($networkresult !== false) {
                    return \json_encode([
                        'status' => 'success',
                        'response' => 'Network request successfully sent', ]);
                }

                throw new Exception('Error joining network');
            }

            throw new Exception('Network ID required');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @OA\Get(
     *     path="/rest_v2/network/networkleave",
     *     tags={"Leave network"},
     *     summary="Leave specific network",
     *     @OA\Parameter(
     *         name="nid",
     *         in="query",
     *         description="Network id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success - Network successfully removed"
     *     ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    protected function networkleave()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();

            if (isset($this->request['nid']) && \is_numeric($this->request['nid'])) {
                if (!isset($this->request['n2n'])) {
                    $networkquery = "delete from network_members where network_id='".$this->request['nid'] . "' and member_id='" . $user['id'] . "'";
                }
                else{
                    $networkquery = "delete na from network_access na join members m on m.id=na.member_id where na.network_id='" . $this->request['nid'] . "' and m.id='" . $user['id'] . "'";
                }
                $networkresult = $this->Db->execute($networkquery);

                if ($networkresult !== false) {
                    return \json_encode([
                        'status' => 'success',
                        'response' => 'Network successfully removed', ]);
                }

                throw new Exception('Error leaving network');
            }

            throw new Exception('Network ID required');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function updatenetworkaccess()
    {
        if ($this->method == 'GET') {
            $user = User::getuser();

            if ($this->request['status'] == 'approve') {
                $userquery = "update network_access na set status='active' where na.request_network_id='" . $this->request['n2n'] . "' and na.network_id='" . $this->request['nid'] . "'";
                $userresult = $this->Db->execute($userquery);
            } elseif ($this->request['status'] == 'freeze') {
                $userquery = "update network_access na set status='freeze' where na.request_network_id='" . $this->request['n2n'] . "' and na.network_id='" . $this->request['nid'] . "'";
                $userresult = $this->Db->execute($userquery);
            } elseif ($this->request['status'] == 'remove') {
                $userquery = "delete na from network_access na join members m on m.id=na.member_id where na.request_network_id='" . $this->request['n2n'] . "' and na.network_id='" . $this->request['nid'] . "'";
                $userresult = $this->Db->execute($userquery);
            }

            if ($this->Db->affectedRows() == 1) {
                return \json_encode(['status' => 'success',
                    'response' => 'Network status updated', ]);
            }

            throw new Exception('Database error when updating network status');
        }

        throw new Exception('Only accepts GET requests');
    }
}

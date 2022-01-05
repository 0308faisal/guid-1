<?php
 /**
 * @OA\SecurityScheme(
 *      type="http",
 *      description="Use Auth to get JWT Token",
 *      name="Authorization",
 *      in="header",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      securityScheme="bearerAuth",    
 * )
 * */
declare(strict_types=1);
/**
 * User API
 *
 * @endpoint /user
 */
class User extends API
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
     *     path="/rest_v2/user/getuser",
     *     tags={"Get user request"},
	 * @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Token"
     *     ),
     *     security={{"bearerAuth":{}}}
	 * )
	 * 
	 */
     
    

    public static function getuser($identifier = null)
    {
        $Db = $GLOBALS['Db'];
        $iplogin = false;

        if (empty($identifier)) {
            $data = Auth::validatetoken();
            $identifier = $data->user;
            $iplogin = $data->iplogin;
        }

        if (\is_numeric($identifier)) {
            $subquery = "m.id= '" . $identifier . "'";
        } else {
            $subquery = "m.email= '" . $identifier . "'";
        }
        $userquery = 'SELECT m.id, m.email, m.fname, m.lname, m.linkd_photo_url, moo.id as occupation_id, moo.name as occupation, mso.name as speciality,m.organization as employer,mngo.name as grade_name,m.medical_reg_no,m.activated,m.admin,m.profileaccess
						FROM members m
						LEFT JOIN members_occupation_options moo on moo.id=m.occupation_id
						LEFT JOIN members_speciality_options mso on mso.id=m.speciality_id
						LEFT JOIN members_name_grade_options mngo on mngo.id=m.name_grade_id
						WHERE ' . $subquery;
        $userresult = $Db->execute($userquery);

        if ($userresult !== false && $Db->count() == 1) {
            return [
                'id' => $userresult[0]['id'],
                'admin' => $userresult[0]['admin'],
                'user' => $userresult[0]['id'],
                'email' => $userresult[0]['email'],
                'firstname' => $userresult[0]['fname'],
                'lastname' => $userresult[0]['lname'],
                'occupation_id' => $userresult[0]['occupation_id'],
                'occupation' => $userresult[0]['occupation'],
                'specialty' => $userresult[0]['speciality'],
                'employer' => $userresult[0]['employer'],
                'gradename' => $userresult[0]['grade_name'],
                'medicalreg' => $userresult[0]['medical_reg_no'],
                'networks' => Network::getNetworks($userresult[0]['id']),
                'avatar' => $userresult[0]['linkd_photo_url'],
                'activated' => $userresult[0]['activated'],
                'iplogin' => $iplogin,
                'profileaccess' => $userresult[0]['profileaccess']
            ];
        }
        $error = new ErrorLogs();
        $error->apiLogs('error', 'getuser - '.$this->Db->getError());
        return false;
    }

    protected function deleteuser()
    {
        if ($this->method == 'POST') {
            $memberquery = "delete from m, mad using members m inner join members_activation_data mad on mad.member_id=m.id where m.email = '" . $this->request['email'] . "'";
            $memberresult = $this->Db->execute($memberquery);

            if ($memberresult !== false) {
                return \json_encode([
                    'status' => 'success',
                    'response' => 'Member deleted successfully',
                ]);
            }
            $error = new ErrorLogs();
            $error->apiLogs('error', 'deleteuser - '.$this->Db->getError());
        }
    }

    /**
     * @internal
     */
    protected function userlist()
    {
        if ($this->method == 'GET') {
            $networkquery = '';

            if (isset($this->args[0]) && \is_numeric($this->args[0])) {
                $query = " JOIN network_members nm on nm.member_id = m.id WHERE nm.network_id='" . $this->args[0] . "'";
            } else {
                $networkquery = ", (select group_concat(CONCAT(n.name, '-' , UCASE(nm.status), '-' , n.id)) from network n join network_members nm on nm.network_id=n.id where nm.member_id=m.id) as network";
            }

            $memberquery = 'SELECT m.id,m.email,m.fname,m.lname' . $networkquery . ' FROM members m';

            if (!empty($query)) {
                $memberquery .= $query;
            }
            $memberresult = $this->Db->execute($memberquery);

            if ($memberresult !== false) {
                if ($this->Db->count() > 0) {
                    return \json_encode([
                        'members' => $memberresult,
                        'status' => 'success',
                        'response' => 'Members loaded successfully',
                    ]);
                }

                throw new Exception('Wrong network count for query');
            }
            $error = new ErrorLogs();
            $error->apiLogs('error', 'userlist - '.$this->Db->getError());
            throw new Exception('Database error when retrieving members');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function approvallist()
    {
        if ($this->method == 'GET') {
            $user = self::getuser();
            $networks[] = $this->request['n2n'];
            $memberquery = 'SELECT m.id,m.email,m.fname,m.lname,n.name as network,nm.status,nm.manager FROM members m
				inner join network_members nm on nm.member_id=m.id
				inner join network n on n.id=nm.network_id
				WHERE n.id in (' . \implode(',', $networks) . ')
				ORDER by nm.status desc';
            $memberresult = $this->Db->execute($memberquery);

            if ($memberresult !== false) {
                if ($this->Db->count() > 0) {
                    return \json_encode([
                        'members' => $memberresult,
                        'status' => 'success',
                        'response' => 'Members loaded successfully',
                    ]);
                }

                throw new Exception('Wrong network count for query');
            }
            $error = new ErrorLogs();
            $error->apiLogs('error', 'approvallist - '.$this->Db->getError());
            throw new Exception('Database error when retrieving members');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function updatesignup()
    {
        if ($this->method == 'GET') {
            $user = self::getuser();
            $activationquery = "SELECT member_id,network_id,activation_code,m.email,m.fname,n.name,n.domain,n.approve FROM members_activation_data mad LEFT JOIN members m on m.id=mad.member_id LEFT JOIN network n on n.id=mad.network_id WHERE mad.member_id = '" . $this->request['id'] . "' AND activation_code = '" . $this->request['activation_code'] . "'";
            $activationresult = $this->Db->execute($activationquery);

            if ($activationresult !== false) {
                if ($this->Db->count() == 1) {
                    if ($this->request['status'] == 'activate') {
                        $this->request['nid'] = $activationresult[0]['network_id'];
                        $this->request['uid'] = $activationresult[0]['activation_code'];
                        $this->method = 'POST';
                        return $this->validateemail();
                    }

                    if ($this->request['status'] == 'resend') {
                        $this->generateUserActivationCode($activationresult[0]['member_id'], $activationresult[0]['email'], $activationresult[0]['fname'], $activationresult[0]['network_id'], true);
                    } elseif ($this->request['status'] == 'remove') {
                        $userquery = "DELETE m,mad,nm FROM members m
											JOIN members_activation_data mad on mad.member_id=m.id
											LEFT JOIN network_members nm on nm.member_id=m.id
											where m.id='" . $this->request['id'] . "' and mad.activation_code='" . $this->request['activation_code'] . "'";
                        $userresult = $this->Db->execute($userquery);
                    }
                    return \json_encode([
                        'status' => 'success',
                        'response' => 'Signup status updated',
                    ]);
                }
            } else {
                $error = new ErrorLogs();
                $error->apiLogs('error', 'updatesignup - '.$this->Db->getError());
                throw new Exception('Member signup not found');
            }
        } else {
            throw new Exception('Only accepts GET requests');
        }
    }

    /**
     * @OA\Get(
     *     path="/rest_v2/user/updatestatus",
     *     tags={"Update status"},
     *     summary="Update user network request status",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Member id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status includes network status to be and network id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success - User Updated and Notified"
     *     ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    protected function updatestatus()
    {
        if ($this->method == 'GET') {
            $manager = 0;
            $status = 'pending'; 
            $requeststatus = \explode('|', $this->request['status']);
            $status = $requeststatus[0];
            $networkid = $requeststatus[1];

            if ($status == 'manager') {
                $status = 'active';
                $manager = '1';
            }

            if (isset($status) && $status !== 'remove') {
                $userquery = "update network_members set manager='{$manager}',status='{$status}' where member_id='" . $this->request['id'] . "' and network_id='" . $networkid . "'";
            } elseif ($status == 'remove') {
                $userquery = "delete from network_members where member_id='" . $this->request['id'] . "' and network_id='" . $networkid . "'";
            }
            $userresult = $this->Db->execute($userquery);

            if ($userresult !== false) {
                if ($this->Db->affectedRows() == 1) {
                    if ($status !== 'remove' && $status !== 'banned') {
                        $user = self::getuser($this->request['id']);

                        foreach ($user['networks'] as $key => $network) {
                            if ($network['id'] == $networkid) {
                                $networkname = $network['name'];
                            }
                        }
                        $to = $user['email'];
                        $from = GUIDEDOC_EMAIL;
                        $subject = "Access Granted for {$networkname}";
                        $message = "Congratulations, your access request for {$networkname} has been granted.<br />You can now view the guidelines from this network <a href='" . BASE_URL . "frontend/index.php'>here</a>.";
                        $result = $this->_sendMail($to, GUIDEDOC_EMAIL, $subject, $message);

                        if ($decoded = \json_decode($result, true)) {
                            if (\strpos($decoded['message'], 'Queued') !== false) {
                                return \json_encode([
                                    'status' => 'success',
                                    'response' => 'User Updated and Notified',
                                ]);
                            }

                            throw new Exception('Error notifying user');
                        }

                        throw new Exception('Error notifying user');
                    }

                    return \json_encode([
                        'status' => 'success',
                        'response' => 'User status has been updated',
                    ]);
                }

                throw new Exception('User not updated ');
            }
            $error = new ErrorLogs();
            $error->apiLogs('error', 'updatestatus api - '.$this->Db->getError());
            throw new Exception('Database error when retrieving user');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function validatepassword()
    {
        if ($this->method == 'POST') {
            if (isset($this->request['passwordtoken']) && $this->request['passwordtoken'] !== '') {
                $passwordquery = "SELECT id,passwordtokencreated FROM members WHERE passwordtoken='" . $this->request['passwordtoken'] . "'";
                $passwordresult = $this->Db->execute($passwordquery);

                if ($passwordresult !== false) {
                    if ($this->Db->count() == 1) {
                        $id = $passwordresult[0]['id'];

                        if (($this->_dateDifference(\date('Y-m-d H:i:s'), \date('Y-m-d H:i:s', \strtotime($passwordresult[0]['passwordtokencreated'])), '%h') <= 1)) {
                            return \json_encode([
                                'id' => $id,
                                'status' => 'success',
                                'response' => 'Password change request approved',
                            ]);
                        }

                        throw new Exception('Password change request expired.');
                    }

                    throw new Exception('Password change request expired.');
                }
                $error = new ErrorLogs();
                $error->apiLogs('error', 'validatepassword - '.$this->Db->getError());
                throw new Exception('Database error when retrieving password change request ');
            }

            throw new Exception('Token required');
        }

        throw new Exception('Only accepts POST requests');
    }

/**
     * @OA\Post(
     *     path="/rest_v2/user/register",
     *     tags={"Register"},
     *     summary="Register user into system",
	 * 		@OA\RequestBody(
	*          @OA\MediaType(
	*              mediaType="multipart/form-data",
	*              @OA\Schema(
	*                  @OA\Property(
	*                      property="firstname",
	*                      type="string",
	*                  ),
	*                  @OA\Property(
	*                      property="lastname",
	*                      type="string"
	*                  ),
    *                   @OA\Property(
	*                      property="email",
	*                      type="string"
	*                  ),
    *                   @OA\Property(
	*                      property="password",
	*                      type="string"
	*                  ),
    *                   @OA\Property(
	*                      property="occupation",
	*                      type="string"
	*                  ),
    *                   @OA\Property(
	*                      property="grade",
	*                      type="string"
	*                  ),
    *                   @OA\Property(
	*                      property="network",
	*                      type="string"
	*                  ),
	*              )
	*          )
	*      ),
     *     @OA\Response(
     *         response=200,
     *         description="Success - User registered successfully, please check your email"
     *     )
	 * )
	 * 
     *
	*/
    protected function register()
    {
        if ($this->method == 'POST') {
            if (isset($this->request['g-recaptcha-response'])) {
                $requestip = \explode('|', $this->_getip());
                $result = $this->makeRequest('https://www.google.com/recaptcha/api/siteverify?secret=' . HOSTS[$_SERVER['SERVER_NAME']]['captcha_secret'] . '&response=' . $this->request['g-recaptcha-response'] . '&remoteip=' . $requestip[0], 'POST');

                if ($result->success !== true) {
                    throw new Exception('Invalid Captcha');
                }
            }

            $user = $this->getuser($this->request['email']);

            if ($user !== false) {
                throw new Exception('User already registered');
            }

            if (
                isset($this->request['email']) && $this->request['email'] !== '' &&
                isset($this->request['password']) && $this->request['password'] !== ''
            ) {
                $email = $this->request['email'];
                $firstname = $this->request['firstname'];
                $lastname = $this->request['lastname'];
                $country = '0'; //$this->request['country'];
                $password = $this->request['password'];
                $occupation = (isset($this->request['occupation']) && \is_numeric($this->request['occupation'])) ? $this->request['occupation'] : 0;
                $speciality = '0'; //$this->request['speciality'];
                $organisation = ''; //$this->request['organisation'];
                $grade = (isset($this->request['grade']) && \is_numeric($this->request['grade'])) ? $this->request['grade'] : 0;
                $network = (isset($this->request['network']) && \is_numeric($this->request['network'])) ? $this->request['network'] : 0;
                $practitioner = isset($_POST['practitioner']) ? 1 : 0;
                $today = \date('Y-m-d H:i:s');
                $ip_address = $this->_getip();
                $password = password_hash($password, PASSWORD_DEFAULT);
                //$password = $this->memberPasswordEncrypt($password);
                $activated = 0;

                if (isset($this->request['nid']) && $this->request['nid'] !== '' && isset($this->request['uid']) && $this->request['uid'] !== '' && $this->validateinvite() == true) {
                    $activated = 1;
                }
                $registrationquery = "INSERT INTO members(fname,lname,email,password,country_id,occupation_id,speciality_id,organization,name_grade_id,practitioner,created,registration_ip,activated) VALUES (
                                       '{$firstname}','{$lastname}','{$email}','{$password}','{$country}',
									   '{$occupation}','{$speciality}','{$organisation}','{$grade}','{$practitioner}','{$today}','{$ip_address}','{$activated}')";

                $registrationresult = $this->Db->execute($registrationquery);

                if ($registrationresult !== false) {
                    $new_member_id = $this->Db->lastInsertID();

                    if ($new_member_id > 0 && $activated == 0) {
                        $this->generateuseractivationcode($new_member_id, $email, $firstname, $network);

                        if (isset($this->request['nid']) && $this->request['nid'] !== '' && isset($this->request['uid']) && $this->request['uid'] !== '' && $this->validateinvite() == true) {
                            $invitequery = "INSERT INTO network_members(network_id,member_id)  VALUES  ('" . $this->request['nid'] . "','" . $new_member_id . "')";
                            $inviteresult = $this->Db->execute($invitequery);

                            if ($inviteresult !== false) {
                                $invitequery = "UPDATE network_invites set signedup=1 where network_id='" . $this->request['nid'] . "' and invite_id='" . $this->request['uid'] . "'";
                                $inviteresult = $this->Db->execute($invitequery);
                            } else {
                                throw new Exception('Error joining network');
                            }
                        }

                        return \json_encode([
                            'userid' => $new_member_id,
                            'status' => 'success',
                            'redirect' => isset($this->request['community']) && $this->request['community'] == 'true' ? "/network_create.php?uid=${new_member_id}" : '',
                            'response' => 'User registered successfully, please check your email',
                        ]);
                    }

                    if ($new_member_id > 0 && $activated == 1) {
                        return \json_encode([
                            'userid' => $new_member_id,
                            'status' => 'success',
                            'redirect' => isset($this->request['community']) && $this->request['community'] == 'true' ? "/network_create.php?uid=${new_member_id}" : '',
                            'response' => 'User registered successfully, please sign in',
                        ]);
                    }

                    throw new Exception('Error registering user');
                }
                $error = new ErrorLogs();
                $error->apiLogs('error', 'register api - '.$this->Db->getError());
                throw new Exception('Database error when registering user');
            }

            throw new Exception('Incomplete user details');
        }

        throw new Exception('Only accepts POST requests');
    }

    /**
     * @OA\Get(
     *     path="/rest_v2/user/resetpassword",
     *     tags={"Reset Password"},
     *     summary="reset user password",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email for login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success - Please check your email"
     *     )
     * )
     */
     
    protected function resetpassword()
    {
        if ($this->method == 'GET') {
            if (isset($this->request['email']) && $this->request['email'] !== '') {
                $passwordtoken = \md5(\time() . \mt_rand(10, 99999) . \time());
                $time = \date('Y-m-d H:i:s', \time());

                $email = $this->request['email'];
                $tokenquery = "UPDATE members set passwordtoken='${passwordtoken}', passwordtokencreated='${time}' where email='${email}'";
                $tokenresult = $this->Db->execute($tokenquery);

                if ($tokenresult !== false) {
                    $to = $email;
                    $from = GUIDEDOC_EMAIL;
                    $headers = 'From: ' . \strip_tags($from) . "\r\n";
                    $headers .= 'Reply-To: ' . \strip_tags($from) . "\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $subject = 'GuideDoc - Password change request';

                    $message = 'Hi,';
                    $message .= '<br /><br />We received a request to reset your password<br /><br />';
                    $message .= "<br /><br />Please <a href='" . BASE_URL . "frontend/changepassword.php?passwordtoken=${passwordtoken}'>click here</a> to change your password<br /><br /><br /><br />";
                    $message .= 'GuideDoc Team';

                    $result = $this->_sendMail($to, GUIDEDOC_EMAIL, $subject, $message);
                    //mail($to, $subject, $message, $headers);
                    if ($decoded = \json_decode($result, true)) {
                        if (\strpos($decoded['message'], 'Queued') !== false) {
                            return \json_encode([
                                'status' => 'success',
                                'response' => 'Please check your email.',
                            ]);
                        }

                        throw new Exception('Error notifying user');
                    }

                    throw new Exception('Error notifying user');
                }
                $error = new ErrorLogs();
                $error->apiLogs('error', 'resetpassword - '.$this->Db->getError());
                throw new Exception('Database error when creating activation code');
            }

            throw new Exception('Email required');
        }

        throw new Exception('Only accepts GET requests');
    }

    /**
     * @internal
     */
    protected function validateemail()
    {
        if ($this->method == 'POST') {
            if (isset($this->request['nid'], $this->request['uid'])) {
                $activationquery = "SELECT member_id,network_id,activation_code,m.email,n.name,n.domain,n.approve FROM members_activation_data mad LEFT JOIN members m on m.id=mad.member_id LEFT JOIN network n on n.id=mad.network_id WHERE network_id = '" . $this->request['nid'] . "' AND activation_code = '" . $this->request['uid'] . "'";
                $activationresult = $this->Db->execute($activationquery);

                if ($activationresult !== false) {
                    if ($this->Db->count() == 1) {
                        $updatequery = "UPDATE members m join members_activation_data mad on m.id=mad.member_id set activated = '1' where mad.activation_code='" . $activationresult[0]['activation_code'] . "' and mad.network_id='" . $activationresult[0]['network_id'] . "'";
                        $updateresult = $this->Db->execute($updatequery);

                        if ($updateresult !== false) {
                            if ($this->Db->affectedRows() == 1) {
                                $activated = 'pending';

                                if ($activationresult[0]['network_id'] !== 0) {
                                    $tmpemail = \explode('@', $activationresult[0]['email']);
                                    $domains = \explode(',', \str_replace(' ', '', $activationresult[0]['domain']));

                                    if (\in_array($tmpemail[1], $domains, true) && $activationresult[0]['approve'] == '1') {
                                        $activated = 'active';
                                    } else {
                                        $activated = 'pending';
                                    }
                                    $invitequery = "INSERT INTO network_members(network_id,member_id,status)  VALUES  ('" . $activationresult[0]['network_id'] . "','" . $activationresult[0]['member_id'] . "','" . $activated . "')";
                                    $inviteresult = $this->Db->execute($invitequery);
                                    $deletequery = "DELETE FROM members_activation_data WHERE network_id = '" . $activationresult[0]['network_id'] . "' AND activation_code = '" . $activationresult[0]['activation_code'] . "'";
                                    $deleteresult = $this->Db->execute($deletequery);
                                }
                                return \json_encode([
                                    'user' => $activationresult[0]['member_id'],
                                    'status' => $activated == 'pending' ? 'pending' : 'success',
                                    'nid' => $activated == 'pending' ? $this->request['nid'] : '',
                                    'response' => 'Activation successful, please login.',
                                ]);
                            }

                            throw new Exception('There was a problem activating your account.');
                        }

                        throw new Exception('There was a problem activating your account.');
                    }

                    throw new Exception('Activation link is no longer valid');
                }
                $error = new ErrorLogs();
                $error->apiLogs('error', 'validateemail - '.$this->Db->getError());
                throw new Exception('Database error when retrieving invite ');
            }

            throw new Exception('Network and Invite ID required');
        }

        throw new Exception('Only accepts POST requests');
    }

    /**
     * @internal
     */
    protected function changepassword()
    {
        if ($this->method == 'POST') {
            if (isset($this->request['passwordtoken']) && $this->request['passwordtoken'] !== '' && isset($this->request['uid']) && $this->request['uid'] !== '' && isset($this->request['password']) && $this->request['password'] !== '') {
                $passwordquery = "SELECT id,passwordtokencreated FROM members WHERE passwordtoken='" . $this->request['passwordtoken'] . "'";
                $passwordresult = $this->Db->execute($passwordquery);

                if ($passwordresult !== false) {
                    if ($this->Db->count() == 1) {
                        $id = $passwordresult[0]['id'];

                        if (($this->_dateDifference(\date('Y-m-d H:i:s'), \date('Y-m-d H:i:s', \strtotime($passwordresult[0]['passwordtokencreated'])), '%h') <= 1)) {
                            //$password = password_hash($this->request['password'], PASSWORD_DEFAULT);
                            $password = $this->memberPasswordEncrypt($this->request['password']);
                            $passwordchangequery = "UPDATE members set password='" . $password . "',passwordtoken='' WHERE id='" . $id . "'";
                            $passwordchangeresult = $this->Db->execute($passwordchangequery);

                            if ($passwordchangeresult !== false) {
                                return \json_encode([
                                    'id' => $id,
                                    'status' => 'success',
                                    'response' => 'Password change successful',
                                ]);
                            }

                            throw new Exception('Unable to change password.' . $this->Db->getError());
                        }

                        throw new Exception('Password change request expired.');
                    }

                    throw new Exception('Password change request not found');
                }
                $error = new ErrorLogs();
                $error->apiLogs('error', 'changepassword - '.$this->Db->getError());
                throw new Exception('Database error when retrieving password change request ');
            }

            throw new Exception('Token required');
        }

        throw new Exception('Only accepts POST requests');
    }

    /**
     * @internal
     */
    protected function memberprofile()
    {
        if ($this->method == 'POST') {
            $userid = $this->request['userid'];
            $firstname = $this->request['firstname'];
            $lastname = $this->request['lastname'];
            $occupation = $this->request['occupation'] == '' ? 0 : $this->request['occupation'];
            $speciality = $this->request['speciality'] == '' ? 0 : $this->request['speciality'];
            $employer = $this->request['employer'];
            $gradename = $this->request['gradename'] == '' ? 0 : $this->request['gradename'];
            $country = $this->request['country'] == '' ? 0 : $this->request['country'];
            $profilequery = "UPDATE members set fname='{$firstname}', lname='{$lastname}',occupation_id='{$occupation}',speciality_id='{$speciality}',organization='{$employer}',name_grade_id='{$gradename}',country_id='{$country}' where id='{$userid}'";
            $profileresult = $this->Db->execute($profilequery);

            if ($profileresult == false) {
                $error = new ErrorLogs();
                $error->apiLogs('error', 'memberprofile - '.$this->Db->getError());
                throw new Exception('Error updating profile');
            }

            return \json_encode([
                'status' => 'success',
                'response' => 'Profile saved successfully',
            ]);
        }

        throw new Exception('Only accepts POST requests');
    }

    /**
     * @internal
     */
    protected function memberpassword()
    {
        if ($this->method == 'POST') {
            $userid = $this->request['userid'];
            $oldpassword = $this->request['oldpassword'];
            $newpassword = $this->request['newpassword'];
            $userquery = "SELECT id, password FROM members WHERE id = '{$userid}'";
            $result = $this->Db->execute($userquery);

            if ($result !== false) {
                if ($this->Db->count() == 1) {
                    $db_password = $result[0]['password'];
                    //if(password_verify($oldpassword, $db_password))
                    if ($this->memberPasswordDecrypt($db_password, $oldpassword)) {
                        //$password = password_hash($password, PASSWORD_DEFAULT);
                        $password = $this->memberPasswordEncrypt($newpassword);
                        $passwordquery = "UPDATE members set password='{$password}' where id='{$userid}'";
                        $passwordresult = $this->Db->execute($passwordquery);

                        if ($passwordresult == false) {
                            throw new Exception('Error updating password - ' . $passwordquery);
                        }

                        return \json_encode([
                            'status' => 'success',
                            'response' => 'Password saved successfully',
                        ]);
                    }

                    throw new Exception('Old password is incorrect');
                }

                throw new Exception('Invalid user');
            }
            $error = new ErrorLogs();
            $error->apiLogs('error', 'memberpassword - '.$this->Db->getError());
            throw new Exception('Database error when retrieving user');
        }

        throw new Exception('Only accepts POST requests');
    }

    /**
     * @internal
     */
    protected function generateuseractivationcode($member_id, $email, $first_name, $network_id = 0, $resend = false)
    {
        $activation_code = \md5(\time() . \mt_rand(10, 99999) . \time());

        if ($resend == false) {
            $activationquery = "INSERT INTO members_activation_data(member_id,activation_code,network_id) VALUES ('{$member_id}','{$activation_code}','{$network_id}')";
            $activationresult = $this->Db->execute($activationquery);
        }

        if ($resend == true || $activationresult !== false) {
            $to = $email;
            $from = GUIDEDOC_EMAIL;
            $headers = 'From: ' . \strip_tags($from) . "\r\n";
            $headers .= 'Reply-To: ' . \strip_tags($from) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = 'GuideDoc - Account Verification';

            $message = 'Hi ' . $first_name . ',';
            $message .= '<br /><br />Thank you for registering with GuideDoc.<br /><br />';
            $message .= "<br /><br />Please <a href='" . BASE_URL . "/frontend/register.php?nid={$network_id}&uid={$activation_code}&activate=1'>click here</a> to validate the email address you have provided..<br /><br />";
            $message .= '<br /><br />You can also download our mobile app at the following links:<br /><br />';
            $message .= "<br /><a href='https://apps.apple.com/gb/app/guidedoc/id1136579212'>GuideDoc iPhone App</a><br />";
            $message .= "<br /><a href='https://play.google.com/store/apps/details?id=co.guidedoc.guidedocapp&hl=en_IE'>GuideDoc Android App</a><br />";
            $message .= '<br /><br />Thanks again,<br /><br />';
            $message .= 'GuideDoc Team';

            $result = $this->_sendMail($to, GUIDEDOC_EMAIL, $subject, $message);

            if ($decoded = \json_decode($result, true)) {
                if (\strpos($decoded['message'], 'Queued') !== false) {
                    return \json_encode([
                        'status' => 'success',
                        'response' => 'Account Created Successfuly, please check your email.',
                    ]);
                }

                throw new Exception('Error notifying user');
            }

            throw new Exception('Error notifying user');
        }
        $error = new ErrorLogs();
        $error->apiLogs('error', 'generateuseractivationcode - '.$this->Db->getError());
        throw new Exception('Database error when creating activation code');
    }
}

<?php
/**
 * @OA\Info(title="Guidedoc Api", version="1.0.0")
 */
abstract class API
{
	protected $apistatus = 200;
	protected $method = '';
	protected $endpoint = '';
	protected $verb = '';
	protected $args = Array();
	protected $file = Null;
	protected $config;
	protected $Db;

	public function __construct($request = null)
	{
		$this->Db=$GLOBALS['Db'];
		header("Access-Control-Allow-Orgin: *");
		header("Access-Control-Allow-Methods: *");
		header("Content-Type: application/json");
		$this->args = explode('/', rtrim($request, '/'));
		$this->endpoint = array_shift($this->args);
		if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
		{
			$this->verb = array_shift($this->args);
		}

		$this->method = $_SERVER['REQUEST_METHOD'];
		if($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
		{
			if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
			{
				$this->method = 'DELETE';
			}
			else if($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
			{
				$this->method = 'PUT';
			}
			else
			{
				throw new Exception("Unexpected Header");
			}
		}

		switch($this->method)
		{
			case 'DELETE':
			case 'POST':
				$this->request = $this->_cleanInputs($_POST);
				$this->file = $_FILES;
				break;
			case 'GET':
				$this->request = $this->_cleanInputs($_GET);
				break;
			case 'PUT':
				$this->request = $this->_cleanInputs($_GET);
				$this->file = file_get_contents("php://input");
				break;
			default:
				$this->_response('Invalid Method', 405);
				break;
		}
	}

	public function processAPI()
	{
		if((int) method_exists($this, $this->endpoint) > 0)
		{
			return $this->_response($this->{$this->endpoint}($this->args),$this->apistatus);
		}
		return $this->_response("No Endpoint:" . $this->endpoint, 404);
	}

	private function _response($data, $status = 200)
	{
		header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
		return json_encode($data);
	}

	/**
	 * @internal
	 */
	protected function memberPasswordEncrypt($decrypted)
	{
		$salt = sha1(md5("SALT"));
		$key = hash('SHA256', $salt . $decrypted, true);
		srand();
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		if(strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22)
		{
			return false;
		}
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
		return $iv_base64 . $encrypted;
	}
	/**
	 * @internal
	 */
	protected function memberPasswordDecrypt($encrypted, $password)
	{
		$salt = sha1(md5("SALT"));
		$key = hash('SHA256', $salt . $password, true);
		$iv = base64_decode(substr($encrypted, 0, 22) . '==');
		$encrypted = substr($encrypted, 22);
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
		$hash = substr($decrypted, -32);
		$decrypted = substr($decrypted, 0, -32);
		if(md5($decrypted) != $hash)
		{
			return false;
		}
		return $decrypted;
	}

	function makeRequest($data, $method)
	{

		if($method == "GET")
		{
			$url = $data;
		}
		else
		{
			$tmp = explode("?", $data);
			$url = $tmp[0];
			parse_str(urldecode($tmp[1]), $data);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if($method == "GET")
		{
			curl_setopt($ch, CURLOPT_POST, 0);
		}
		else
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}


	protected function _getip()
	{
		if(isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
		{
			$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$client = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}

		return $ip;
	}

	protected function _sendMail($to, $from, $subject, $message)
	{
		$mg_from_email = $from;
		$mg_reply_to_email = $from;
		$mg_message_url = MAILGUN_URL . '/messages';
		$mg_post_fields = array('from' => $mg_reply_to_email . ' <' . $mg_reply_to_email . '>',
			'to' => $to,
			'from' => $mg_reply_to_email . ' <' . $mg_reply_to_email . '>',
			'subject' => $subject,
			'html' => $message,
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $mg_message_url);

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		curl_setopt($ch, CURLOPT_USERPWD, 'api:' . MAILGUN_API);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $mg_post_fields);
		$result = curl_exec($ch);

		curl_close($ch);

		return $result;
	}

	private function _cleanInputs($data)
	{
		$clean_input = Array();
		if(is_array($data))
		{
			foreach($data as $k => $v)
			{
				$clean_input[$k] = $this->_cleanInputs($v);
			}
		}
		else
		{
			$clean_input = trim(addslashes($data));
		}
		return $clean_input;
	}

	/**
	 * @internal
	 */
	protected function _dateDifference($date_1, $date_2, $differenceFormat = '%a')
	{
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format($differenceFormat);
	}

	/**
	 * @internal
	 */
	protected function _findinarray($array, $field, $value)
	{
		foreach($array as $key => $item)
		{
			if($item[$field] == $value)
			{
				return $key;
			}
		}

		return false;
	}

	private function _requestStatus($code)
	{
		$status = array(
			200 => 'OK',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			500 => 'Internal Server Error',
		);
		return ($status[$code]) ? $status[$code] : $status[500];
	}

}

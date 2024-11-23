<?php

namespace Odin\Mailer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class Mailer
{
	protected $__subject = null;
	protected $__body = null;
	protected $__data = [];
	protected $__attachments = null;
	protected $addressData = [
		'from' => [],
		'to' => [],
		'cc' => [],
		'bcc' => [],
		'replyTo' => []
	];

	protected $__canSend__ = true;


	protected $config = [];


	protected static $mailConfig = [];

	protected static $__oneTimeData = [];

	protected $_message = null;

	/**
	 * thêm địa chỉ email
	 *
	 * @param string $type
	 * @param array|string $email
	 * @param string $name
	 * @return $this
	 */
	public function addAddress($type = 'to', $email = null, $name = null)
	{
		if ($email && array_key_exists($type, $this->addressData)) {
			if (is_array($email)) {
				foreach ($email as $key => $val) {
					if (is_numeric($key)) {
						if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
							$this->addressData[$type][$val] = 'Guest';
						}
					} else {
						if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
							$this->addressData[$type][$val] = $key;
						} elseif (filter_var($key, FILTER_VALIDATE_EMAIL)) {
							$this->addressData[$type][$key] = $val;
						}
					}
				}
			} else {
				if ($name) {
					$this->addressData[$type][$email] = $name;
				} else {
					$this->addressData[$type][] = $email;
				}
			}
		}
		return $this;
	}

	/**
	 * lay danh sach mail gửi
	 *
	 * @return Array<int,string>[]
	 */
	protected function getAddressToList()
	{
		$to = [];
		$email = $this->addressData['to'];
		if (is_array($email)) {
			foreach ($email as $key => $val) {
				if (is_numeric($key)) {
					if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
						$to[] = [$val, 'Guest'];
					}
				} else {
					if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
						// $to[] = $val;
						$to[] = [$val, 'Guest'];
					} elseif (filter_var($key, FILTER_VALIDATE_EMAIL)) {
						$to[] = [$key, $val];
					}
				}
			}
		} else {
			$to = [$email, 'Guest'];
		}
		return $to;
	}


	protected function sendByData($to, $subject = null, $body = 'template', $data = [])
	{
		try {
			return Mail::to($to['email'], $to['name'])->send(new MailTemplate(subject: $subject, data: $data, view: $body, addresses: $this->addressData));
		} catch (\Throwable $th) {
			//throw $th;
			$this->_message = $th->getMessage();
		}
		return false;
	}


	/**
	 * gửi mail
	 *
	 * @param string $body
	 * @param array $var
	 * @return void
	 */
	public function _sendMail($body = null, $vars = [])
	{
		if (!$this->__canSend__) return false;
		$this->__checkConfig();
		if (method_exists($this, 'beforeSend')) {
			$s = $this->beforeSend();
			if ($s === false) return false;
		}
		if (static::$__oneTimeData) {
			$vars = array_merge(static::$__oneTimeData, $vars);
			static::$__oneTimeData = [];
		}
		if (!$body) $body = $this->__body;


		$mails = $this->getAddressToList();
		$t = count($mails);
		if ($t == 0)
			return false;
		if ($t == 1)
			return $this->sendByData(['email' => $mails[0][0], 'name' => $mails[0][1]], $this->__subject, $body, $vars);
		$a = [];
		foreach ($mails as $i => $mail) {
			if ($r = $this->sendByData(['email' => $mail[0], 'name' => $mail[1]], $this->__subject, $body, $vars)) {
				$a[] = $r;
			}
		}
		return $a;

	}


	/**
	 * gửi mail
	 *
	 * @param string|array $to địa chỉ / thông tin người nhận
	 * @param string $subject Chủ đề
	 * @param string $body blade view
	 * @param array $data sữ liệu được dùng trong mail
	 * @param array $attachments file đính kèm
	 * @return bool
	 */
	protected function _send($to = null, $subject = null, $body = '', $data = [], $attachments = null)
	{
		if ($subject) {
			$this->__subject = $subject;
		}
		if (!$body) $body = $this->__body;
		$var = array_merge($this->__data, static::$__oneTimeData, $data);


		static::$__oneTimeData = [];
		if (is_string($to) && filter_var($to, FILTER_VALIDATE_EMAIL)) {
			$this->addAddress('to', $to);
		} elseif (is_array($to)) {
			foreach ($to as $key => $val) {
				if (is_numeric($key)) {
					if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
						$this->addAddress('to', $val);
					}
				} elseif (strtolower($key) == '@cc') { //neu co CC
					$this->addAddress('cc', $val);
				} elseif (strtolower($key) == '@bcc') { // neu co BCC
					$this->addAddress('bcc', $val);
				} else {
					if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
						$this->addAddress('to', $val, $key);
					} elseif (filter_var($key, FILTER_VALIDATE_EMAIL)) {
						$this->addAddress('to', $key, $val);
					}
				}
			}
		}
		$this->_sendMail($body, $var);
		return true;
	}

	protected function _subject($subject = null)
	{
		$this->__subject = $subject;
		return $this;
	}

	protected function _body($body = null)
	{
		$this->__body = $body;
		return $this;
	}

	protected function _data($data = null)
	{
		$this->__data = $data;
		return $this;
	}

	protected function _message($message = null)
	{
		$this->__data['message'] = $message;
		return $this;
	}

	protected function _attach($files = null)
	{
		if ($files) {
			if (is_array($files)) {
				foreach ($files as $i => $file) {
					if (is_file($file)) $this->__attachments[] = $file;
				}
			} elseif (is_file($files)) $this->__attachments[] = $files;
		}
		return $this;
	}

	protected function _queue(int $time = 1)
	{
		$this->__checkConfig();
		if (config('mail.queue.enabled') == 'OFF')
			return $this->send();
		if (is_numeric($time) && $time >= 0) {
			$body = view($this->__body, $this->__data)->render();
			$this->__data = ['body' => $body];
			$this->__body = 'mails.queue';
			$emailJob = (new Job($this))->delay(Carbon::now()->addMinutes($time));
			dispatch($emailJob);
			return true;
		} else {
			return false;
		}
	}

	protected function _sendAfter(int $time = 1)
	{
		// Config::set('mail', static::$config);
		return $this->_queue($time);
	}

	public function __call($method, $params)
	{
		if (array_key_exists($method, $this->addressData)) {
			return $this->addAddress($method, ...$params);
		} elseif (method_exists($this, '_' . $method)) {
			return call_user_func_array([$this, '_' . $method], $params);
		} else {
			if (preg_match('/^[A-z0-9_]+$/i', $method)) {
				$this->__data[$method] = $params[0] ?? '';
			}
		}
		return $this;
	}
	public static function __callStatic($method, $params)
	{
		$mail = new static();
		return call_user_func_array([$mail, $method], $params);
	}

}

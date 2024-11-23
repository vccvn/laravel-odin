<?php

namespace Odin\Mailer;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $_data;
    public $_view;
    public $_subject;
    public $_addresses = []; // Thêm thuộc tính để lưu danh sách email BCC

    public function __construct($subject = null, array $data = [], $view = 'template', $addresses = [])
    {
        $this->_subject = $subject;
        $this->_data = (array) $data;
        $this->_view = $view;
        if (is_array($addresses)) {
            $this->_addresses = $addresses;
        }
    }

    public function build()
    {
        $this->view('emails.' . $this->_view);
        if ($this->_data) {
            $this->with($this->_data);
        }

        if ($this->_subject) {
            $this->subject($this->_subject);
        }
        if (!is_array($this->_addresses))
            return $this;
        if (array_key_exists('cc', $this->_addresses) && ($cc = $this->_addresses['cc'])) {
            $this->addAddress('cc', $cc);
        }
        if (array_key_exists('bcc', $this->_addresses) && ($bcc = $this->_addresses['bcc'])) {
            $this->addAddress('bcc', $bcc);
        }
        if (array_key_exists('replyTo', $this->_addresses) && ($replyTo = $this->_addresses['replyTo'])) {
            $this->addAddress('replyTo', $replyTo);
        }
        
        return $this;
    }

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
        if ($email && in_array($t = strtolower($type), ['cc', 'bcc', 'replyto', 'from'])) {
            if (is_array($email)) {
                foreach ($email as $key => $val) {
                    if (is_numeric($key)) {
                        if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
                            $this->addSingleAddress($t, $val);
                        }
                    } else {
                        if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
                            $this->addSingleAddress($t, $val, $key);
                        } elseif (filter_var($key, FILTER_VALIDATE_EMAIL)) {
                            $this->{$t}($key, $val);
                            $this->addSingleAddress($t, $key, $val);
                        }
                    }
                }
            } else {
                if ($name) {
                    $this->addSingleAddress($t, $email, $name);
                } else {
                    $this->addSingleAddress($t, $email);
                }
            }
        }
        return $this;
    }

    protected function addSingleAddress(string $type = 'cc', string $email = 'example@example.com', string $name = null){
        $params = [$email];
        if($name) $params[] = $name;
        switch (strtolower($type)) {
            case 'replyto':
                $this->replyTo(...$params);
                break;
            
            default:
                call_user_func_array([$this, $type], $params);
                break;
        }
    }
}

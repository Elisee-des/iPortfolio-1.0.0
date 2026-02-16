<?php
/**
 * PHP Email Form - Simple Implementation
 * Based on BootstrapMade PHP Email Form
 */

class PHP_Email_Form {
    public $ajax = false;
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $smtp = array();
    private $messages = array();

    public function add_message($content, $label = '') {
        $this->messages[] = array('content' => $content, 'label' => $label);
    }

    public function send() {
        $body = '';
        foreach ($this->messages as $message) {
            $body .= $message['label'] . ': ' . $message['content'] . "\n\n";
        }

        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (!empty($this->smtp)) {
            // Use SMTP
            return $this->send_smtp($body, $headers);
        } else {
            // Use PHP mail function
            if (mail($this->to, $this->subject, $body, $headers)) {
                return 'OK';
            } else {
                return 'Failed to send email';
            }
        }
    }

    private function send_smtp($body, $headers) {
        $smtp_host = $this->smtp['host'];
        $smtp_port = $this->smtp['port'];
        $smtp_user = $this->smtp['username'];
        $smtp_pass = $this->smtp['password'];
        $encryption = isset($this->smtp['encryption']) ? $this->smtp['encryption'] : 'tls';

        $fp = fsockopen(($encryption == 'ssl' ? 'ssl://' : '') . $smtp_host, $smtp_port, $errno, $errstr, 30);
        if (!$fp) {
            return 'SMTP connection failed: ' . $errstr;
        }

        $this->smtp_put($fp, 'EHLO ' . $smtp_host);
        if ($encryption == 'tls') {
            $this->smtp_put($fp, 'STARTTLS');
            stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->smtp_put($fp, 'EHLO ' . $smtp_host);
        }

        $this->smtp_put($fp, 'AUTH LOGIN');
        $this->smtp_put($fp, base64_encode($smtp_user));
        $this->smtp_put($fp, base64_encode($smtp_pass));
        $this->smtp_put($fp, 'MAIL FROM: <' . $this->from_email . '>');
        $this->smtp_put($fp, 'RCPT TO: <' . $this->to . '>');
        $this->smtp_put($fp, 'DATA');
        $this->smtp_put($fp, 'Subject: ' . $this->subject . "\r\n" . $headers . "\r\n" . $body . "\r\n.");
        $this->smtp_put($fp, 'QUIT');
        fclose($fp);

        return 'OK';
    }

    private function smtp_put($fp, $command) {
        fwrite($fp, $command . "\r\n");
        $response = fgets($fp, 512);
        if (substr($response, 0, 1) != '2' && substr($response, 0, 1) != '3') {
            return false;
        }
        return true;
    }
}
?>
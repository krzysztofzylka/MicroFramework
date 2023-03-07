<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email\Extra;

use Krzysztofzylka\MicroFramework\Trait\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SendEmail
{

    use Log;

    private PHPMailer $PHPMailer;

    /**
     * Set PHPMailer
     * @param PHPMailer $PHPMailer
     * @return void
     */
    public function setPHPMailer(PHPMailer $PHPMailer): void
    {
        $this->PHPMailer = $PHPMailer;
    }

    /**
     * Get PHPMailer Handler
     * @return PHPMailer
     */
    public function getPHPMailerHandler(): PHPMailer
    {
        return $this->PHPMailer;
    }

    /**
     * Set from
     * @param string $email email
     * @param string $name name
     * @return void
     * @throws Exception
     */
    public function setFrom(string $email, string $name): void
    {
        $this->PHPMailer->setFrom($email, $name);
    }

    /**
     * Add address
     * @param string $email email
     * @param string $name optional name
     * @return void
     * @throws Exception
     */
    public function addAddress(string $email, string $name = ''): void
    {
        $this->PHPMailer->addAddress($email, $name);
    }

    /**
     * Add replay to
     * @param string $email email
     * @param string $name optional name
     * @return void
     * @throws Exception
     */
    public function addReplyTo(string $email, string $name = ''): void
    {
        $this->PHPMailer->addReplyTo($email, $name);
    }

    /**
     * Add CC
     * @param string $email email
     * @param string $name optional name
     * @return void
     * @throws Exception
     */
    public function addCC(string $email, string $name = ''): void
    {
        $this->PHPMailer->addCC($email, $name);
    }

    /**
     * Add BCC
     * @param string $email email
     * @param string $name optional name
     * @return void
     * @throws Exception
     */
    public function addBCC(string $email, string $name = ''): void
    {
        $this->PHPMailer->addBCC($email, $name);
    }

    /**
     * Add attachment
     * @param string $path path
     * @param string $name optional name
     * @return void
     * @throws Exception
     */
    public function addAttachment(string $path, string $name = ''): void
    {
        $this->PHPMailer->addAttachment($path, $name);
    }

    /**
     * Is HTML E-Mail
     * @param ?bool $isHtml
     * @return void
     */
    public function isHtml(?bool $isHtml = true): void
    {
        $this->PHPMailer->isHTML($isHtml);
    }

    /**
     * Send E-Mail
     * @param string $subject subject
     * @param string $body body
     * @return bool
     * @throws Exception
     */
    public function send(string $subject, string $body): bool
    {
        $this->PHPMailer->Subject = $subject;
        $this->PHPMailer->Body = $body;

        $send = $this->PHPMailer->send();
        $this->PHPMailer->smtpClose();

        if (!$send) {
            $this->log('Fail send e-mail', 'ERROR', ['errorInfo' => $this->getError()]);
        }

        return $send;
    }

    /**
     * Get error
     * @return string
     */
    public function getError(): string
    {
        return $this->PHPMailer->ErrorInfo;
    }

}
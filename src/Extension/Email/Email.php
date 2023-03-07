<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email;

use config\Config;
use Exception;
use Krzysztofzylka\MicroFramework\ConfigDefault;
use Krzysztofzylka\MicroFramework\Extension\Email\Extra\SendEmail;
use Krzysztofzylka\MicroFramework\Extension\Email\PredefinedConfig\Gmail;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Email
{

    use Log;

    /**
     * Create new e-mail
     * @return SendEmail|false
     */
    public function newEmail(): SendEmail|false
    {
        if (!Kernel::getConfig()->email) {
            return false;
        }

        try {
            $PHPMailer = new PHPMailer();
            $this->connect($PHPMailer);

            $sendMail = new SendEmail();
            $sendMail->setPHPMailer($PHPMailer);
            $sendMail->isHtml();

            return $sendMail;
        } catch (Exception $exception) {
            $this->log('Fail create new email', 'ERROR', ['exception' => $exception]);

            return false;
        }
    }

    /**
     * Connect to email
     * @param PHPMailer $phpMailer
     * @param ?object $config
     * @return void
     */
    private function connect(PHPMailer $phpMailer, ?object $config = null): void
    {
        /** @var Config $config */
        $config = $config ?? Kernel::getConfig();

        if (!is_null($config->emailPredefinedConfig)) {
            /** @var ConfigDefault $predefinedConfig */
            if ($config->emailPredefinedConfig === 'gmail') {
                $predefinedConfig = new Gmail();
            }
        }

        $phpMailer->From = $config->emailFrom[0];
        $phpMailer->FromName = $config->emailFrom[1];
        $phpMailer->Username = $config->emailUsername;
        $phpMailer->Password = $config->emailPassword;

        if ($config->debug) {
            $phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        if (($predefinedConfig->emailAuthType ?? $config->emailAuthType) === 'smtp') {
            $phpMailer->isSMTP();
            $phpMailer->SMTPAuth = $predefinedConfig->emailSMTPAuth ?? $config->emailSMTPAuth;
            $phpMailer->Mailer = 'smtp';
        }

        $phpMailer->Host = $predefinedConfig->emailHost ?? $config->emailHost;
        $phpMailer->SMTPSecure = $predefinedConfig->emailSMTPSecure ?? $config->emailSMTPSecure;
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->Port = $predefinedConfig->emailPort ?? $config->emailPort;

    }

}
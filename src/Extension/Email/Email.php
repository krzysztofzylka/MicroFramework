<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email;

use config\Config;
use Exception;
use Krzysztofzylka\MicroFramework\Extension\Email\Enum\PredefinedConfig;
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
            $predefinedConfig = match ($config->emailPredefinedConfig) {
                PredefinedConfig::Gmail => new Gmail()
            };
        }

        if ($config->debug && $config->emailDebug) {
            $phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        $phpMailer->From = $config->emailFrom ?? $config->emailUsername;
        $phpMailer->FromName = $config->emailFromName;
        $phpMailer->Username = $config->emailUsername;
        $phpMailer->Password = $config->emailPassword;

        if ($config->emailIsSMTP) {
            $phpMailer->Mailer = 'smtp';
            $phpMailer->isSMTP();
            $phpMailer->SMTPSecure = $predefinedConfig->emailSMTPSecure ?? $config->emailSMTPSecure;
            $phpMailer->SMTPAuth = $predefinedConfig->emailSMTPAuth ?? $config->emailSMTPAuth;
        }

        $phpMailer->Host = $predefinedConfig->emailHost ?? $config->emailHost;
        $phpMailer->CharSet = $predefinedConfig->emailCharset ?? $config->emailCharset;
        $phpMailer->Port = $predefinedConfig->emailPort ?? $config->emailPort;
    }

}
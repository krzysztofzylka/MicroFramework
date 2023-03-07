<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email;

use Exception;
use Krzysztofzylka\MicroFramework\Extension\Email\Extra\SendEmail;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{

    use Log;

    /**
     * Connect to email
     * @param PHPMailer $phpMailer
     * @param ?object $config
     * @return void
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function connect(PHPMailer $phpMailer, ?object $config = null): void
    {
        $config = $config ?? Kernel::getConfig();

        $phpMailer->isSMTP();
        $phpMailer->Host = $config->emailHost;
        $phpMailer->SMTPAuth = $config->emailSMTPAuth;
        $phpMailer->Username = $config->emailUsername;
        $phpMailer->Password = $config->emailPassword;

        if ($config->emailSMTPSecure) {
            $phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }

        $phpMailer->Port = $config->emailPort;

        $phpMailer->setFrom($config->emailFrom[0], $config->emailFrom[1]);
    }

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

            return $sendMail;
        } catch (Exception $exception) {
            $this->log('Fail create new email', 'ERROR', ['exception' => $exception]);

            return false;
        }
    }

}
<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email;

use config\Config;
use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Email\Enum\PredefinedConfig;
use Krzysztofzylka\MicroFramework\Extension\Email\Extra\SendEmail;
use Krzysztofzylka\MicroFramework\Extension\Email\PredefinedConfig\Gmail;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;
use Krzysztofzylka\MicroFramework\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


/**
 * Email extension
 * @package Extension\Email
 */
class Email
{

    use Log;

    /**
     * Send new e-mail
     * @param string $address
     * @param string $subject
     * @param string $content
     * @param string $layout
     * @param string $header
     * @param string $footer
     * @return bool
     * @throws ViewException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendEmail(
        string $address,
        string $subject,
        string $content,
        string $layout = 'default',
        string $header = '',
        string $footer = ''
    ): bool
    {
        $newEmail = $this->newEmail();
        $newEmail->addAddress($address);

        $layout = match ($layout) {
            'default' => 'MicroFramework/EmailLayout/default',
            default => $layout
        };

        $view = new View();
        $htmlContent = $view->render(
            [
                'content' => $content,
                'header' => $header,
                'footer' => $footer
            ],
            $layout
        );

        return $newEmail->send($subject, $htmlContent);
    }

    /**
     * Create new custom e-mail
     * @return SendEmail|false
     */
    public function newEmail(): SendEmail|false
    {
        if (!$_ENV['email.enabled']) {
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
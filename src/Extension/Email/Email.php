<?php

namespace Krzysztofzylka\MicroFramework\Extension\Email;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Email\Extra\SendEmail;
use Krzysztofzylka\MicroFramework\Extension\Email\PredefinedConfig\Gmail;
use Krzysztofzylka\MicroFramework\Trait\Log;
use Krzysztofzylka\MicroFramework\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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
        if (!$_ENV['email_enabled']) {
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
        $config = $config ?? $_ENV;

        if (!is_null($config['email_predefined_config'])) {
            $predefinedConfig = match ($config['email_predefined_config']) {
                'gmail' => new Gmail()
            };
        }

        if ($config['config_debug'] && $config['email_debug']) {
            $phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        $phpMailer->From = $config['email_from'] ?? $config['email_username'];
        $phpMailer->FromName = $config['email_from_name'];
        $phpMailer->Username = $config['email_username'];
        $phpMailer->Password = $config['email_password'];

        if ($config['email_is_smtp']) {
            $phpMailer->Mailer = 'smtp';
            $phpMailer->isSMTP();
            $phpMailer->SMTPSecure = $predefinedConfig->emailSMTPSecure ?? $config['email_smtp_secure'];
            $phpMailer->SMTPAuth = $predefinedConfig->emailSMTPAuth ?? $config['email_smtp_auth'];
        }

        $phpMailer->Host = $predefinedConfig->emailHost ?? $config['email_host'];
        $phpMailer->CharSet = $predefinedConfig->emailCharset ?? $config['email_charset'];
        $phpMailer->Port = $predefinedConfig->emailPort ?? $config['email_port'];
    }

}
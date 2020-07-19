<?php
/**
 * Project: ShoppingLists
 * File: SendWelcomeEmailService.php
 * Author: Ndifreke Ekott
 * Date: 18/07/2020 17:21
 *
 */

namespace App\Services;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class SendWelcomeEmailService
{
    private const EMAIL_TEMPLATE = 'emails/user/welcome_email.html.twig';

    private $mailer;

    private $environment;

    private $logger;

    private $requestStack;

    private $appSenderEmail;

    private $appUrl;

    public function __construct(
        MailerInterface $mailer,
        Environment     $environment,
        LoggerInterface $logger,
        RequestStack    $requestStack,
        string          $appSenderEmail,
        string          $appUrl
    ){
        $this->mailer           = $mailer;
        $this->environment      = $environment;
        $this->logger           = $logger;
        $this->requestStack     = $requestStack;
        $this->appSenderEmail   = $appSenderEmail;
        $this->appUrl           = $appUrl;
    }

    public function sendWelcomeEmail(UserInterface $user)
    {
        $html = $this->prepareEmailTemplate($user);
        $this->sendEmail($user, $html);
    }

    private function sendEmail($user, $emailBody)
    {
        try {
            $email = new Email();
            $email->html($emailBody);
            $email->to($user->getEmail());
            $email->from('ndy40.ekott@gmail.com');
            $email->subject('Welcome email');
            $this->mailer->send($email);

        } catch (TransportExceptionInterface $ex) {
            $this->logger->error($ex);
        }
    }

    private function prepareEmailTemplate($user)
    {
        $params = [
            'name'          => $user->getFirstName(),
            'activation_link'   => $this->getVerifyLink($user)
        ];

        return $this->environment->render(self::EMAIL_TEMPLATE, $params);
    }

    private function getVerifyLink($user)
    {
//        $request = $this->requestStack->getCurrentRequest();
        return  $this->appUrl . '/user/verify/' . $user->getVerifyToken();
    }
}
<?php
/**
 * Project: ShoppingLists
 * File: ResetPasswordService.php
 * Author: Ndifreke Ekott
 * Date: 09/07/2020 20:06
 *
 */

namespace App\Services;


use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Twig\Environment;

final class SendResetPasswordService
{
    const RESET_TEMPLATE_HTML = 'emails/user/user_password_reset.html.twig';

    const TOKEN_LENGTH = 15;

    private $userProvider;

    private $mailer;

    private $environment;

    private $logger;

    private $repository;

    public function __construct(
        UserProviderInterface $userProvider,
        UserRepository $repository,
        MailerInterface $mailer,
        Environment $environment,
        LoggerInterface $logger
    ){
        $this->userProvider = $userProvider;
        $this->mailer       = $mailer;
        $this->environment  = $environment;
        $this->logger       = $logger;
        $this->repository   = $repository;
    }

    public function resetPassword(string $email)
    {
        if ($user = $this->userProvider->loadUserByUsername($email)) {
            $user = $this->getResetToken($user);
            $this->sendResetEmail($user);
            $this->repository->update($user);
            return true;
        }

        throw new UsernameNotFoundException();
    }

    private function sendResetEmail(UserInterface $user)
    {
        try{
            $params = [
                'reset_link'  => $this->getResetLink($user),
                'first_name'  => $user->getFirstName()
            ];
            $email = new Email();
            $email->to($user->getEmail());
            $email->from('ndy40.ekott@gmail.com');
            $email->subject('Account password reset');
            $email->html(
                $this->environment->render(self::RESET_TEMPLATE_HTML, $params)
            );

            $this->mailer->send($email);
        } catch (\Exception $ex) {
            $this->logger->error($ex);
        }
    }

    private function getResetLink(UserInterface $user)
    {
        return "http://localhost/users/reset-password/" . $user->getResetToken();
    }

    private function getResetToken(UserInterface $user)
    {
        $user->setResetToken(bin2hex(random_bytes(self::TOKEN_LENGTH)));
        return $user;
    }
}
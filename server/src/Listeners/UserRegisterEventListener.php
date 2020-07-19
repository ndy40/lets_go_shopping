<?php
/**
 * Project: ShoppingLists
 * File: UserRegisterEventListener.php
 * Author: Ndifreke Ekott
 * Date: 18/07/2020 15:31
 *
 */

namespace App\Listeners;


use App\Services\SendWelcomeEmailService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;

class UserRegisterEventListener
{
    private $service;

    public function __construct(SendWelcomeEmailService $service)
    {
        $this->service = $service;
    }

    public function onUserRegisterEvent(Event $event)
    {
        $this->service->sendWelcomeEmail($event->getUser());
    }
}
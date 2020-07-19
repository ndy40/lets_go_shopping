<?php
/**
 * Project: ShoppingLists
 * File: NewRegisterEvent.php
 * Author: Ndifreke Ekott
 * Date: 18/07/2020 15:13
 *
 */

namespace App\Events;


use Symfony\Component\Security\Core\User\UserInterface;

class UserRegisterEvent extends \Symfony\Contracts\EventDispatcher\Event
{
    public const NAME = "user.register";

    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
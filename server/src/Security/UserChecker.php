<?php
/**
 * Project: ShoppingLists
 * File: UserChecker.php
 * Author: Ndifreke Ekott
 * Date: 06/09/2020 19:42
 *
 */

namespace App\Security;


use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (! $user->isVerified() ) {
            throw new CustomUserMessageAccountStatusException('User is not verified');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
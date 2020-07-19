<?php
/**
 * Project: ShoppingLists
 * File: UserProvider.php
 * Author: Ndifreke Ekott
 * Date: 30/06/2020 08:57
 *
 */

namespace App\Providers;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{

    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function loadUserByUsername(string $username)
    {
        return $this->repository->loadUserByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException('Expected instance of ' . User::class);
        }

        $refreshUser = $this->repository->findOneBy(['id' => $user->getId()]);

        if (!$refreshUser) {
            throw new EntityNotFoundException('User not found');
        }

        return $refreshUser;
    }

    public function supportsClass(string $class)
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
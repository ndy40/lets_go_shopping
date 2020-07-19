<?php
/**
 * Project: ShoppingLists
 * File: VerifyUserService.php
 * Author: Ndifreke Ekott
 * Date: 19/07/2020 11:48
 *
 */

namespace App\Services;


use App\Exceptions\InvalidVerifyTokenException;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class VerifyUserService
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function verify(string $token)
    {
        $user = $this->repository->findOneBy([
            'verifyToken' => $token
        ]);

        if (! $user instanceof UserInterface) {
            // push this to a translation file.
            throw new InvalidVerifyTokenException('Verify token is invalid');
        }

        $user->setVerifyToken(null);
        $user->setIsVerified(true);
        $this->repository->update($user);

        return true;
    }
}
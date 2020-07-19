<?php
/**
 * Project: ShoppingLists
 * File: ResetUserPasswordService.php
 * Author: Ndifreke Ekott
 * Date: 19/07/2020 13:23
 *
 */

namespace App\Services;


use App\Exceptions\InvalidVerifyTokenException;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetUserPasswordService
{
    private $repository;


    private $encoder;

    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder
    ){
        $this->repository = $repository;
        $this->encoder    = $encoder;
    }

    public function verifyToken(string $token)
    {
        $user = $this->repository->findOneBy([
            'resetToken' => $token
        ]);

        if (!($user instanceof UserInterface)) {
            throw new InvalidVerifyTokenException('Invalid reset token');
        }

        return true;
    }

    public function resetPassword($model)
    {
        $user = $this->repository->findOneBy([
            'resetToken' => $model->getToken()
        ]);

        $encodedPassword = $this->encoder->encodePassword($user, $model->getPassword());
        $user->setPassword($encodedPassword);
        $user->setResetToken(null);

        $this->repository->update($user);

        return true;
    }
}
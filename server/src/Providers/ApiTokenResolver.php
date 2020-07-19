<?php
/**
 * Project: ShoppingLists
 * File: ApiTokenResolver.php
 * Author: Ndifreke Ekott
 * Date: 04/07/2020 18:22
 *
 */

namespace App\Providers;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiTokenResolver
{
    private $tokenService;

    private $jwtManager;

    private $provider;

    public function __construct(TokenStorageInterface $tokenService, JWTTokenManagerInterface $jwtManager, UserProviderInterface $provider)
    {
        $this->tokenService = $tokenService;
        $this->jwtManager = $jwtManager;
        $this->provider = $provider;
    }

    public function getUser(): UserInterface
    {
        $payload = $this->jwtManager->decode($this->tokenService->getToken());
        return $this->provider->loadUserByUsername(
            $payload[$this->jwtManager->getUserIdClaim()]
        );
    }
}
<?php
/**
 * Project: ShoppingLists
 * File: ChangePasswordForm.php
 * Author: Ndifreke Ekott
 * Date: 19/07/2020 17:31
 *
 */

namespace App\Forms\Model;


use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordForm
{
    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\NotCompromisedPassword()
     * @Assert\EqualTo(propertyPath="confirmPassword",
     *     message="Password and Password confirmation do not match.")
     */
    private $password;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $confirmPassword;

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }
}
<?php
/**
 * Project: ShoppingLists
 * File: SignUpRequests.php
 * Author: Ndifreke Ekott
 * Date: 27/06/2020 16:40
 *
 */

namespace App\Requests;


use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpRequests
{
    /**
     * @var string
     * @Assert\Email()
     * @Assert\NotNull()
     * @Groups({"user:reset_password"})
     */
    public $email;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\EqualTo(propertyPath="confirmPassword")
     */
    public $password;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     */
    public $confirmPassword;
}
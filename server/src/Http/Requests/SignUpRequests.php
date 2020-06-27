<?php
/**
 * Project: ShoppingLists
 * File: SignUpRequests.php
 * Author: Ndifreke Ekott
 * Date: 27/06/2020 16:40
 *
 */

namespace App\Http\Requests;


use Symfony\Component\Validator\Constraints as Assert;

class SignUpRequests
{
    /**
     * @var string
     * @Assert\Email()
     * @Assert\NotNull()
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
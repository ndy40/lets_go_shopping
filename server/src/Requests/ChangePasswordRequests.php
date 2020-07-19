<?php
/**
 * Project: ShoppingLists
 * File: ChangePasswordRequests.php
 * Author: Ndifreke Ekott
 * Date: 03/07/2020 21:54
 *
 */

namespace App\Requests;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangePasswordRequests
 *
 * Chnage user password.
 */
class ChangePasswordRequests
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\EqualTo(propertyPath="confirmPassword",
     *     message="Password and Password confirmation do not match.")
     */
    public $password;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     */
    public $confirmPassword;
}
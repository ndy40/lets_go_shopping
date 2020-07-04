<?php
/**
 * Project: ShoppingLists
 * File: ChangePasswordRequests.php
 * Author: Ndifreke Ekott
 * Date: 03/07/2020 21:54
 *
 */

namespace App\Http\Requests;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangePasswordRequests
 *
 * Chnage user password.
 *
 * @package App\Http\Requests
 */
class ChangePasswordRequests
{
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
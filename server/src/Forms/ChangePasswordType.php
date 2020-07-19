<?php
/**
 * Project: ShoppingLists
 * File: ChangePasswordType.php
 * Author: Ndifreke Ekott
 * Date: 19/07/2020 17:17
 *
 */

namespace App\Forms;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('token', HiddenType::class)
            ->add('password', PasswordType::class, [
                'trim'      => true,
                'required'  => true
            ])
            ->add('confirmPassword', PasswordType::class, [
                'trim'      => true,
                'required'  => true,
            ])
            ->add('Change', SubmitType::class);
    }
}
<?php
/**
 * Project: ShoppingLists
 * File: OwnerAware.php
 * Author: Ndifreke Ekott
 * Date: 20/08/2020 22:43
 *
 */

namespace App\Extensions\Doctrine\Owner;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class OwnerAware
 * @package App\Extensions\Doctrine\Owner
 * @Annotations()
 * @Annotation\Target(targets="CLASS")
 */
final class OwnerAware
{
    public $fieldName;
}
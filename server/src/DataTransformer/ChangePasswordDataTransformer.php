<?php
/**
 * Project: ShoppingLists
 * File: ChangePasswordDataTransformer.php
 * Author: Ndifreke Ekott
 * Date: 03/07/2020 21:59
 *
 */

namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChangePasswordDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        // TODO: Implement transform() method.
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return false;
    }
}
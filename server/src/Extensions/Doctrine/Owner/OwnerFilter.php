<?php
/**
 * Project: ShoppingLists
 * File: OwnerFilter.php
 * Author: Ndifreke Ekott
 * Date: 20/08/2020 22:44
 *
 */

namespace App\Extensions\Doctrine\Owner;


use App\Providers\ApiTokenResolver;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;

class OwnerFilter extends \Doctrine\ORM\Query\Filter\SQLFilter
{
    /**
     * @var Doctrine\Common\Annotations\Reader
     */
    private $reader;

    private $tokenResolver;


    /**
     * @inheritDoc
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $ownerAnnotation = $this->reader
            ->getClassAnnotation($targetEntity->getReflectionClass(), OwnerAware::class);

        if (!$ownerAnnotation) {
            return '';
        }

        $currentUser = $this->tokenResolver->getUser();

        if ($currentUser) {
            return $targetTableAlias . '.' . $ownerAnnotation->fieldName . '=' . $currentUser->getId();
        }

        return '';
    }

    /**
     * @param mixed $reader
     */
    public function setReader(Reader $reader): void
    {
        $this->reader = $reader;
    }

    public function setUserProvider(ApiTokenResolver $resolver)
    {
        $this->tokenResolver = $resolver;
    }
}
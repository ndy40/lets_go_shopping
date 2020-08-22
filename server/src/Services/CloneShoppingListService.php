<?php
/**
 * Project: ShoppingLists
 * File: CloneShoppingListService.php
 * Author: Ndifreke Ekott
 * Date: 22/08/2020 21:49
 *
 */

namespace App\Services;


use ApiPlatform\Core\Exception\InvalidValueException;
use App\Entity\ShoppingList;
use App\Repository\ShoppingListRepository;

class CloneShoppingListService
{
    private $repository;

    public function __construct(ShoppingListRepository $repository)
    {
        $this->repository = $repository;
    }

    public function makeCopy(ShoppingList $entity)
    {
        if (!$this->isATemplate($entity)) {
            throw new InvalidValueException('Can only clone a Template shopping list');
        }

        $copyEntity = clone $entity;
        return $this->repository->create($copyEntity);
    }

    public function isATemplate(ShoppingList $entity)
    {
        return $entity->getStatus() === ShoppingList::STATUS_TEMPLATE;
    }
}
<?php
/**
 * Project: ShoppingLists
 * File: CloneShoppingListsAction.php
 * Author: Ndifreke Ekott
 * Date: 22/08/2020 21:44
 *
 */

namespace App\Controller\Operation;


use App\Entity\ShoppingList;
use App\Services\CloneShoppingListService;
use Psr\Log\LoggerInterface;

class CloneShoppingListsAction
{
    private $service;

    private $logger;

    public function __construct(CloneShoppingListService $service, LoggerInterface $logger)
    {
        $this->service  = $service;
        $this->logger   = $logger;
    }

    public function __invoke(ShoppingList $shoppingList)
    {
        try {
            return $this->service->makeCopy($shoppingList);
        } catch (\Exception $ex) {
            $this->logger->error($ex);
            throw $ex;
        }
    }
}
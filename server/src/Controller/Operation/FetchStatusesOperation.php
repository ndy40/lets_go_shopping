<?php
/**
 * Project: ShoppingLists
 * File: FetchStatusesOperation.php
 * Author: Ndifreke Ekott
 * Date: 25/08/2020 06:16
 *
 */

namespace App\Controller\Operation;


use App\Entity\ShoppingList;
use App\Responses\TransitionListsResponse;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;

class FetchStatusesOperation
{
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(ShoppingList $shoppingList)
    {
        $workflow = $this->registry->get($shoppingList);
        $statuses = $workflow->getEnabledTransitions($shoppingList);
        $transitonResponse = new TransitionListsResponse();
        $transitonResponse->statuses =  array_reduce($statuses, function ($carry, Transition $item) {
            $carry = array_merge($carry, $item->getTos());
            return $carry;
        }, []);

        return $transitonResponse;
    }

}
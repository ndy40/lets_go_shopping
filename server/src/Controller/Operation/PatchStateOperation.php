<?php
/**
 * Project: ShoppingLists
 * File: PatchStateOperation.php
 * Author: Ndifreke Ekott
 * Date: 25/08/2020 05:38
 *
 */

namespace App\Controller\Operation;


use App\Entity\ShoppingList;
use App\Repository\ShoppingListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\Registry;

class PatchStateOperation
{
    private $repository;

    private $registry;

    public function __construct(ShoppingListRepository $repository, Registry $registry)
    {
        $this->repository = $repository;
        $this->registry   = $registry;
    }

    public function __invoke(Request $request, ShoppingList $shoppingList)
    {
        $workflow = $this->registry->get($shoppingList);
        $status = $this->getStatus($request);

        if ($workflow->can($shoppingList, $this->getTransition($status))) {
            $workflow->apply($shoppingList, $this->getTransition($status));
            return $this->repository->find($shoppingList->getId());
        }

        return new Response('Invalid status update', 400);
    }

    private function getStatus(Request $request)
    {
        return (json_decode($request->getContent()))->status;
    }

    private function getTransition(string $status)
    {
        return 'to_' . strtolower($status);
    }
}
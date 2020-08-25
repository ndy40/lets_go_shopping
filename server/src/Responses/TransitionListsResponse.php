<?php
/**
 * Project: ShoppingLists
 * File: TransitionListsResponse.php
 * Author: Ndifreke Ekott
 * Date: 25/08/2020 06:14
 *
 */

namespace App\Responses;


use Symfony\Component\Serializer\Annotation\Groups;

class TransitionListsResponse
{
    /**
     * @var array
     * @Groups({"shopping_lists:read"})
     */
    public $statuses;
}
<?php
/**
 * Project: ShoppingLists
 * File: ShoppingListsTest.php
 * Author: Ndifreke Ekott
 * Date: 10/10/2020 13:44
 *
 */

namespace App\Tests\Functional;

use App\Entity\ShoppingList;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class ShoppingListsTest
 * @package App\Tests\Functional
 */
class ShoppingListsTest extends \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase
{
    use TestHelperTrait, RefreshDatabaseTrait;

    const SHOPPING_LIST_SCHEMA = [
        "id" =>  ['type' => 'integer'],
		"status" =>  ['type' => 'string', 'enum' => ['CLOSED', 'TEMPLATE', 'DRAFT', 'PUBLISHED']],
		"channelId" =>  ['type' => 'string'],
		"shoppingItems" =>  [ 'type' => 'array', 'items' => ['type' => 'string']],
		"title" =>  ['type' => 'string'],
		"createdAt" =>  ['type' => 'string', 'format' => 'date-time'],
		"updatedAt" =>  ['type' => 'string', 'format' => 'date-time'],
    ];

    const SHOPPING_LIST_URL = '/api/shopping_lists';

    /**
     * @test
     */
    public function testFetchingShoppingListsOfDefaultUser()
    {
        $schemaCollection = $this->getCollectionSchema( self::SHOPPING_LIST_SCHEMA,
            ShoppingList::class
        );

        $token = $this->getDefaultToken();
        $response = static::createClient([], [
            'headers' => [
                'content-type' => 'application/ld+json',
                'authorization' => 'Bearer ' . $token->token
            ]
        ])->request('GET', self::SHOPPING_LIST_URL);

        $this->assertMatchesResourceCollectionJsonSchema(
            ShoppingList::class
        );
    }

    /**
     * @test
     */
    public function testFetchingShoppingListsItemOfDefaultUser()
    {
        $token = $this->getDefaultToken();
        $response = static::createClient([], [
            'headers' => [
                'content-type' => 'application/ld+json',
                'authorization' => 'Bearer ' . $token->token
            ]
        ])->request('GET', self::SHOPPING_LIST_URL);

        $data = json_decode($response->getContent())->{"hydra:member"}[0];

        $response = static::createClient([], [
            'headers' => [
                'content-type' => 'application/ld+json',
                'authorization' => 'Bearer ' . $token->token
            ]
        ])->request('GET', self::SHOPPING_LIST_URL . '/' . $data->id);

        $this->assertMatchesResourceItemJsonSchema(
            ShoppingList::class
        );
    }

    /**
     * @test
     */
    public function testCreatingShoppingListsCollection()
    {
        $this->markTestIncomplete();
    }

    public function testErrorThrownWithInvalidStatusWhenCreatingShoppingLists()
    {
        $this->markTestIncomplete();
    }

    public function testErrorIsThrownWhenUpdatingToWrongStatusFromDraft()
    {
        $this->markTestIncomplete();
    }

    public function testSuccessfulTransitionOfStateFromDraftToTemplate()
    {
        $this->markTestIncomplete();
    }

    public function testSuccessfulTransitionOfStateFromTemplateToDraft()
    {
        $this->markTestIncomplete();
    }
}
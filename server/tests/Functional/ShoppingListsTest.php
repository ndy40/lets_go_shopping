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
use Hautelook\AliceBundle\PhpUnit\BaseDatabaseTrait;

/**
 * Class ShoppingListsTest
 * @package App\Tests\Functional
 */
class ShoppingListsTest extends \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase
{
    use TestHelperTrait, BaseDatabaseTrait;

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

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }

    /**
     * @test
     */
    public function testFetchingShoppingListsOfDefaultUser()
    {
        $schemaCollection = $this->getCollectionSchema(
            self::SHOPPING_LIST_SCHEMA,
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

        $response = static::createClient([], $this->getRequestHeader($token))
            ->request(
                'GET',
                self::SHOPPING_LIST_URL . '/' . $data->id
            );

        $this->assertMatchesResourceItemJsonSchema(
            ShoppingList::class
        );
    }

    /**
     * @test
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreatingShoppingListsCollection()
    {
        $token = $this->getDefaultToken();
        self::createClient(
            [],
            $this->getRequestHeader($token)
        )->request('POST',
            self::SHOPPING_LIST_URL,
            [
                'json' => [
                    "title" =>  'Test Shopping List 1',
                    "status" => 'DRAFT'
                ]
            ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertMatchesResourceItemJsonSchema(ShoppingList::class);
    }

    /**
     * @test
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testErrorThrownWithInvalidStatusWhenCreatingShoppingLists()
    {
        $token = $this->getDefaultToken();
        self::createClient(
            [],
            $this->getRequestHeader($token)
        )->request('POST',
            self::SHOPPING_LIST_URL,
            [
                'json' => [
                    "title" =>  'Test Shopping List 1',
                    "status" => 'OPENED'
                ]
            ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertMatchesResourceItemJsonSchema(ShoppingList::class);
    }

    /**
     * @test We shouldn't be able to move from TEMPLATE state to CLOSED.
     */
    public function testErrorIsThrownWhenUpdatingToWrongStatusFromTemplateToClosed()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_LIST_URL,
            [
                'json' => [
                    "title" =>  'Test Shopping List 1',
                    "status" => 'TEMPLATE'
                ]
            ]);

        $shippingListIri = json_decode($response->getContent())->{"@id"};

        $headers['headers']['content-type'] = 'application/merge-patch+json';

        self::createClient([], $headers)
            ->request(
                'PATCH',
                $shippingListIri,
                [
                   'json' => [
                       'status' => 'CLOSED'
                   ]
                ]
            );

        $this->assertResponseStatusCodeSame(200);
    }

    public function testSuccessfulTransitionOfStateFromDraftToTemplate()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_LIST_URL,
            [
                'json' => [
                    "title" =>  'Test Shopping List 1',
                    "status" => 'DRAFT'
                ]
            ]);
        $shippingListIri = json_decode($response->getContent())->{"@id"};

        $headers['headers']['content-type'] = 'application/merge-patch+json';

        self::createClient([], $headers)
            ->request(
                'PATCH',
                $shippingListIri,
                [
                    'json' => [
                        'status' => 'TEMPLATE'
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(200);
    }

    public function testSuccessfulTransitionOfStateFromTemplateToDraft()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_LIST_URL,
            [
                'json' => [
                    "title" =>  'Test Shopping List 1',
                    "status" => 'TEMPLATE'
                ]
            ]);
        $shippingListIri = json_decode($response->getContent())->{"@id"};

        $headers['headers']['content-type'] = 'application/merge-patch+json';

        self::createClient([], $headers)
            ->request(
                'PATCH',
                $shippingListIri,
                [
                    'json' => [
                        'status' => 'DRAFT'
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(200);
    }

    public function testSuccessfullyAddingShoppingItemToShoppingLists()
    {
        $this->markTestIncomplete();
    }

    public function testSuccessfullyReplacingShoppingItemInShoppingLists()
    {
        $this->markTestIncomplete();
    }

    public function testSuccessfullyRemovingAllShoppingListItems()
    {
        $this->markTestIncomplete();
    }

    public function testShoppingItemsCanBeAddedByFriends()
    {
        $this->markTestIncomplete("To be implemented after Social feature");
    }

    public function testShoppingItemsCanBeRemovedByFriends()
    {
        $this->markTestIncomplete('To be implemented after social feature');
    }

    public function testShoppingListsInviteCanBeSentToFriends()
    {
        $this->markTestIncomplete('To be implemented after social feature');
    }
}
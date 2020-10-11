<?php
/**
 * Project: ShoppingLists
 * File: ShoppingItemTest.php
 * Author: Ndifreke Ekott
 * Date: 10/10/2020 13:45
 *
 */

namespace App\Tests\Functional;


use App\Entity\ShoppingItem;
use Hautelook\AliceBundle\PhpUnit\BaseDatabaseTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class ShoppingItemTest
 * @package App\Tests\Functional
 */
class ShoppingItemTest extends \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase
{
    use TestHelperTrait, BaseDatabaseTrait;

    const SHOPPING_ITEM_SCHEMA = [
        'id' =>  ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'status' =>  ['type' => 'string', 'enum' => ['PICKED', 'NOT_PICKED']],
        'quantity' => ['type' => "integer"],
        'createdAt' =>  ['type' => 'string', 'format' => 'date-time'],
        'updatedAt' =>  ['type' => 'string', 'format' => 'date-time'],
    ];

    const SHOPPING_ITEM_URL = '/api/shopping_items';

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }


    public function testSuccessfulCreatingOfSingleShoppingItem()
    {
        $schemaCollection = $this->getCollectionSchema(
            self::SHOPPING_ITEM_SCHEMA,
            ShoppingItem::class
        );

        $token = $this->getDefaultToken();
        $response = static::createClient([], [
            'headers' => [
                'content-type' => 'application/ld+json',
                'authorization' => 'Bearer ' . $token->token
            ]
        ])->request('POST', self::SHOPPING_ITEM_URL, [
            'json' => [
                'name' => 'Shopping Item 1',
                'status' => 'NOT_PICKED'
            ]
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(
            ShoppingItem::class
        );
    }

    public function testSuccessfulReplacingSingleShoppingItem()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_ITEM_URL,
            [
                'json' => [
                    'name' => 'Shopping Item 1',
                    'status' => 'NOT_PICKED',
                    'quantity' => 1
                ]
            ]);

        $shippingListIri = json_decode($response->getContent())->{"@id"};

        self::createClient([], $headers)
            ->request(
                'PUT',
                $shippingListIri,
                [
                    'json' => [
                        'name' => 'Replaced Name',
                        'status' => 'PICKED',
                        'quantity' => 1
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @test
     */
    public function testSuccessfulDeletingOfShoppingItem()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_ITEM_URL,
            [
                'json' => [
                    'name' => 'Shopping Item 1',
                    'status' => 'NOT_PICKED',
                    'quantity' => 1
                ]
            ]);

        $shippingListIri = json_decode($response->getContent())->{"@id"};
        $headers['headers']['content-type'] = 'application/merge-patch+json';

        self::createClient([], $headers)
            ->request(
                'DELETE',
                $shippingListIri
            );

        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @test
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testSuccessfulUpdatingOfStatus()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_ITEM_URL,
            [
                'json' => [
                    'name' => 'Shopping Item 1',
                    'status' => 'NOT_PICKED',
                    'quantity' => 1
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
                        'status' => 'PICKED',
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @test
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testErrorThrownIfStatusIsInvalid()
    {
        $token = $this->getDefaultToken();
        $headers = $this->getRequestHeader($token);
        $response = self::createClient(
            [],
            $headers
        )->request('POST',
            self::SHOPPING_ITEM_URL,
            [
                'json' => [
                    'name' => 'Shopping Item 1',
                    'status' => 'NOT_PICKED',
                    'quantity' => 1
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
                        'status' => 'INVALID STATUS',
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(400);
    }
}
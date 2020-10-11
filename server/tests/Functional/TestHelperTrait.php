<?php
/**
 * Project: ShoppingLists
 * File: FunctionalTestHelper.php
 * Author: Ndifreke Ekott
 * Date: 10/10/2020 13:39
 *
 */

namespace App\Tests\Functional;


trait TestHelperTrait
{

    protected string $authorizationUrl = '/authentication_token';

    protected string $defaultEmail = 'testemail@gmail.com';

    protected string $defaultPassword = 'password1234';

    public function getRequestHeader(object $token)
    {
        return [
            'headers' => [
                'content-type' => 'application/ld+json',
                'authorization' => 'Bearer ' . $token->token
            ]
        ];
    }

    public function getDefaultToken()
    {
        $response = static::createClient()
            ->request('POST',
                $this->authorizationUrl,
                [
                    'json' => [
                        'username' => 'testemail@gmail.com',
                        'password' => 'password1234'
                    ]
                ]
            );

        return json_decode($response->getContent());
    }

    public function getCollectionSchema(array $itemSchema, string $clazz)
    {
        return [
          'type' => 'object',
          'properties' => [
              'hydra:member' => [
                  'type' => 'array',
                  'items' => [
                      'type' => 'object',
                      'properties' => $itemSchema
                  ]
              ],
              "hydra:totalItems" => [
                  'type' => 'integer'
              ],
          ]
        ];
    }
}
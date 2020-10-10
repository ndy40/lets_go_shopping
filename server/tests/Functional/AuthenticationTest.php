<?php
/**
 * Project: ShoppingLists
 * File: AuthenticationTest.php
 * Author: Ndifreke Ekott
 * Date: 10/10/2020 11:48
 *
 */

namespace App\Tests\Functional;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AuthenticationTest extends ApiTestCase
{
    use RefreshDatabaseTrait, TestHelperTrait;

    const AUTHENTICATION_URL = '/authentication_token';

    const REFRESH_URL = '/refresh_token';

    /**
     * @test
     */
    public function testSuccessfulAuthentication()
    {
        $successfulSchema = [
            [
                "type" => "object",
                "required" => [
                   "token", "refresh_token"
                ],
                "properties" => [
                    "token" => [
                        "type" => "string"
                    ],
                    "refresh_token" => [
                        "type" => "string"
                    ]
                ]
            ]
        ];

        $response = static::createClient()
            ->request('POST',
                self::AUTHENTICATION_URL,
                [
                    'json' => [
                        'username' => 'testemail@gmail.com',
                        'password' => 'password1234'
                    ]
                ]
            );
        $this->assertResponseStatusCodeSame(200);
        $this->assertMatchesJsonSchema($successfulSchema);
    }

    /**
     * @test
     */
    public function testInvalidAuthentication()
    {
        $invalidSchema = [
            'type' => 'object',
            'properties' =>  [
                "code" => 401,
                "message" => 'Invalid credentials.'
            ]
        ];

        static::createClient()
            ->request('POST',
                self::AUTHENTICATION_URL,
                [
                    'json' => [
                        'username' => 'invalid_email@gmail.com',
                        'password' => 'password1234'
                    ]
                ]
            );
        $this->assertResponseStatusCodeSame(401);
        $this->assertMatchesJsonSchema($invalidSchema);
    }

    /**
     * @test 400 error thrown if field is missing in request.
     */
    public function test400ErrorWithMissingFields()
    {
        $response = static::createClient()
            ->request('POST',
                self::AUTHENTICATION_URL,
                [
                    'json' => [
                        'password' => 'password1234'
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @test
     */
    public function testThatAnUnverifiedAccountsCannotLogIn()
    {
        $schema = [
            "type" =>  "object",
            "properties" => [
                "code" =>  [
                    "type" => "integer",
                    "value" => 401
                ],
                "message" =>  [
                    "type"  => "string",
                    "value" => "User is not verified"
                ]
            ]
        ];

        $response = static::createClient()
            ->request('POST',
                self::AUTHENTICATION_URL,
                [
                    'json' => [
                        'username' => 'testemail2@gmail.com',
                        'password' => 'password1234'
                    ]
                ]
            );

        $this->assertResponseStatusCodeSame(401);
        $this->assertMatchesJsonSchema($schema);
    }
}
<?php
/**
 * Project: ShoppingLists
 * File: SwaggerDecorators.php
 * Author: Ndifreke Ekott
 * Date: 05/09/2020 13:31
 *
 */
declare(strict_types=1);

namespace App\Decorators;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerDecorator implements NormalizerInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);
        $docs['components']['schemas']['Token'] = [
            'type'       => 'object',
            'properties' => [
                'token'  => [
                    'type'      => 'string',
                    'readOnly'  => true,
                ],
                'refresh_token' => [
                    'type'      => 'string',
                    'readOnly'  => true,
                ]
            ]
        ];

        $docs['components']['schemas']['RefreshToken'] = [
          'type' => 'object',
          'properties' => [
              'refresh_token' => [
                  'type' => 'string',
              ]
          ]
        ];

        $docs['components']['schemas']['Credentials'] = [
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'username',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
            ],
        ];
        $tokenDocumentation = [
            'paths' => [
                '/authentication_token' => [
                    'post' => [
                        'tags' => ['Token'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get JWT token to login.',
                        'requestBody' => [
                            'description' => 'Create new JWT Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get JWT token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/refresh_token' => [
                    'post' => [
                        'tags' => ['Token'],
                        'operationId' => 'postRefreshToken',
                        'summary' => 'Refresh JWT token',
                        'requestBody' => [
                            'description' => 'Refresh JWT token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' =>  '#/components/schemas/RefreshToken'
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            Response::HTTP_CREATED => [
                                'description' => 'New JWT Token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
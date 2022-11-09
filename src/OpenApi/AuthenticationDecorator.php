<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;

final class AuthenticationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $pathItem = new Model\PathItem(
            ref: 'Auth',
            get: new Model\Operation(
                operationId: 'getUserSessionItem',
                tags: ['Auth'],
                responses: [
                    '204' => [
                        'description' => 'Get authenticated UserSession resource',
                        'headers' => [
                            'Location' => [
                                'description' => 'The IRI of the currently authenticated UserSession resource',
                                'type' => 'string'
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Invalid request',
                    ],  
                ],
                summary: 'Get authenticated UserSession resource.',
                security: [],
            ),
        );

        $openApi->getPaths()->addPath('/api/auth', $pathItem);

        $pathItem = new Model\PathItem(
            ref: 'Auth',
            post: new Model\Operation(
                operationId: 'postUserAuth',
                tags: ['Auth'],
                responses: [
                    '204' => [
                        'description' => 'Get authenticated UserSession resource',
                        'headers' => [
                            'Location' => [
                                'description' => 'The IRI of the generated UserSession resource',
                                'type' => 'string'
                            ],
                            'Set-Cookie' => [
                                'description' => 'HttpOnly cookie with the Authentication key to be used in further API requests',
                                'type' => 'string'
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Invalid request',
                    ],
                ],
                summary: 'Authenticates a User resource.',
                requestBody: new Model\RequestBody(
                    description: 'The User credentials',
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'properties' => [
                                    'username' => [
                                        'type' => 'string',
                                        'example' => 'johndoe',
                                        'required' => true
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'example' => 'apassword',
                                        'required' => true
                                    ],
                                ]
                            ],
                        ],
                    ]),
                ),
                security: [],
            ),
        );

        $openApi->getPaths()->addPath('/api/auth/user', $pathItem);

        $pathItem = new Model\PathItem(
            ref: 'Auth',
            post: new Model\Operation(
                operationId: 'postUserSessionTokenAuth',
                tags: ['Auth'],
                responses: [
                    '204' => [
                        'description' => 'Get authenticated UserSession resource',
                        'headers' => [
                            'Location' => [
                                'description' => 'The IRI of the generated UserSession resource',
                                'type' => 'string'
                            ],
                            'Set-Cookie' => [
                                'description' => 'HttpOnly cookie with the Authentication key to be used in further API requests',
                                'type' => 'string'
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Invalid request',
                    ],
                ],
                summary: 'Authenticates a User resource via a token.',
                requestBody: new Model\RequestBody(
                    description: 'The User token',
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'properties' => [
                                    'token' => [
                                        'type' => 'string',
                                        'required' => true,
                                        'description' => 'A JWT created to authenticate an UserSession for an User resource'
                                    ],
                                ]
                            ],
                        ],
                    ]),
                ),
                security: [],
            ),
        );

        $openApi->getPaths()->addPath('/api/auth/token', $pathItem);

        return $openApi;
    }
}
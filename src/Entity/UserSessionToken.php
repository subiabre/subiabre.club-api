<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Put;
use App\State\UserSessionTokenStateProcessor;
use App\State\UserSessionTokenStateProvider;

#[API\ApiResource(
    uriTemplate: '/users/{id}/token',
    uriVariables: [
        'id' => new Link(
            fromClass: User::class,
            toProperty: 'user'
        )
    ],
    operations: [
        new Put(
            requirements: null,
            provider: UserSessionTokenStateProvider::class,
            processor: UserSessionTokenStateProcessor::class,
            security: "is_granted('USER_IS', request)"
        )
    ]
)]
class UserSessionToken {
    #[API\ApiProperty(writable: false, readable: true)]
    private string $token;

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}

<?php

namespace App\Security\Authentication;

use App\Repository\User\UserKeyRepository;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private UserKeyRepository $userKeyRepository
    ) {
    }

    public function supports(Request $request): ?bool
    {
        if ($request->getMethod() === Request::METHOD_POST && $request->getPathInfo() === '/api/authentication/key') {
            return true;
        }

        return false;
    }

    public function authenticate(Request $request): Passport
    {
        try {
            $key = $this->userKeyRepository->findOneBy(['value' => json_decode($request->getContent(), true)['value']]);

            if (!$key) throw new AuthenticationException();

            return new SelfValidatingPassport(
                new UserBadge($key->getUser()->getId(), function ($user) {
                    $user = $this->userRepository->find($user);

                    if (!$user) throw new UserNotFoundException();

                    return $user;
                })
            );
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException($e::class);
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    //    public function start(Request $request, AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}

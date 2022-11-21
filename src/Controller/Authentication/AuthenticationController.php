<?php

namespace App\Controller\Authentication;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User\UserSession;
use App\Repository\User\UserRepository;
use App\Repository\User\UserSessionRepository;
use App\Service\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth')]
class AuthenticationController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserSessionRepository $userSessionRepository,
        private AuthenticationService $authenticationService,
        private IriConverterInterface $iriConverter,
        private EntityManagerInterface $entityManager
    ) {
    }

    private function error(string $message = 'check that the Content-Type header is "application/json"', int $status = 400): Response
    {
        return $this->json(
            [ 'error' => sprintf('Invalid Auth request: %s.', $message) ],
            $status
        );
    }

    #[Route('', name: 'app_auth_session', methods: ['GET'])]
    public function getSession(Request $request): Response
    {
        if (!$request->getSession()->isStarted()) $request->getSession()->start();
        
        $userSession = $this->userSessionRepository->findOneBySession($request->getSession());

        if (!$userSession) return $this->error("Could not find session");

        return new Response(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->iriConverter->getIriFromResource($userSession)
            ]
        );
    }

    #[Route('/user', name: 'app_auth_credentials', methods: ['POST'])]
    public function authCredentials(Request $request): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) return $this->error();

        if (!$request->getSession()->isStarted()) $request->getSession()->start();

        $user = $this->userRepository->findByUser($this->getUser());

        $userSession = new UserSession();
        $userSession->setUser($user);
        $userSession->setSessionId($request->getSession()->getId());
        $userSession->setUserAgent($request->headers->get('User-Agent'));
        $userSession->setDateCreated(new \DateTime());
        $userSession = $this->authenticationService->refreshUserSession($userSession);

        $this->entityManager->persist($userSession);
        $this->entityManager->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->iriConverter->getIriFromResource($userSession)
            ]
        );
    }

    #[Route('/key', name: 'app_auth_key', methods: ['POST'])]
    public function authKey(Request $request): Response
    {
        return $this->authCredentials($request);
    }
}

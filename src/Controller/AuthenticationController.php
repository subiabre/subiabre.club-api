<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\UserSession;
use App\Repository\UserRepository;
use App\Service\Session\SessionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth')]
class AuthenticationController extends AbstractController
{
    public function __construct(
        private SessionService $sessionService,
        private UserRepository $userRepository,
        private IriConverterInterface $iriConverter,
        private EntityManagerInterface $entityManager
    ) {
    }

    private function error(int $status = 400, string $message = 'check that the Content-Type header is "application/json"'): Response
    {
        return $this->json(
            [ 'error' => sprintf('Invalid login request: %s.', $message) ],
            $status
        );
    }

    #[Route('', name: 'app_auth_session', methods: ['GET'])]
    public function getSession(Request $request): Response
    {
        if (!$request->getSession()->isStarted()) $request->getSession()->start();
        
        $userSession = $this->userSessionRepository->findOneBySession($request->getSession());

        return new Response(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->iriConverter->getIriFromResource($userSession)
            ]
        );
    }

    #[Route('/token', name: 'app_auth_token', methods: ['POST'])]
    public function authToken(Request $request): Response
    {
        return $this->authCredentials($request);
    }

    #[Route('/credentials', name: 'app_auth_credentials', methods: ['POST'])]
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
        $userSession = $this->sessionService->refreshUserSession($userSession);

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
}

<?php

namespace App\Command\User;

use App\Entity\User\UserKey;
use App\Repository\User\UserRepository;
use App\Service\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:key-create',
    description: 'Generate a UserKey to authenticate a User',
)]
class UserKeyCreateCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthenticationService $authenticationService,
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the User to be authenticated with this token')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $io->error(sprintf("The user %s could not be found.", $username));
            return Command::FAILURE;
        }

        $key = new UserKey();
        $key->setUser($user);
        $key->setDateCreated(new \DateTime());
        $key = $this->authenticationService->updateUserKeyValue($key);

        $this->entityManager->persist($key);
        $this->entityManager->flush();        

        $io->success([
            "The UserKey was created successfully:",
            $key->getValue()
        ]);

        return Command::SUCCESS;
    }
}

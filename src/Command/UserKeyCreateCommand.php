<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        private AuthenticationService $authenticationService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $sessionLifetime = $this->authenticationService->getConfig()[AuthenticationService::CONFIG_SESSION_LIFETIME];

        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the User to be authenticated with this token')
            ->addOption('expires', null, InputOption::VALUE_NONE, 'Should this token expire?')
            ->addOption('lifetime', null, InputOption::VALUE_OPTIONAL, 'If the token is set to expire this will mark for how long its valid',  $sessionLifetime)
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

        $token = $this->authenticationService->createUserSessionToken($user, $input->getOption('expires') ? $input->getOption('lifetime') : null);

        $io->success("The token was created successfully");
        $io->writeln($token);

        return Command::SUCCESS;
    }
}

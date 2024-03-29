<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create:admin',
    description: 'create a new admin account',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create:admin');
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('full_name', InputArgument::OPTIONAL, 'Full Name')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $fullName = $input->getArgument('full_name');
        if(!$fullName) {
            $question = new Question ('what\'s their name?');
            $fullName = $helper->ask($input, $output, $question);
        }


        $email = $input->getArgument('email');
        if(!$email) {
            $question = new Question ('what\'s their email?');
            $email = $helper->ask($input, $output, $question);
        }


        $plainPassword = $input->getArgument('password');
        if(!$plainPassword) {
            $question = new Question ('what\'s their password?');
            $plainPassword = $helper->ask($input, $output, $question);
        }

        $user = (new User())->setFullName($fullName)
            ->setEmail($email)
            ->setPlainPassword($plainPassword)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('New admin created');

        return Command::SUCCESS;
    }
}

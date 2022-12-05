<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class GetTokenCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:token';

    protected function configure()
    {
        $this->setDescription("Get token command");
        $this->addArgument('username', InputArgument::REQUIRED, 'The username');
        $this->addArgument('password', InputArgument::REQUIRED, 'The user password');
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var ContainerInterface $container */
        $container = $this->getApplication()->getKernel()->getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        /** @var UserPasswordHasher $encoder */
        $encoder = $container->get('security.user_password_hasher');

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $input->getArgument('username')
        ]);

        if ($user != null) {
            if ($encoder->isPasswordValid($user, $input->getArgument('password'))) {
                $jwt = $container->get('lexik_jwt_authentication.jwt_manager')->create($user);
                $output->writeln($jwt);
            } else {
                $output->writeln('Bad credentials');
            }
        } else {
            $output->writeln('User not found');
        }
       
        return Command::SUCCESS;
    }

}
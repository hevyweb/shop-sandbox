<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class GenerateUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:generate:user';

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function configure()
    {
        $this
            ->setDescription('Generates admin user')
            ->addOption(
                'username',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Username',
                'admin'
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_OPTIONAL,
                'User password',
                'admin'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getOption('username');
        $plainPassword = $input->getOption('password');
        /**
         * @var UserRepository $userRepository
         */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);


        if ($userRepository->findOneBy(['username' => $username])) {
            $io->error('User with username "' . $username . '" already exists.');
            return;
        }
        $user = new User();
        $this->assignRoles($user);
        $this->assignUniqueEmail($user);
        $user
            ->setUsername($username)
            ->setFirstName($username)
            ->setLastName($username)
            ->setPlainPassword($plainPassword);
        $this->encodePassword($user);
        $entityManager->persist($user);
        $entityManager->flush();

        $io->success('New admin user created. Username: ' . $username . ', password: ' . $plainPassword);
    }

    /**
     * @param User $user
     * @return User
     */
    protected function assignUniqueEmail($user)
    {
        do {
            $email = 'Change-me' . uniqid();
        } while($this->getUserRepository()->findOneBy(['email' => $email]));

        return $user->setEmail($email);
    }

    /**
     * @return EntityManager|object
     */
    private function getEntityManager()
    {
        if (empty($this->entityManager)){
            $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        }

        return $this->entityManager;
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository()
    {
        return $this->getEntityManager()->getRepository(User::class);
    }

    /**
     * @param User $user
     * @throws \RuntimeException
     * @return User
     */
    private function assignRoles($user)
    {
        /**
         * @var Role $adminRole
         * @var Role $userRole
         * @var RoleRepository $roleRepository
         */
        $roleRepository = $this->getEntityManager()->getRepository(Role::class);
        $adminRole = $roleRepository->findOneBy(['code' => 'ROLE_ADMIN']);
        $userRole = $roleRepository->findOneBy(['code' => 'ROLE_USER']);

        if (empty($adminRole) || empty($userRole)) {
            throw new \RuntimeException('Run migrations first. Roles have not been set yet');
        }

        return $user
            ->addRole($adminRole)
            ->addRole($userRole);
    }

    /**
     * @param User $user
     * @return User
     */
    private function encodePassword($user)
    {
        /**
         * @var UserPasswordEncoder $passwordEncoder
         */
        $passwordEncoder = $this->getContainer()->get('security.password_encoder');
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        return $user->setPassword($password);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\User;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221204234751 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $encoder = $this->container->get('security.user_password_hasher');
        $em = $this->container->get("doctrine.orm.entity_manager");

        $user = new User();
        $user
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setEmail('admin');

        $password = $encoder->hashPassword($user, "admin");
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

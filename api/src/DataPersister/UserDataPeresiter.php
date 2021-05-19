<?php

namespace Webapp\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webapp\Entity\User;
use Webapp\Manager\UserManager;

final class UserDataPeresiter implements ContextAwareDataPersisterInterface
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * UserDataPeresiter constructor.
     * @param EntityManagerInterface $entityManager
     * @param JWTEncoderInterface $JWTEncoder
     * @param JWTTokenManagerInterface $jwtManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        JWTEncoderInterface $JWTEncoder,
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->userManager = new UserManager($entityManager, $JWTEncoder, $jwtManager, $eventDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        if (!$data instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function persist($data, array $context = [])
    {
        switch ($context['collection_operation_name']) {
            case 'register':
                $this->userManager->create($data);
                break;
            case 'login':
             $this->userManager->login($data);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}

<?php

namespace Webapp\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webapp\Entity\User;
use Webapp\Event\User\UserEvent;
use Webapp\Event\UserLoginEvent;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;
    /**
     * @var JWTEncoderInterface
     */
    private $JWTEncoder;
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * UserManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param JWTEncoderInterface $jwtEncoder
     * @param JWTTokenManagerInterface $jwtManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        JWTEncoderInterface $jwtEncoder,
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->entityManager = $entityManager;
        $this->JWTEncoder = $jwtEncoder;
        $this->jwtManager = $jwtManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param User $user
     */
    public function create(User $user)
    {
        $event = new UserEvent($user);

        $this->entityManager->transactional(function (EntityManagerInterface $em) use ($event, $user) {
            $user->setRoles($user->getRoles());
            $this->eventDispatcher->dispatch($event);

            $em->persist($user);
        });
    }

    /**
     * 1. Receives credentials from the user
     * 2. Validate the password
     * 3. Return a token back to the client.
     *
     * @param User $user
     * @throws \Exception
     */
    public function login(User $user): void
    {
        /** @var User $dbUser */
        $dbUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        $this->hydrateProperty('id', $user, $dbUser->getId());

        if (!$dbUser || null === $dbUser) {
            throw new \InvalidArgumentException('Theres no such user found', Response::HTTP_NOT_FOUND);
        }

        try {
            if (!($user->getPassword() === $dbUser->getPassword()))
                throw new JWTDecodeFailureException('Wrong password', 'God knows why');

            $token = $this->jwtManager->create($user);

        } catch (JWTDecodeFailureException $e) {
            throw new \Exception('Password is incorrect!');
        }

        $this->hydrateProperty('token', $user, $token);

        $this->eventDispatcher->dispatch(new UserLoginEvent($token), UserLoginEvent::USER_LOGIN);
    }

    /**
     * @param string $propertyName
     * @param object $object
     * @param mixed $valueToHydrate
     * @return object
     * @throws \ReflectionException
     */
    private function hydrateProperty(string $propertyName, object $object, $valueToHydrate): object
    {
        try {
            $refObj = new \ReflectionObject($object);
            $refProp = $refObj->getProperty($propertyName);
            $refProp->setAccessible(true);
            $refProp->setValue($object, $valueToHydrate);
        } catch (\ReflectionException $e) {
            throw new \ReflectionException($e->getMessage());
        }
        return $object;
    }
}

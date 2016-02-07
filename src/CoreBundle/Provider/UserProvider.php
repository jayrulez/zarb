<?php

namespace CoreBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;

class UserProvider implements UserProviderInterface
{
    protected $_repository;

    public function __construct($repository)
    {
        $this->_repository = $repository;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->_repository
            ->findOneBy(['username'=>$username]);

        if (null === $user) {
            $message = sprintf(
                'Unable to find an CoreBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf(
                'Instances of "%s" are not supported.',
                $class
            ));
        }

        $username = $user->getUsername();

        return $this->_repository->findOneBy(['username'=>$username]);
    }

    public function supportsClass($class)
    {
        return $class === 'CoreBundle\Entity\User';
    }
}
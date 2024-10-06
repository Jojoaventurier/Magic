<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

final readonly class OAuthRegistrationService
{

    public function __construct(
        private UserRepository $repository
    ) {

    }

    /**
     * @param GoogleUser $resourceOwner 
     */

public function persist(ResourceOwnerInterface $resourceOwner): User
{
    $currentDateTime = new \DateTime('now');
    $user = (new User())
    ->setEmail($resourceOwner->getEmail())
    ->setGoogleId($resourceOwner->getId())
    ->setCreationDate($currentDateTime)
    ->setUserName($resourceOwner->getName());

     $this->repository->add($user, flush: true);
     return $user;

}

}
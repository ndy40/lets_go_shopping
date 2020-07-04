<?php
/**
 * Project: ShoppingLists
 * File: UserCreateEventListner.php
 * Author: Ndifreke Ekott
 * Date: 03/07/2020 21:16
 *
 */

namespace App\Events\Listeners;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserCreateEventListener
 *
 * Event listens for user create event and encodes password.
 *
 * @package App\Listeners
 */
class UserCreateEventListener implements EventSubscriber
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function getSubscribedEvents()
    {
        return [
          Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $encodedPassword = $this->encoder
            ->encodePassword($entity, $entity->getPassword());

        $entity->setPassword($encodedPassword);
    }

}
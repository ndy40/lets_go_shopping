<?php
/**
 * Project: ShoppingLists
 * File: UserCreateEventListner.php
 * Author: Ndifreke Ekott
 * Date: 03/07/2020 21:16
 *
 */

namespace App\Listeners;


use App\Events\UserRegisterEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\EventManager\Event;

/**
 * Class UserCreateEventListener
 *
 * Event listens for user create event and encodes password.
 *
 * @package App\Listeners
 */
class UserCreateEventListener implements EventSubscriber
{
    const TOKEN_LENGTH = 20;

    private $encoder;

    private $dispatcher;

    public function __construct(UserPasswordEncoderInterface $encoder, EventDispatcherInterface $dispatcher)
    {
        $this->encoder = $encoder;
        $this->dispatcher = $dispatcher;
    }

    public function getSubscribedEvents()
    {
        return [
          Events::prePersist,
          Events::postPersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if (!$this->supports($args)) {
            return;
        }

        $entity = $args->getObject();
        $encodedPassword = $this->encoder
            ->encodePassword($entity, $entity->getPassword());

        $entity->setPassword($encodedPassword);
        $entity->setVerifyToken($this->getVerifyToken());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        if (!$this->supports($args)) {
            return;
        }

        $event = new UserRegisterEvent($args->getObject());
        $this->dispatcher->dispatch($event, UserRegisterEvent::NAME);
    }

    private function supports(LifecycleEventArgs $args)
    {
        return $args->getObject() instanceof UserInterface;
    }

    private function getVerifyToken()
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH));
    }
}
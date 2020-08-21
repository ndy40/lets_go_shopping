<?php
/**
 * Project: ShoppingLists
 * File: EntityOwnerPreWriteSubscriber.php
 * Author: Ndifreke Ekott
 * Date: 21/08/2020 21:59
 *
 */

namespace App\Listeners;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Extensions\Doctrine\Owner\OwnerAware;
use App\Providers\ApiTokenResolver;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;


class EntityOwnerPreWriteSubscriber implements EventSubscriberInterface
{
    private $tokenResolver;

    private $accessor;

    private $reader;

    public function __construct(ApiTokenResolver $tokenResolver, PropertyAccessorInterface $accessor, Reader $reader)
    {
        $this->tokenResolver = $tokenResolver;
        $this->accessor      = $accessor;
        $this->reader        = $reader;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
          KernelEvents::VIEW => ['attachOwner', EventPriorities::PRE_WRITE]
        ];
    }

    public function attachOwner(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if (!is_object($entity) || !$this->isOwnerAware($entity)) {
            return;
        }

        $this->setOwner($entity, $this->tokenResolver->getUser());
    }

    private function setOwner($entity, $user)
    {
        if ($this->accessor->isWritable($entity, 'owner') && $user) {
            $this->accessor->setValue($entity, 'owner', $user);
        }
    }

    private function isOwnerAware($object)
    {
        return $this->reader->getClassAnnotation(
            new \ReflectionClass(get_class($object)),
            OwnerAware::class
            )
            instanceof OwnerAware;
    }
}
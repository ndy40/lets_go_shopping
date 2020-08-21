<?php
/**
 * Project: ShoppingLists
 * File: DoctrineOwnerFilterKernelEvent.php
 * Author: Ndifreke Ekott
 * Date: 21/08/2020 06:45
 *
 */

namespace App\Listeners;


use App\Providers\ApiTokenResolver;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class DoctrineOwnerFilterKernelEvent
{
    private $tokenProvider;

    private $em;

    private $reader;

    public function __construct(ApiTokenResolver  $provider, EntityManagerInterface $em, Reader $reader)
    {
        $this->tokenProvider = $provider;
        $this->em = $em;
        $this->reader = $reader;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($event->getRequest()->headers->has('authorization')) {

            $filter = $this->em->getFilters()->enable('owner_filter');

            $filter->setUserProvider($this->tokenProvider);
            $filter->setReader($this->reader);
        }

    }
}
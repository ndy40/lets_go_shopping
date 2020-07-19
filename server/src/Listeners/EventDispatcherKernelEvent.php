<?php
/**
 * Project: ShoppingLists
 * File: EventDispatcherKernelEvent.php
 * Author: Ndifreke Ekott
 * Date: 18/07/2020 18:13
 *
 */

namespace App\Listeners;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EventDispatcherKernelEvent
{

    public function onKernelRequest(RequestEvent $event)
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $containerBuilder->addCompilerPass(new RegisterListenersPass());
        $containerBuilder->register('event_dispatcher', EventDispatcher::class);
    }
}
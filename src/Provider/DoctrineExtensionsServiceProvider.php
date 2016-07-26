<?php

namespace Sergiors\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\Common\EventManager;
use Gedmo\Sortable\SortableListener;
use Gedmo\Timestampable\TimestampableListener;
use Gedmo\Sluggable\SluggableListener;

/**
 * @author Sérgio Rafael Siquira <sergio@inbep.com.br>
 */
class DoctrineExtensionsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        if (!isset($app['annotations'])) {
            throw new \LogicException(
                'You must register the AnnotationsServiceProvider to use the DoctrineExtensionsServiceProvider.'
            );
        }

        $app['gedmo.listeners'] = function () {
            return [
                new SortableListener(),
                new TimestampableListener(),
                new SluggableListener(),
            ];
        };

        $app['db.event_manager'] = $app->extend('db.event_manager', function (EventManager $event) use ($app) {
            $listeners = $app['gedmo.listeners'];

            foreach ($listeners as $listener) {
                $listener->setAnnotationReader($app['annotations']);
                $event->addEventSubscriber($listener);
            }

            return $event;
        });
    }
}

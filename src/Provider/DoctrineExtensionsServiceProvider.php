<?php

namespace Sergiors\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\Common\EventManager;
use Gedmo\Sortable\SortableListener;
use Gedmo\Timestampable\TimestampableListener;
use Gedmo\Sluggable\SluggableListener;

/**
 * @author Sérgio Rafael Siquira <sergio@inbep.com.br>
 */
class DoctrineExtensionsServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!isset($app['annotation_reader'])) {
            throw new \LogicException(
                'You must register the AnnotationsServiceProvider to use the DoctrineExtensionsServiceProvider.'
            );
        }

        $app['gedmo.listeners'] = $app->share(function () {
            return [
                new SortableListener(),
                new TimestampableListener(),
                new SluggableListener(),
            ];
        });

        $app['db.event_manager'] = $app->share(
            $app->extend('db.event_manager', function (EventManager $event) use ($app) {
                $listeners = $app['gedmo.listeners'];

                foreach ($listeners as $listener) {
                    $listener->setAnnotationReader($app['annotation_reader']);
                    $event->addEventSubscriber($listener);
                }

                return $event;
            })
        );
    }

    public function boot(Application $app)
    {
    }
}

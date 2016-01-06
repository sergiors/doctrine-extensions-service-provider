<?php
namespace Sergirs\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\Common\EventManager;
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

        $app['gedmo.sortable_listener'] = $app->share(function (Application $app) {
            $listener = new SortableListener();
            $listener->setAnnotationReader($app['annotation_reader']);
            return $listener;
        });

        $app['gedmo.timestampable_listener'] = $app->share(function (Application $app) {
            $listener = new TimestampableListener();
            $listener->setAnnotationReader($app['annotation_reader']);
            return $listener;
        });

        $app['gedmo.sluggable_listener'] = $app->share(function (Application $app) {
            $listener = new SluggableListener();
            $listener->setAnnotationReader($app['annotation_reader']);
            return $listener;
        });

        $app['db.event_manager'] = $app->share(
            $app->extend('db.event_manager', function (EventManager $event) use ($app) {
                $event->addEventSubscriber($app['gedmo.sortable_listener']);
                $event->addEventSubscriber($app['gedmo.timestampable_listener']);
                $event->addEventSubscriber($app['gedmo.sluggable_listener']);
                return $event;
            })
        );
    }

    public function boot(Application $app)
    {
    }
}

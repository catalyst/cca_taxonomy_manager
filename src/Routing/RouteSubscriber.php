<?php

namespace Drupal\cca_taxonomy_manager\Routing;

use Drupal\Core\Routing\RouteObjectInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $config = \Drupal::config('cca_taxonomy_manager.settings');
    if ($config->get('taxonomies')) {
      foreach ($config->get('taxonomies') as $taxonomy) {
        if ($taxonomy) {
          if ($route = $collection->get('view.cca_taxonomy_manager_search.' . $taxonomy)) {
            $route->setDefault('taxonomy_vocabulary', $taxonomy);
          }
        }
      }
    }
  }

  /**
   * Adds variables to the current route match object if it is a relevant taxonomy view.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The request event, that contains the current request.
   */
  public function onKernelRequest(RequestEvent $event) {
    $request = $event->getRequest();
    // Deliberately avoid calling \Drupal::routeMatch()->getRouteName() because
    // that will instantiate a protected route match object that will not have
    // the raw variable we want to add.
    $routeName = RouteObjectInterface::ROUTE_NAME;
    $config = \Drupal::config('cca_taxonomy_manager.settings');
    if ($config->get('taxonomies')) {
      foreach ($config->get('taxonomies') as $taxonomy) {
        if ($taxonomy) {
          if ($request->attributes->get($routeName) === 'view.cca_taxonomy_manager_search.' . $taxonomy) {
            if ($raw = $request->attributes->get('_raw_variables', [])) {
              $raw->add(['taxonomy_vocabulary' => $taxonomy]);
              $request->attributes->set('_raw_variables', $raw);
            }
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events = parent::getSubscribedEvents();

    // The route object attribute will have been set in
    // router_listener::onKernelRequest(), which has a priority of 32.
    $events[KernelEvents::REQUEST][] = ['onKernelRequest', 31];

    return $events;
  }

}

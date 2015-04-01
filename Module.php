<?php
namespace RouteCache;

use Zend\Mvc\MvcEvent;

class Module 
{
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'routeLoad'), 999);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'routeSave'), 0);
    }
    
    /**
     * Method that tries to save a route match into a cache system
     * @param MvcEvent $event
     */
    public function routeSave(MvcEvent $event)
    {
        $match = $event->getRouteMatch();
        if(!$match) {
            return;
        }
    
        if($event->getParam('route-cached') || !$event->getParam('route-cacheable')) {
            return;
        }
    
        $path = $event->getRequest()
                      ->getUri()
                      ->getPath();
    
        // save the route match into the cache.
        $cache = $event->getApplication()->getServiceManager()->get('var-cache');
        $cacheKey = $this->getCacheKey($path);
        $data = array (
            'name'  => $event->getRouteMatch()->getMatchedRouteName(),
            'route' => $path,
            'defaults' => $event->getRouteMatch()->getParams(),
        );
        $cache->setItem($cacheKey, $data);
    }
    
    /**
     * Method that tries to load cached routes and speed up the matching
     * @param MvcEvent $event
     */
    public function routeLoad(MvcEvent $event)
    {
        $request = $event->getRequest();
        if(!(
            $request->getMethod() == \Zend\Http\Request::METHOD_GET &&
            $request->getQuery()->count() == 0
        )) {
            // We do not cache route match for requests that can produce
            // different match.
            return ;
        }
    
        $event->setParam('route-cacheable', true);
    
        // check if we have data in our cache
        $path = $request->getUri()
                        ->getPath();
    
        $cache = $event->getApplication()->getServiceManager()->get('var-cache');
        $cacheKey = $this->getCacheKey($path);
        $cachedData = $cache->getItem($cacheKey);
    
        if(!empty($cachedData)) {
            $event->setParam('route-cached', true);
    
            $cachedRoute = \Zend\Mvc\Router\Http\Literal::factory($cachedData);
            $router = $event->getRouter();
            $router->addRoute($cachedData['name'], $cachedRoute, 99999);
        }
    }
    
    /**
     * Returns ZF2 compatible cache key name
     * @param string $text
     * @return string
     */
    public function getCacheKey($text) 
    {
        return 'route-'.md5($text);
    }
}
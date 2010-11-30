<?php
Scarlet\Router::draw(function($map){
    /**
     * The priority is based on the order of creation:
     *  first created -> highest priority
     * 
     * Sample of regular route:
     *  $map->match('products/:id', 'catalog#view');
     *      - or -
     *  $map->match('products/:id', array('controller' => 'catalog', 'action' => 'view'));
     * 
     * You can also set the root of your site, otherwise this defaults to home#index
     *  $map->root('home#index');
     *      - or -
     *  $map->root(array('controller' => 'home', 'action' => 'index'));
     */

    /**
     * This is a default route. It's not recommended for RESTful applications
     * as it makes every action in every controller accessible via GET requests.
     */
    $map->match('/:controller(/:action(/:id))');
    
});

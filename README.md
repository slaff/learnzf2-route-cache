Simple module that speeds up the route matching using caching.

This module is part of the "Learn ZF2" book (http://learnzf2.com) that can help you learn Zend Framework 2 (ZF2) and improve the performance of your ZF2 application.

We have noticed that in real world applications routing can take 
300 ms or more. With this module the routing can take less than 40 ms.

Installation
------------
Run the following command from the root folder of your ZF2 application.

```
php composer.phar require learnzf2/route-cache:dev-master
```

Setup
-----
First: To enable the module make sure to add it to the list of modules in your config/application.config.php.

Second: This module requires a cache service that is called "var-cache". The cache service 
should be able to store variables. If you already have such a service but it has a different name then you can use aliases in the service_manager section of your application configuration. This can be achieved by adding the following lines:

```
'service_manager' = array(
     // ...
     'aliases' => array (
     	// ...
     	'var-cache' => '<your-cache-service-name>'
     )
),
```

If you don't have a cache service in your application then copy the file 
vendor/learnzf2/route-cache/config/cache.local.php.dist to config/autoload/cache.local.php.
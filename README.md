# jms-serializer-carbon
Carbon Serializer for the JMS Serializer library

# Installation
To instal and use the package please install as per the [JMS documentation](http://jmsyst.com/libs/serializer/master/handlers):
```php
$builder
    ->configureHandlers(function(JMS\Serializer\Handler\HandlerRegistry $registry) {
        $registry->registerSubscribingHandler(new MyHandler());
    })
;
```


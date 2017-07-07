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
# Symfony2
You will need to register a new service in order to utilise the Carbon serilisation:
```yml
carbon_handler:
        class: AutumnDev\JMS\CarbonHandler
        tags:
            - { name: jms_serializer.subscribing_handler }
```
# Usage
In order to use the serialisation you must tag your entities thusly:
```php
    /**
    * @Type("Carbon")
    */
    public $date;
```
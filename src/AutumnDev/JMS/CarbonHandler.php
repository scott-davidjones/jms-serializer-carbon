<?php

namespace AutumnDev\JMS;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\Context;
use Carbon\Carbon;

class CarbonHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'CarbonISO8601',
                'method' => 'serializeCarbonISO8601ToJson',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'xml',
                'type' => 'CarbonISO8601',
                'method' => 'serializeCarbonISO8601ToXml',
            ),
        );
    }
    /**
     * serializes carbon object to ISO 8601 string for JSON
     * serialization
     *
     * @param XmlSerializationVisitor $visitor
     * @param Carbon $date
     * @param array $type
     * @param Context $context
     *
     * @return String
     */
    public function serializeCarbonISO8601ToJson(
        JsonSerializationVisitor $visitor,
        Carbon $date,
        array $type,
        Context $context
    ) {
        return $date->toIso8601String();
    }
    /**
     * serializes carbon object to ISO 8601 string for XML
     * serialization
     *
     * @param XmlSerializationVisitor $visitor
     * @param Carbon $date
     * @param array $type
     * @param Context $context
     *
     * @return String
     */
    public function serializeCarbonISO8601ToXml(
        XmlSerializationVisitor $visitor,
        Carbon $date,
        array $type,
        Context $context
    ) {
        return $date->toIso8601String();
    }
}
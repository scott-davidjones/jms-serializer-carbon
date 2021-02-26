<?php

namespace AutumnDev\JMS;

use Carbon\CarbonInterface;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\Context;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone;
use DateTime;
use RuntimeException;

class CarbonHandler implements SubscribingHandlerInterface
{

	/** @var bool|mixed */
	private $xmlCData;
	/** @var DateTimeZone */
	private $defaultTimezone;
	/** @var mixed|string */
	private $defaultFormat;

	public function __construct($defaultFormat = DateTime::ISO8601, $defaultTimezone = 'UTC', $xmlCData = true)
	{
		$this->defaultFormat = $defaultFormat;
		$this->defaultTimezone = new DateTimeZone($defaultTimezone);
		$this->xmlCData = $xmlCData;
	}

	public static function getSubscribingMethods()
	{
		$methods = [];
		$deserialisationTypes = ['Carbon', 'CarbonImmutable', Carbon::class, CarbonImmutable::class];
		$serialisationTypes = ['Carbon', 'CarbonImmutable', Carbon::class, CarbonImmutable::class];

		foreach (['json', 'xml', 'yml'] as $format) {

			foreach ($deserialisationTypes as $type) {
				$methods[] = [
					'type' => $type,
					'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
					'format' => $format,
				];
			}

			foreach ($serialisationTypes as $type) {
				$methods[] = [
					'type' => $type,
					'format' => $format,
					'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
					'method' => 'serializeCarbon',
				];
			}
		}

		return $methods;
	}

	public function serializeCarbon(
		SerializationVisitorInterface $visitor,
		CarbonInterface $date,
		array $type,
		Context $context
	): string
	{
		if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
			return $visitor->visitSimpleString($date->format($this->getFormat($type)), $type);
		}

		$format = $this->getFormat($type);
		if ('U' === $format) {
			return $visitor->visitInteger($date->format($format), $type);
		}

		return $visitor->visitString($date->format($this->getFormat($type)), $type);
	}

	public function deserializeCarbonFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
	{
		if ($this->isDataXmlNull($data)) {
			return null;
		}

		$dateObj = $this->parseDateTime($data, $type);
		return Carbon::instance($dateObj);
	}

	public function deserializeCarbonFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
	{
		if (empty($data)) {
			return null;
		}

		$dateObj = $this->parseDateTime($data, $type);
		return Carbon::instance($dateObj);
	}

	private function parseDateTime($data, array $type, $immutable = false)
	{
		$timezone = isset($type['params'][1]) ? new DateTimeZone($type['params'][1]) : $this->defaultTimezone;
		$format = $this->getFormat($type);

		if ($immutable) {
			$datetime = DateTimeImmutable::createFromFormat($format, (string)$data, $timezone);
		} else {
			$datetime = DateTime::createFromFormat($format, (string)$data, $timezone);
		}

		if (false === $datetime) {
			throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
		}

		return $datetime;
	}

	private function getFormat(array $type): string
	{
		return isset($type['params'][0]) ? $type['params'][0] : $this->defaultFormat;
	}

	private function isDataXmlNull($data): bool
	{
		$attributes = $data->attributes('xsi', true);
		return isset($attributes['nil'][0]) && (string)$attributes['nil'][0] === 'true';
	}
}

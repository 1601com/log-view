<?php

namespace Agentur1601com\LogView\Service\Filter;

abstract class AbstractFilter
{

	const COMPARATOR_IN = 'IN';
	const COMPARATOR_DATETIME_GREATER_THAN = 'GREATER_THAN';
	const COMPARATOR__DATETIME_LESSER_THAN = 'LESSER_THAN';
	const COMPARATOR_CONTAINS = 'CONTAINS';

	/**
	 * @var array
	 */
	private $_filter = [];

	/**
	 * @var array
	 */
	private static $_allowedOperators = [
		self::COMPARATOR_IN => '',
		self::COMPARATOR_DATETIME_GREATER_THAN => '',
		self::COMPARATOR__DATETIME_LESSER_THAN => '',
		self::COMPARATOR_CONTAINS => '',
	];

	final public function addFilter(string $key, string $comparator, $comparatorValue): bool
	{
		if (!isset(self::$_allowedOperators[$comparator])) {
			return false;
		}
		$this->_filter[$key][$comparator][] = $comparatorValue;
		return true;
	}

	final public function applyFilter(array &$entries): bool
	{
		foreach ($entries as $index => $entry) {
			if ($this->_isValidEntry($entry)) {
				continue;
			}
			unset($entries[$index]);
		}
		return true;
	}

	final private function _isValidEntry(array $entry): bool
	{
		foreach ($this->_filter as $key => $keyComparisons) {
			if (!isset($entry[$key])) {
				//no value to compare to => entry is fine
				continue;
			}
			foreach ($keyComparisons as $comparator => $values) {
				switch ($comparator) {
					case self::COMPARATOR_IN:
						if (!is_array($values)) {
							$values = [$values];
						}
						$found = false;
						foreach ($values as $value) {
							if (array_search($entry[$key], $value)) {
								$found = true;
							}
						}
						if (!$found) {
							return false;
						}
						break;
					case self::COMPARATOR_DATETIME_GREATER_THAN:
						if ($this->_convertToTimestamp($entry[$key]) < $this->_convertToTimestamp($values[0], false)) {
							return false;
						}
						break;
					case self::COMPARATOR__DATETIME_LESSER_THAN:
						if ($this->_convertToTimestamp($entry[$key]) > $this->_convertToTimestamp($values[0], false)) {
							return false;
						}
						break;
					case self::COMPARATOR_CONTAINS:
						if (!is_array($values)) {
							$values = [$values];
						}
						$found = false;
						foreach ($values as $value) {
							if (str_contains($entry[$key], $value)) {
								$found = true;
							}
						}
						if (!$found) {
							return false;
						}
						break;
					default:
						trigger_error(sprintf('Unhandled comparator: %s for key: %s', $comparator, $key), E_USER_WARNING);
						//don't apply this filter then
						break;
				}
			}
		}
		return true;
	}

	private function _convertToTimestamp(string $value, bool $hasSeconds = true): int
	{
		$seconds = '';
		if ($hasSeconds) {
			$seconds = ':s';
		}
		return \DateTime::createFromFormat(sprintf('Y-m-d\\TH:i%s', $seconds), $value)->getTimestamp();
	}
}

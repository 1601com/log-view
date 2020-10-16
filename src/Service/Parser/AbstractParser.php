<?php

namespace Agentur1601com\LogView\Service\Parser;

use Agentur1601com\LogView\Service\Filter\AbstractFilter;

abstract class AbstractParser
{

	/**
	 * @var AbstractFilter|null
	 */
	protected $_filter;

	protected $_limit = 50;

	abstract protected function _parseFile(string $filePath): ?array;

	final public function execute(array $collection): array
	{
		$result = [];
		foreach ($collection as $filePath) {
			$result = array_merge($result, $this->_parseFile($filePath));
		}
		return $result;
	}

	/**
	 * @param AbstractFilter|null $filter
	 * @return bool
	 */
	public function setFilter(?AbstractFilter $filter): bool
	{
		$this->_filter = $filter;
		return true;
	}

	final protected function _isFiltered(array $entry): bool
	{
		if (!$this->_filter) {
			return false;
		}
		return $this->_filter->isEntryFiltered($entry);
	}

	final public function setLimit(int $limit): bool
	{
		$this->_limit = $limit;
		return true;
	}
}

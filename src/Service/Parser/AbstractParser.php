<?php

namespace Agentur1601com\LogView\Service\Parser;

abstract class AbstractParser
{

	abstract protected function _parseFile(string $filePath): ?array;

	final public function execute(array $collection): array
	{
		$result = [];
		foreach ($collection as $filePath) {
			$result = array_merge($result, $this->_parseFile($filePath));
		}
		return $result;
	}
}

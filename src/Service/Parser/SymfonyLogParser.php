<?php

namespace Agentur1601com\LogView\Service\Parser;

class SymfonyLogParser extends AbstractParser
{
	const KEY_DATE = 'date';
	const KEY_LOGGER = 'logger';
	const KEY_LEVEL = 'level';
	const KEY_MESSAGE = 'message';
	const KEY_CONTEXT = 'context';
	const KEY_EXTRA = 'extra';

	protected function _parseFile(string $filePath): ?array
	{
		if (!$fileContent = $this->_getFileContent($filePath)) {
			trigger_error(sprintf('No content in file: %s', $filePath));
			return null;
		}
		$result = [];
		//go through each file in reverse so the newest one is at the top
		$data = explode(PHP_EOL, $fileContent);
		for (end($data); key($data) !== null; prev($data)) {
			if (!($line = current($data))) {
				//skip empty lines
				continue;
			}
			if (!$entry = $this->_parseLogLine($line)) {
				continue;
			}
			$result[] = $entry;
		}
		return $result;
	}

	final private function _parseLogLine(string $logLine): ?array
	{
		if (!preg_match(sprintf('/\[(?P<%s>.*)\] (?P<%s>[\w-]+).(?P<%s>\w+): (?P<%s>[^\[\{]+) (?P<%s>[\[\{].*[\]\}]) (?P<%s>[\[\{].*[\]\}])/',
			self::KEY_DATE, self::KEY_LOGGER, self::KEY_LEVEL, self::KEY_MESSAGE, self::KEY_CONTEXT, self::KEY_EXTRA), $logLine, $matches)) {
			//todo use fallback error parsing
			return null;
			trigger_error(sprintf('log line does not match regex: %s', $logLine), E_USER_WARNING);
			return null;
		}

		$entry = [
			self::KEY_DATE => null,
			self::KEY_LOGGER => null,
			self::KEY_LEVEL => null,
			self::KEY_MESSAGE => null,
			self::KEY_CONTEXT => null,
			self::KEY_EXTRA => null,
		];
		foreach ($matches as $groupName => $value) {
			if ($groupName === self::KEY_DATE) {
				if (!$newDate = \DateTime::createFromFormat('Y-m-d H:i:s', $value)) {
					trigger_error(sprintf('Unable to parse date: %s', $value), E_USER_WARNING);
					$entry[$groupName] = $value;
				}
				$entry[$groupName] = $newDate->format('Y-m-d\\TH:i:s');
				continue;
			}
			$entry[$groupName] = $value;
		}
		return $entry;
	}

	final private function _getFileContent(string $filePath): ?string
	{
		if (!is_readable($filePath)) {
			trigger_error(sprintf('%s is not readable', $filePath), E_USER_WARNING);
			return null;
		}
		return file_get_contents($filePath);
	}
}
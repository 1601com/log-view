<?php

namespace Agentur1601com\LogView\Service\Loader;

use Agentur1601com\LogView\Service\Filter\AbstractFilter;
use Agentur1601com\LogView\Service\Parser\SymfonyParser;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractLoader
{

	/**
	 * @var Kernel
	 */
	private $_kernel;

	/**
	 * @var SymfonyParser
	 */
	private $_logParser;

	private $_supportedExtensions = [
		'log' => '',
	];

	/**
	 * AbstractLoader constructor.
	 * @param KernelInterface $kernel
	 * @param SymfonyParser $logParser
	 */
	final public function __construct(KernelInterface $kernel, SymfonyParser $logParser)
	{
		$this->_kernel = $kernel;
		$this->_logParser = $logParser;
	}

	/**
	 * @param AbstractFilter $filter
	 * @return array
	 */
	final public function load(?AbstractFilter $filter): array
	{
		$this->_logParser->setFilter($filter);
		$files = [];
		foreach (new \DirectoryIterator($this->_kernel->getLogDir()) as $item) {
			if (!isset($this->_supportedExtensions[$item->getExtension()])) {
				continue;
			}
			$files[] = $item->getRealPath();
		}
		return $this->_logParser->execute($files);
	}

	final public function setLimit(int $limit): bool
	{
		return $this->_logParser->setLimit($limit);
	}
}

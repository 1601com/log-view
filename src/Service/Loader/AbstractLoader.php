<?php

namespace Agentur1601com\LogView\Service\Loader;

use Agentur1601com\LogView\Service\Parser\SymfonyLogParser;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractLoader
{

	/**
	 * @var Kernel
	 */
	private $_kernel;

	/**
	 * @var SymfonyLogParser
	 */
	private $_logParser;

	private $_supportedExtensions = [
		'log' => '',
	];

	/**
	 * AbstractLoader constructor.
	 * @param KernelInterface $kernel
	 * @param SymfonyLogParser $logParser
	 */
	final public function __construct(KernelInterface $kernel, SymfonyLogParser $logParser)
	{
		$this->_kernel = $kernel;
		$this->_logParser = $logParser;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	final public function load(): array
	{
		$files = [];
		foreach (new \DirectoryIterator($this->_kernel->getLogDir()) as $item) {
			if (!isset($this->_supportedExtensions[$item->getExtension()])) {
				continue;
			}
			$files[] = $item->getRealPath();
		}
		return $this->_logParser->parseFileCollection($files);
	}
}

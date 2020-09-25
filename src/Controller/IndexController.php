<?php

namespace Agentur1601com\LogView\Controller;

use Agentur1601com\LogView\Service\Filter\DefaultFilter;
use Agentur1601com\LogView\Service\Loader\DefaultLoader;
use Agentur1601com\LogView\Service\Parser\SymfonyParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/contao/log-view",
 *     name=IndexController::class,
 *     defaults={
 *         "_scope" = "backend",
 *         "_token_check" = false,
 *         "_backend_module" = "log-view"
 *     }
 * )
 */
class IndexController extends AbstractController
{
	const REQUEST_KEY_DATETIME_START = 'dateTimeStart';
	const REQUEST_KEY_DATETIME_END = 'dateTimeEnd';
	const REQUEST_KEY_MESSAGE = 'message';
	const REQUEST_KEY_LEVEL = 'level';
	const REQUEST_KEY_LOGGER = 'logger';

	/**
	 * @var TwigEnvironment
	 */
	private $_twig;
	/**
	 * @var DefaultLoader
	 */
	private $_logLoader;
	/**
	 * @var DefaultFilter
	 */
	private $_logFilter;

	/**
	 * IndexController constructor.
	 * @param TwigEnvironment $twig
	 * @param DefaultLoader $logLoader
	 * @param DefaultFilter $logFilter
	 */
	public function __construct(TwigEnvironment $twig, DefaultLoader $logLoader, DefaultFilter $logFilter)
	{
		$this->_twig = $twig;
		$this->_logLoader = $logLoader;
		$this->_logFilter = $logFilter;
	}

	/**
	 * @param Request $request
	 * @return Response|null
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function __invoke(Request $request)
	{
		$result = $this->_logLoader->load();
		if (!$this->_applyRequestFilter($request)) {
			trigger_error('Failed ot apply log filter from request data', E_USER_WARNING);
			return null;
		}
		$this->_logFilter->applyFilter($result);
		return new Response($this->_twig->render('@LogView/index.html.twig', [
			'logData' => $result,
			'requestValues' => [
				self::REQUEST_KEY_DATETIME_START => $request->get(self::REQUEST_KEY_DATETIME_START),
				self::REQUEST_KEY_DATETIME_END => $request->get(self::REQUEST_KEY_DATETIME_END),
				self::REQUEST_KEY_LEVEL => $request->get(self::REQUEST_KEY_LEVEL),
				self::REQUEST_KEY_MESSAGE => $request->get(self::REQUEST_KEY_MESSAGE),
				self::REQUEST_KEY_LOGGER => $request->get(self::REQUEST_KEY_LOGGER),
			]
		]));
	}

	/**
	 * Use request object to filter entries
	 *
	 * @param Request $request
	 * @return bool
	 */
	private function _applyRequestFilter(Request $request): bool
	{
		if (!$this->_applyDateTimeFilter($request)) {
			trigger_error('Failed to apply date time filter', E_USER_WARNING);
			return false;
		}
		if (!$this->_applyMessageFilter($request)) {
			trigger_error('Failed to apply message filter', E_USER_WARNING);
			return false;
		}
		if (!$this->_applyLevelFilter($request)) {
			trigger_error('Failed to apply level filter', E_USER_WARNING);
			return false;
		}
		if (!$this->_applyLoggerFilter($request)) {
			trigger_error('Failed to apply logger filter', E_USER_WARNING);
			return false;
		}
		return true;
	}

	private function _applyLoggerFilter(Request $request): bool
	{
		if ((!$logger = $request->get(self::REQUEST_KEY_LOGGER))) {
			return true;
		}
		if (!$this->_logFilter->addFilter(SymfonyParser::KEY_LOGGER, $this->_logFilter::COMPARATOR_CONTAINS, $logger)) {
			trigger_error('Unable to apply logger filter', E_USER_WARNING);
			return false;
		}
		return true;
	}

	private function _applyLevelFilter(Request $request): bool
	{
		if ((!$level = $request->get(self::REQUEST_KEY_LEVEL)) || !is_array($level)) {
			return true;
		}
		if (!$this->_logFilter->addFilter(SymfonyParser::KEY_LEVEL, $this->_logFilter::COMPARATOR_IN, $level)) {
			trigger_error('Unable to apply level filter', E_USER_WARNING);
			return false;
		}
		return true;
	}

	private function _applyMessageFilter(Request $request): bool
	{
		if (!$message = $request->get(self::REQUEST_KEY_MESSAGE)) {
			return true;
		}
		if (!$this->_logFilter->addFilter(SymfonyParser::KEY_MESSAGE, $this->_logFilter::COMPARATOR_CONTAINS, $message)) {
			trigger_error('Unable to apply message filter', E_USER_WARNING);
			return false;
		}
		return true;
	}

	private function _applyDateTimeFilter(Request $request): bool
	{
		if ($dateGreaterThanValue = $request->get(self::REQUEST_KEY_DATETIME_START)) {
			if (!$this->_logFilter->addFilter(SymfonyParser::KEY_DATE, $this->_logFilter::COMPARATOR_DATETIME_GREATER_THAN, $dateGreaterThanValue)) {
				trigger_error('Unable to add filter for datetime', E_USER_WARNING);
				return false;
			}
		}
		if ($dateLesserThanValue = $request->get(self::REQUEST_KEY_DATETIME_END)) {
			if (!$this->_logFilter->addFilter(SymfonyParser::KEY_DATE, $this->_logFilter::COMPARATOR__DATETIME_LESSER_THAN, $dateLesserThanValue)) {
				trigger_error('Unable to add filter for datetime', E_USER_WARNING);
				return false;
			}
		}

		return true;
	}
}

<?php

namespace agentur1601com\logView\EventListener;

use agentur1601com\logView\Controller\IndexController;
use Contao\CoreBundle\Event\MenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class BackendMenuListener
{
	/**
	 * @var RouterInterface
	 */
	private $router;
	/**
	 * @var RequestStack
	 */
	private $requestStack;

	/**
	 * BackendMenuListener constructor.
	 * @param RouterInterface $router
	 * @param RequestStack $requestStack
	 */
	public function __construct(RouterInterface $router, RequestStack $requestStack)
	{
		$this->router = $router;
		$this->requestStack = $requestStack;
	}

	/**
	 * @param MenuEvent $event
	 */
	public function onBuild(MenuEvent $event): void
	{
		$factory = $event->getFactory();
		$tree = $event->getTree();

		if ($tree->getName() !== 'mainMenu') {
			return;
		}

		$contentNode = $tree->getChild('content');

		$node = $factory->createItem('log-view')
			->setUri($this->router->generate(IndexController::class))
			->setLabel('Log view')
			->setLinkAttribute('title', 'View log files for this project')
			->setLinkAttribute('class', 'log-view') //todo: remove if not required
			->setCurrent($this->requestStack->getCurrentRequest()->get('_backend_module') === 'log-view');

		$contentNode->addChild($node);
	}
}


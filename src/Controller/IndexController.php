<?php

namespace agentur1601com\logView\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

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
	/**
	 * @var TwigEnvironment
	 */
	private $twig;

	public function __construct(TwigEnvironment $twig)
	{
		$this->twig = $twig;
	}

	public function __invoke()
	{
		return new Response($this->twig->render(
			'base.html.twig',
			[]
		));
	}
}

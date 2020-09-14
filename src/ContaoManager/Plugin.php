<?php

/*
 * This file is part of [package name].
 *
 * (c) John Doe
 *
 * @license LGPL-3.0-or-later
 */

namespace Agentur1601com\LogView\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Agentur1601com\LogView\LogViewBundle;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getBundles(ParserInterface $parser)
	{
		return [
			BundleConfig::create(LogViewBundle::class)
				->setLoadAfter([ContaoCoreBundle::class, TwigBundle::class]),
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
	{
		return $resolver
			->resolve(__DIR__ . '/../Resources/config/routing.yml')
			->load(__DIR__ . '/../Resources/config/routing.yml');
	}
}

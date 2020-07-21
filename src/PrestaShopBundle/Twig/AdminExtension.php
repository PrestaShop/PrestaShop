<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\InitRuntimeInterface;
use Twig_Extension;

/**
 * Twig extension for the Symfony Asset component.
 *
 * @deprecated since 1.7.5.0 to be removed in 1.8.0.0
 *
 * @author Mlanawo Mbechezi <mlanawo.mbechezi@ikimea.com>
 */
class AdminExtension extends Twig_Extension implements InitRuntimeInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AdminExtension constructor.
     *
     * @param RequestStack|null $requestStack
     * @param ContainerInterface $container
     */
    public function __construct(RequestStack $requestStack = null, ContainerInterface $container)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig_admin_extension';
    }
}

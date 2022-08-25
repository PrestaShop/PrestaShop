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

declare(strict_types=1);

namespace PrestaShopBundle\Bridge\AdminController\Action;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class is a base class for action, which are actions the user can do.
 * For example, add a new entity, or update it.
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $config;

    /**
     * To create an action, you must specify a label and an array of configurations where you can find:
     * - confirm: is the text for the confirmation alert
     * - desc: is text for the title HTML attribute
     * - href: the link to the action
     * - icon: a name of a PrestaShop icons
     * - text: the text in the link for the action
     *
     * @param string $label
     * @param array $config
     */
    public function __construct(string $label, array $config = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        $resolver->resolve($config);

        $this->label = $label;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'confirm',
            'desc',
            'href',
            'icon',
            'text',
        ]);
    }
}

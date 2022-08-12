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

namespace PrestaShopBundle\Bridge\Helper\Listing\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class represents a field which need a label and a config.
 */
class Field implements FieldInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $config;

    /**
     * To create a field, you must specify a label and an array of configurations where you can find:
     * - align: represents where you want to align the value in the cell
     * - class: represents a class which be added to the class html attributes in the cell
     * - filter_key: represents the name of the filter key
     * - orderby: tell if you want or not to be able to order by this field in your list
     * - position: identify a field as a position cell
     * - search: enable or disable the search by this field
     * - title: which is required and represents the text, will be shown in the list's header
     * - width: define the width for the cell of this field
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
        $resolver->setRequired([
            'title',
        ]);

        $resolver->setDefined([
            'align',
            'class',
            'filter_key',
            'orderby',
            'position',
            'search',
            'width',
        ]);

        $resolver->addAllowedTypes('orderby', 'boolean');
        $resolver->addAllowedTypes('search', 'boolean');
    }
}

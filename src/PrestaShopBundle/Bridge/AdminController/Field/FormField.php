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

namespace PrestaShopBundle\Bridge\AdminController\Field;

/**
 * @todo: unsure if we need separate interface or no. Will see later
 */
class FormField
{
    /**
     * @todo - this is not the actual html input type, but the legacy form type, idk how to represent the difference in naming
     */
    public const TYPE_INPUT = 'input';

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $type
     * @param array<string, mixed>> $config
     * @param string $value the value of the input
     */
    public function __construct(string $type, array $config, string $value = '')
    {
        $this->type = $type;
        $this->config = $config;
        //@todo: add options resolver?
        $this->value = $value;
    }

    /**
     * @todo: define existing types as constants?
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}

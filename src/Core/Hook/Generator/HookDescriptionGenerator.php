<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Hook\Generator;

use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Util\String\StringModifier;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShop\PrestaShop\Core\Util\String\StringValidator;
use PrestaShop\PrestaShop\Core\Util\String\StringValidatorInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generates description for hook names.
 */
final class HookDescriptionGenerator implements DynamicHookDescriptiveContentGeneratorInterface
{
    /**
     * @var array
     */
    private $hookDescriptions;

    /**
     * @var StringValidatorInterface
     */
    private $stringValidator;

    /**
     * @var StringModifierInterface
     */
    private $stringModifier;

    /**
     * @param array $hookDescriptions
     * @param StringValidatorInterface $stringValidator
     * @param StringModifierInterface $stringModifier
     */
    public function __construct(
        array $hookDescriptions,
        StringValidatorInterface $stringValidator,
        StringModifierInterface $stringModifier
    ) {
        $this->hookDescriptions = $hookDescriptions;
        $this->stringValidator = $stringValidator;
        $this->stringModifier = $stringModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($hookName)
    {
        foreach ($this->hookDescriptions as $hookPlaceholder => $hookDescription) {
            $prefix = isset($hookDescription['prefix']) ? $hookDescription['prefix'] : '';
            $suffix = isset($hookDescription['suffix']) ? $hookDescription['suffix'] : '';

            if ($this->stringValidator->startsWithAndEndsWith($hookName, $prefix, $suffix) &&
                !$this->stringValidator->doesContainsWhiteSpaces($hookName)
            ) {
                return new HookDescription(
                    $hookName,
                    $hookDescription['title'],
                    $hookDescription['description']
                );
            }
        }

        return new HookDescription(
            $hookName,
            '',
            ''
        );
    }
}

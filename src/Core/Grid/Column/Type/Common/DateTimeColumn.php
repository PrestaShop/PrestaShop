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

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeColumn extends AbstractColumn
{
    /**
     * Default date format.
     * Note the use of non-breaking hyphens (U+2011)
     */
    public const DEFAULT_FORMAT = 'Y‑m‑d H:i:s';

    /**
     * Complete datetime format, without seconds.
     * Note the use of non-breaking hyphens (U+2011)
     */
    public const DATETIME_WITHOUT_SECONDS = 'Y‑m‑d H:i';

    private const FORMAT_NORMALIZATION_MAP = [
        '-' => '‑', // convert hyphens into non-breaking hyphens
    ];

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'date_time';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'field',
            ])
            ->setDefaults([
                'format' => self::DEFAULT_FORMAT,
                'empty_data' => '',
                'clickable' => false,
            ])
            ->setAllowedTypes('format', 'string')
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('empty_data', 'string')
            ->setAllowedTypes('clickable', 'bool')
            ->setNormalizer(
                'format',
                function (Options $options, $value) {
                    return strtr($value, self::FORMAT_NORMALIZATION_MAP);
                }
            );
    }
}

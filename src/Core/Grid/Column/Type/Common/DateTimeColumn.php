<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

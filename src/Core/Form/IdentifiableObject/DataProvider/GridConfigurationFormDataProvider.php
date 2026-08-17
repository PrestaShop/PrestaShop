<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

/**
 * Provides the default data of the grid views configuration form. The current values are
 * injected by the grid views panel presenter, which knows the displayed grid; this form is
 * never loaded by id.
 */
final class GridConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        return $this->getDefaultData();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'display_shared_filters' => true,
            'display_totals' => true,
        ];
    }
}

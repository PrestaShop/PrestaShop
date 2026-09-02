<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;

/**
 * Provides data for language's add/edit forms
 */
final class LanguageFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $bus,
        private readonly bool $isMultistoreFeatureActive,
        private readonly array $defaultShopAssociation
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var EditableLanguage $editableLanguage */
        $editableLanguage = $this->bus->handle(new GetLanguageForEditing($id));

        $data = [
            'name' => $editableLanguage->getName(),
            'iso_code' => $editableLanguage->getIsoCode(),
            'tag_ietf' => $editableLanguage->getTagIETF(),
            'locale' => $editableLanguage->getLocale(),
            'short_date_format' => $editableLanguage->getShortDateFormat(),
            'full_date_format' => $editableLanguage->getFullDateFormat(),
            'is_rtl' => $editableLanguage->isRtl(),
            'is_active' => $editableLanguage->isActive(),
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $editableLanguage->getShopAssociation();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [
            'short_date_format' => 'Y-m-d',
            'full_date_format' => 'Y-m-d H:i:s',
            'is_rtl' => false,
            'is_active' => true,
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}

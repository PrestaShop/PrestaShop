<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Form\DTO\ShopRestriction;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\MultiStoreSettingsFormDataProviderInterface;

/**
 * {@inheritdoc}
 */
final class ShopLogosFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var MultiStoreSettingsFormDataProviderInterface
     */
    private $themeMultiStoreSettingsFormDataProvider;

    /**
     * @param CommandBusInterface $commandBus
     * @param MultiStoreSettingsFormDataProviderInterface $themeMultiStoreSettingsFormDataProvider
     */
    public function __construct(
        CommandBusInterface $commandBus,
        MultiStoreSettingsFormDataProviderInterface $themeMultiStoreSettingsFormDataProvider
    ) {
        $this->commandBus = $commandBus;
        $this->themeMultiStoreSettingsFormDataProvider = $themeMultiStoreSettingsFormDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->themeMultiStoreSettingsFormDataProvider->getData();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function setData(array $data)
    {
        $data = $this->geFilteredFieldsByShopRestriction($data);

        $command = new UploadLogosCommand();

        if (!empty($data['header_logo'])) {
            $command->setUploadedHeaderLogo($data['header_logo']);
        }

        if (!empty($data['mail_logo'])) {
            $command->setUploadedMailLogo($data['mail_logo']);
        }

        if (!empty($data['invoice_logo'])) {
            $command->setUploadedInvoiceLogo($data['invoice_logo']);
        }

        if (!empty($data['favicon'])) {
            $command->setUploadedFavicon($data['favicon']);
        }

        $this->commandBus->handle($command);

        return [];
    }

    /**
     * If shop_restriction argument exists in the post this means that certain shop restrictions are applied.
     * It filters and drops the values which are not being selected for editing for specific shop.
     *
     * @param array $data - form data
     *
     * @return array
     */
    private function geFilteredFieldsByShopRestriction(array $data)
    {
        if (!isset($data['shop_restriction'])) {
            return $data;
        }

        /** @var ShopRestriction $shopRestriction */
        $shopRestriction = $data['shop_restriction'];

        $shopRestrictionFields = $shopRestriction->getShopRestrictionFields();

        foreach ($shopRestrictionFields as $shopRestrictionField) {
            $doesValueExistsAndNotRestrictedToShop = isset($data[$shopRestrictionField->getFieldName()])
                && !$shopRestrictionField->isRestrictedToContextShop();

            if ($doesValueExistsAndNotRestrictedToShop) {
                unset($data[$shopRestrictionField->getFieldName()]);
            }
        }

        return $data;
    }
}

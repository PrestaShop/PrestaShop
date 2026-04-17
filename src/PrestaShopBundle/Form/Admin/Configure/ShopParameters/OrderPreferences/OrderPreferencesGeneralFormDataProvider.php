<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderPreferences;

use PrestaShop\PrestaShop\Adapter\CMS\CMSDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\GeneralConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Order Settings" page.
 */
class OrderPreferencesGeneralFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var GeneralConfiguration
     */
    private $generalConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CMSDataProvider
     */
    private $cmsDataProvider;

    public function __construct(
        GeneralConfiguration $generalConfiguration,
        TranslatorInterface $translator,
        CMSDataProvider $cmsDataProvider
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->translator = $translator;
        $this->cmsDataProvider = $cmsDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->generalConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // If TOS option is disabled - reset the cms id as well
        if (!$data['enable_tos']) {
            $data['tos_cms_id'] = 0;
        }

        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->generalConfiguration->updateConfiguration($data);
    }

    /**
     * Perform validation on form data before saving it.
     *
     * @param array $data
     *
     * @return array Returns array of errors
     */
    protected function validate(array $data)
    {
        $errors = [];
        $purchaseMinimumValue = $data['purchase_minimum_value'] ?? null;

        // Check if purchase minimum value is a positive number
        if (!is_numeric($purchaseMinimumValue) || $purchaseMinimumValue < 0) {
            $errors[] = $this->translator->trans(
                'Minimum purchase total required in order to validate the order',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        // If TOS option is enabled - check if the selected CMS page is valid
        if ($data['enable_tos']) {
            $tosCmsId = $data['tos_cms_id'];
            $tosCms = $this->cmsDataProvider->getCMSById($tosCmsId);

            if (!$tosCms->id) {
                $errors[] = [
                    'key' => 'Assign a valid page if you want it to be read.',
                    'domain' => 'Admin.Shopparameters.Notification',
                    'parameters' => [],
                ];
            }
        }

        return $errors;
    }
}

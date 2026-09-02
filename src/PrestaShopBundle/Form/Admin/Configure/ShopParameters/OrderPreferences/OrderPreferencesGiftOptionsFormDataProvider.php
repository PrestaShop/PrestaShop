<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderPreferences;

use PrestaShop\PrestaShop\Adapter\Order\GiftOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Order Settings" page.
 */
class OrderPreferencesGiftOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var GiftOptionsConfiguration
     */
    private $giftOptionsConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        GiftOptionsConfiguration $giftOptionsConfiguration,
        TranslatorInterface $translator
    ) {
        $this->giftOptionsConfiguration = $giftOptionsConfiguration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->giftOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // If gift wrapping tax rules group was not submitted - reset it to 0
        if (!isset($data['gift_wrapping_tax_rules_group'])) {
            $data['gift_wrapping_tax_rules_group'] = 0;
        }

        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->giftOptionsConfiguration->updateConfiguration($data);
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
        $giftWrappingPrice = $data['gift_wrapping_price'] ?? null;

        // Check if purchase minimum value is a positive number
        if (!empty($giftWrappingPrice) && (!is_numeric($giftWrappingPrice) || $giftWrappingPrice < 0)) {
            return [
                [
                    'key' => 'The %s field is invalid.',
                    'domain' => 'Admin.Notifications.Error',
                    'parameters' => [
                        $this->translator->trans(
                            'Gift-wrapping price',
                            [],
                            'Admin.Shopparameters.Feature'
                        ),
                    ],
                ],
            ];
        }

        return [];
    }
}

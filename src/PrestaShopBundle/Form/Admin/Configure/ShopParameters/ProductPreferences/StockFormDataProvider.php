<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Product\StockConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class StockFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var StockConfiguration
     */
    private $configuration;

    public function __construct(
        StockConfiguration $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->configuration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->configuration->updateConfiguration($data);
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
        $displayLastQuantities = $data['display_last_quantities'];
        if (!is_numeric($displayLastQuantities) || 0 > $displayLastQuantities) {
            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$this->translator->trans(
                    'Display remaining quantities when the quantity is lower than',
                    [],
                    'Admin.Shopparameters.Feature'
                )],
            ];
        }

        return $errors;
    }
}

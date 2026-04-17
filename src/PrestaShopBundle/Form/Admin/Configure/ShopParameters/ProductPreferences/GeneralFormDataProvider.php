<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Product\GeneralConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class GeneralFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var GeneralConfiguration
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        GeneralConfiguration $configuration,
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
        $invalidFields = [];

        $newDaysNumber = $data['new_days_number'];
        if (!is_numeric($newDaysNumber) || 0 > $newDaysNumber) {
            $invalidFields[] = $this->translator->trans(
                'Number of days for which the product is considered \'new\'',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $shortDescriptionLimit = $data['short_description_limit'];
        if (!is_numeric($shortDescriptionLimit) || 0 >= $shortDescriptionLimit) {
            $invalidFields[] = $this->translator->trans(
                'Max size of product summary',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $errors = [];
        foreach ($invalidFields as $field) {
            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$field],
            ];
        }

        return $errors;
    }
}

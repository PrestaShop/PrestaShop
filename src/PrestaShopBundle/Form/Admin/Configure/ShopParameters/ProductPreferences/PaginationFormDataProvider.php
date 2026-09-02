<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Product\PaginationConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PaginationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var PaginationConfiguration
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        PaginationConfiguration $configuration,
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
        $productsPerPage = $data['products_per_page'];
        if (!is_numeric($productsPerPage) || 0 >= $productsPerPage) {
            $errors[] = [
                'key' => 'The %s field is invalid. Please enter a positive integer.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$this->translator->trans('Products per page', [], 'Admin.Shopparameters.Feature')],
            ];
        }

        return $errors;
    }
}

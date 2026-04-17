<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MetaSettingsFormDataProvider is responsible for providing configurations data and responsible for persisting data
 * in configuration database.
 */
final class MetaSettingsShopUrlsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $shopUrlsDataConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Validate
     */
    private $validate;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param DataConfigurationInterface $shopUrlsDataConfiguration
     * @param TranslatorInterface $translator
     * @param Validate $validate
     */
    public function __construct(
        DataConfigurationInterface $shopUrlsDataConfiguration,
        TranslatorInterface $translator,
        Validate $validate
    ) {
        $this->shopUrlsDataConfiguration = $shopUrlsDataConfiguration;
        $this->translator = $translator;
        $this->validate = $validate;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->shopUrlsDataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $errors = $this->validateData($data);

        if (!empty($errors)) {
            return $errors;
        }

        return $this->shopUrlsDataConfiguration->updateConfiguration($data);
    }

    /**
     * Implements custom validation for configuration form.
     *
     * @param array $data
     *
     * @return array - if array is not empty then error strings are returned
     *
     * @throws PrestaShopException
     */
    private function validateData(array $data)
    {
        $errors = [];
        if (!$this->validate->isCleanHtml($data['domain'])) {
            $errors[] = $this->translator->trans(
                'This domain is not valid.',
                [],
                'Admin.Notifications.Error'
            );
        }

        if (!$this->validate->isCleanHtml($data['domain_ssl'])) {
            $errors[] = $this->translator->trans(
                'The SSL domain is not valid.',
                [],
                'Admin.Notifications.Error'
            );
        }

        return $errors;
    }
}

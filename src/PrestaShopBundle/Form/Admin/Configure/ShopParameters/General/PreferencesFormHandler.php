<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * This class manages the data manipulated using forms
 * in "Configure > Shop Parameters > General" page.
 */
final class PreferencesFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var PreferencesFormDataProvider
     */
    private $formDataProvider;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        FormFactoryInterface $formFactory,
        PreferencesFormDataProvider $formDataProvider,
        Configuration $configuration
    ) {
        $this->formFactory = $formFactory;
        $this->formDataProvider = $formDataProvider;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->formFactory->createBuilder()
            ->add('general', PreferencesType::class, [
                'is_ssl_enabled' => $this->configuration->getBoolean('PS_SSL_ENABLED'),
            ])
            ->setData($this->formDataProvider->getData())
            ->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        return $this->formDataProvider->setData($data);
    }
}

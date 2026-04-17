<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Webservice;

use PrestaShop\PrestaShop\Adapter\Webservice\WebserviceConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Advanced Parameters > Webservice" page.
 */
final class WebserviceFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var WebserviceConfiguration
     */
    private $webserviceConfiguration;

    /**
     * @param WebserviceConfiguration $webserviceConfiguration
     */
    public function __construct(WebserviceConfiguration $webserviceConfiguration)
    {
        $this->webserviceConfiguration = $webserviceConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->webserviceConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->webserviceConfiguration->updateConfiguration($data);
    }
}

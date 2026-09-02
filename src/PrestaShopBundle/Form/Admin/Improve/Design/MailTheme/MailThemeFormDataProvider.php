<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\MailTheme;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Design > Mail Theme" page.
 */
class MailThemeFormDataProvider implements FormDataProviderInterface
{
    /** @var DataConfigurationInterface */
    private $mailThemeConfiguration;

    public function __construct(DataConfigurationInterface $mailThemeConfiguration)
    {
        $this->mailThemeConfiguration = $mailThemeConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->mailThemeConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->mailThemeConfiguration->updateConfiguration($data);
    }
}

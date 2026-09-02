<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerPreferences;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShopBundle\Entity\Repository\TabRepository;

/**
 * Class manages "Configure > Shop Parameters > Customer Settings" page
 * form handling.
 */
final class CustomerPreferencesFormHandler extends Handler
{
    /**
     * @var TabRepository
     */
    private $tabRepository;

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        $errors = parent::save($data);

        if (empty($errors)) {
            $this->handleB2bUpdate($data['enable_b2b_mode']);
        }

        return $errors;
    }

    /**
     * @param TabRepository $tabRepository
     */
    public function setTabRepository(TabRepository $tabRepository)
    {
        $this->tabRepository = $tabRepository;
    }

    /**
     * Based on B2b mode, we need to enable/disable some tabs.
     *
     * @param bool $b2bMode Current B2B mode status
     *
     * @throws InvalidArgumentException
     */
    private function handleB2bUpdate($b2bMode)
    {
        $b2bTabs = ['AdminOutstanding'];
        foreach ($b2bTabs as $tabName) {
            $this->tabRepository->changeStatusByClassName($tabName, (bool) $b2bMode);
        }
    }
}

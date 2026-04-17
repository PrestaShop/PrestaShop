<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Login;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
    ) {
    }

    public function getData()
    {
        return [
            'email' => $this->authenticationUtils->getLastUsername(),
        ];
    }

    public function setData(array $data)
    {
        // Nothing to do the login mechanism is handled by the Symfony framework
        return [];
    }
}

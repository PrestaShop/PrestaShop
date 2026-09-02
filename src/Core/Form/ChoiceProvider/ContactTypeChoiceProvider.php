<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use Contact;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class ContactTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var int
     */
    private $langId;

    public function __construct(int $langId)
    {
        $this->langId = $langId;
    }

    public function getChoices(): array
    {
        return FormChoiceFormatter::formatFormChoices(
            Contact::getContacts($this->langId),
            'id_contact',
            'name'
        );
    }
}

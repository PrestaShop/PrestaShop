<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Group;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides choices for customer groups
 */
final class GroupByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct(int $contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        // Use FormChoiceFormatter so groups sharing the same name don't collapse
        // into a single choice (the name is used as the choice key by Symfony).
        return FormChoiceFormatter::formatFormChoices(
            Group::getGroups($this->contextLangId, true),
            'id_group',
            'name',
            false
        );
    }
}

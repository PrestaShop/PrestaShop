<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryDeleteModeChoiceProvider.
 */
final class CategoryDeleteModeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $associateOnlyLabel = $this->translator->trans(
            'If they have no other category, I want to associate them with the parent category.',
            [],
            'Admin.Catalog.Notification'
        );

        $associateAndDisableLabel = sprintf(
            '%s %s',
            $this->translator->trans(
                'If they have no other category, I want to associate them with the parent category and turn them offline.',
                [],
                'Admin.Catalog.Notification'
            ),
            $this->translator->trans('(Recommended)', [], 'Admin.Catalog.Notification')
        );

        $deleteProductLabel = $this->translator->trans(
            'If they have no other category, I want to delete them as well.',
            [],
            'Admin.Catalog.Notification'
        );

        return [
            $associateAndDisableLabel => CategoryDeleteMode::ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE,
            $associateOnlyLabel => CategoryDeleteMode::ASSOCIATE_PRODUCTS_WITH_PARENT_ONLY,
            $deleteProductLabel => CategoryDeleteMode::REMOVE_ASSOCIATED_PRODUCTS,
        ];
    }
}

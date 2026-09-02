<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Title\AbstractTitleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Title\Query\GetTitleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryHandler\GetTitleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryResult\EditableTitle;
use PrestaShop\PrestaShop\Core\Domain\Title\TitleSettings;

/**
 * Handles command that gets title for editing
 *
 * @internal
 */
#[AsQueryHandler]
class GetTitleForEditingHandler extends AbstractTitleHandler implements GetTitleForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetTitleForEditing $query): EditableTitle
    {
        $title = $this->titleRepository->get($query->getTitleId());

        $titleImage = _PS_GENDERS_DIR_ . $query->getTitleId()->getValue() . '.jpg';
        $titleWidth = $titleHeight = null;
        if (file_exists($titleImage)) {
            list($titleWidth, $titleHeight) = getimagesize($titleImage);
        }

        return new EditableTitle(
            $query->getTitleId()->getValue(),
            $title->name,
            (int) $title->type,
            $titleWidth ?: TitleSettings::DEFAULT_IMAGE_WIDTH,
            $titleHeight ?: TitleSettings::DEFAULT_IMAGE_HEIGHT
        );
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Admin\ImageConfiguration;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageSettingsForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageSettings;

/**
 * Handles command that gets image settings for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetImageSettingsForEditingHandler implements GetImageSettingsForEditingHandlerInterface
{
    public function __construct(
        private readonly ImageConfiguration $imageConfiguration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetImageSettingsForEditing $query): EditableImageSettings
    {
        $config = $this->imageConfiguration->getConfiguration();

        return new EditableImageSettings(
            $config['formats'],
            $config['base-format'],
            $config['avif-quality'],
            $config['jpeg-quality'],
            $config['png-quality'],
            $config['webp-quality'],
            $config['generation-method'],
            $config['picture-max-size'],
            $config['picture-max-width'],
            $config['picture-max-height']
        );
    }
}

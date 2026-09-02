<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain\ImageSettings;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageSettingsForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageSettings;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

class ImageSettingsContext extends AbstractDomainFeatureContext
{
    /**
     * @When I edit images settings with following properties:
     */
    public function editImageSettingsUsingCommand(TableNode $table)
    {
        $data = $this->fixDataType($table->getRowsHash());

        $command = new EditImageSettingsCommand();
        $command->setFormats($data['formats']);
        $command->setBaseFormat($data['base-format']);
        $command->setAvifQuality($data['avif-quality']);
        $command->setJpegQuality($data['jpeg-quality']);
        $command->setPngQuality($data['png-quality']);
        $command->setWebpQuality($data['webp-quality']);
        $command->setGenerationMethod($data['generation-method']);
        $command->setPictureMaxSize($data['picture-max-size']);
        $command->setPictureMaxWidth($data['picture-max-width']);
        $command->setPictureMaxHeight($data['picture-max-height']);

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When images settings should have the following properties:
     */
    public function assertImageSettingsProperties(TableNode $table)
    {
        $errors = [];
        $expectedData = $this->fixDataType($table->getRowsHash());

        /** @var EditableImageSettings $imageSettings */
        $imageSettings = $this->getQueryBus()->handle(new GetImageSettingsForEditing());

        if (isset($expectedData['formats'])) {
            if ($imageSettings->getFormats() !== $expectedData['formats']) {
                $errors[] = 'formats';
            }
        }

        if (isset($expectedData['base-format'])) {
            if ($imageSettings->getBaseFormat() !== $expectedData['base-format']) {
                $errors[] = 'base-format';
            }
        }

        if (isset($expectedData['avif-quality'])) {
            if ($imageSettings->getAvifQuality() !== $expectedData['avif-quality']) {
                $errors[] = 'avif-quality';
            }
        }

        if (isset($expectedData['jpeg-quality'])) {
            if ($imageSettings->getJpegQuality() !== $expectedData['jpeg-quality']) {
                $errors[] = 'jpeg-quality';
            }
        }

        if (isset($expectedData['png-quality'])) {
            if ($imageSettings->getPngQuality() !== $expectedData['png-quality']) {
                $errors[] = 'png-quality';
            }
        }

        if (isset($expectedData['webp-quality'])) {
            if ($imageSettings->getWebpQuality() !== $expectedData['webp-quality']) {
                $errors[] = 'webp-quality';
            }
        }

        if (isset($expectedData['generation-method'])) {
            if ($imageSettings->getGenerationMethod() !== $expectedData['generation-method']) {
                $errors[] = 'generation-method';
            }
        }

        if (isset($expectedData['picture-max-size'])) {
            if ($imageSettings->getPictureMaxSize() !== $expectedData['picture-max-size']) {
                $errors[] = 'picture-max-size';
            }
        }

        if (isset($expectedData['picture-max-width'])) {
            if ($imageSettings->getPictureMaxWidth() !== $expectedData['picture-max-width']) {
                $errors[] = 'picture-max-width';
            }
        }

        if (isset($expectedData['picture-max-height'])) {
            if ($imageSettings->getPictureMaxHeight() !== $expectedData['picture-max-height']) {
                $errors[] = 'picture-max-height';
            }
        }

        if (count($errors) > 0) {
            throw new RuntimeException(sprintf('Fields %s are not identical', implode(', ', $errors)));
        }
    }

    /**
     * Fix data properties.
     */
    private function fixDataType(array $data): array
    {
        // Cast to array for formats
        if (array_key_exists('formats', $data) && !is_null($data['formats'])) {
            $data['formats'] = explode(',', $data['formats']);
        }

        // Cast to int
        foreach (
            [
                'avif-quality',
                'jpeg-quality',
                'png-quality',
                'webp-quality',
                'generation-method',
                'picture-max-size',
                'picture-max-width',
                'picture-max-height',
            ] as $key) {
            if (array_key_exists($key, $data) && !is_null($data[$key])) {
                $data[$key] = intval($data[$key]);
            }
        }

        return $data;
    }
}

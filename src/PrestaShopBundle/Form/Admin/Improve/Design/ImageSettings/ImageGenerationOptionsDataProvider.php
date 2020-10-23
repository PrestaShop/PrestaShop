<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the image generation options data inside configuration form.
 */
final class ImageGenerationOptionsDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $imageGenerationOptionsConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param DataConfigurationInterface $imageGenerationOptionsConfiguration
     * @param TranslatorInterface $translator
     */
    public function __construct(
        DataConfigurationInterface $imageGenerationOptionsConfiguration,
        TranslatorInterface $translator
    ) {
        $this->imageGenerationOptionsConfiguration = $imageGenerationOptionsConfiguration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->imageGenerationOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): array
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->imageGenerationOptionsConfiguration->updateConfiguration($data);
    }

    /**
     * Performs validations of submitted data.
     *
     * @param array $data
     *
     * @return array
     */
    private function validate(array $data): array
    {
        $errors = [];

        $jpegQuality = $data['jpeg_quality'];
        if (!is_numeric($jpegQuality) || $jpegQuality < 0 || $jpegQuality > 100) {
            $fieldName = $this->translator->trans('JPEG compression', [], 'Admin.Design.Feature');

            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$fieldName],
            ];
        }

        $pngQuality = $data['png_quality'];
        if (!is_numeric($pngQuality) || $pngQuality < 0 || $pngQuality > 9) {
            $fieldName = $this->translator->trans('PNG compression', [], 'Admin.Design.Feature');

            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$fieldName],
            ];
        }

        // When multistore context isn't Shop::CONTEXT_ALL, then these will be not submitted
        $productPictureMaxSize = $data['product_picture_max_size'] ?? null;
        if (!is_null($productPictureMaxSize) && (!is_numeric($productPictureMaxSize) || $productPictureMaxSize < 0)) {
            $fieldName = $this->translator->trans('Maximum file size of product customization pictures', [], 'Admin.Design.Feature');

            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$fieldName],
            ];
        }

        $productPictureWidth = $data['product_picture_width'] ?? null;
        if (!is_null($productPictureWidth) && (!is_numeric($productPictureWidth) || $productPictureWidth < 0)) {
            $fieldName = $this->translator->trans('Product picture width', [], 'Admin.Design.Feature');

            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$fieldName],
            ];
        }

        $productPictureHeight = $data['product_picture_height'] ?? null;
        if (!is_null($productPictureHeight) && (!is_numeric($productPictureHeight) || $productPictureHeight < 0)) {
            $fieldName = $this->translator->trans('Product picture height', [], 'Admin.Design.Feature');

            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$fieldName],
            ];
        }

        return $errors;
    }
}

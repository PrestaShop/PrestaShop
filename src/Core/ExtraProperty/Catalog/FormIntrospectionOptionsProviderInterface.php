<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

/**
 * Supplies the form options FormFieldTreeProvider needs to build a form type that declares
 * REQUIRED options (e.g. EditProductFormType requires product_id/shop_id/product_type) — such
 * forms cannot be introspected with an empty options array and would fall back to the free-text
 * path input.
 *
 * Implementations are tagged "core.extra_property.form_introspection_options_provider" and only
 * feed the throwaway introspection build: sample values are enough as long as the form BUILDS
 * (no data is ever set, no form is submitted). A provider that turns out to be wrong degrades
 * gracefully — the build failure is caught and the form reads as not introspectable, exactly as
 * if the provider did not exist.
 */
interface FormIntrospectionOptionsProviderInterface
{
    public function supports(string $formId, string $formTypeClass): bool;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(string $formId, string $formTypeClass): array;
}

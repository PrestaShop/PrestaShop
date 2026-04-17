<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This function allows updating a form vars from the twig, the new vars are merged with the existing ones.
 * Used in ToggleChildrenChoiceType to force invalid state on the radio children.
 *
 * Example: {{ update_form_vars(form, {valid: false}) }}
 */
class UpdateFormVarsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'update_form_vars',
                [$this, 'updateFormVars']
            ),
        ];
    }

    public function updateFormVars(FormView $formView, array $newVars): void
    {
        $formView->vars = array_merge($formView->vars, $newVars);
    }
}

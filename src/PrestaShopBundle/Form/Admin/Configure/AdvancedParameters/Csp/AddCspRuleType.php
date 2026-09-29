<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Csp;

use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/** Form for the "Add allowed source" action: add a source to the allow-list without waiting for it to be reported. */
final class AddCspRuleType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('directive', ChoiceType::class, [
                'choices' => $this->getDirectiveChoices(),
                'required' => true,
                'label' => $this->trans('Directive', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('The CSP directive the source is allowed for, e.g. script-src.', 'Admin.Advparameters.Help'),
            ])
            ->add('source', TextType::class, [
                'required' => true,
                'label' => $this->trans('Source', 'Admin.Advparameters.Feature'),
                'help' => $this->trans("A CSP source expression: a host (https://cdn.example.com), a scheme (data:), or a keyword ('self', 'unsafe-inline').", 'Admin.Advparameters.Help'),
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validateSource']),
                ],
            ]);
    }

    /**
     * @param mixed $value
     */
    public function validateSource($value, ExecutionContextInterface $context): void
    {
        if (!is_string($value) || '' === trim($value)) {
            return;
        }

        try {
            new CspSource($value);
        } catch (CspConstraintException) {
            $context->buildViolation(
                $this->trans('This is not a valid CSP source expression.', 'Admin.Advparameters.Feature')
            )->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'csp_add_rule';
    }

    /**
     * @return array<string, string> directive label => value (the directive name serves as both)
     */
    private function getDirectiveChoices(): array
    {
        $choices = [];
        foreach (CspDirective::cases() as $directive) {
            // Granular script-/style- variants are curated at the parent level only.
            if ($directive !== $directive->coarsen()) {
                continue;
            }
            // The base policy sets every fetch directive explicitly,
            // so a rule on default-src/child-src would be a no-op.
            if (CspDirective::DEFAULT_SRC === $directive || CspDirective::CHILD_SRC === $directive) {
                continue;
            }
            $choices[$directive->value] = $directive->value;
        }

        return $choices;
    }
}

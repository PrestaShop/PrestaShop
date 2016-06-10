<?php
use Symfony\Component\Translation\TranslatorInterface;

class CustomerFormatterCore implements FormFormatterInterface
{
    private $translator;
    private $language;

    private $ask_for_birthdate              = true;
    private $ask_for_newsletter             = true;
    private $ask_for_partner_optin          = true;
    private $ask_for_password               = true;
    private $password_is_required           = true;
    private $ask_for_new_password           = false;

    public function __construct(
        TranslatorInterface $translator,
        Language $language
    ) {
        $this->translator = $translator;
        $this->language = $language;
    }

    public function setAskForBirthdate($ask_for_birthdate)
    {
        $this->ask_for_birthdate = $ask_for_birthdate;
        return $this;
    }

    public function setAskForNewsletter($ask_for_newsletter)
    {
        $this->ask_for_newsletter = $ask_for_newsletter;
        return $this;
    }

    public function setAskForPartnerOptin($ask_for_partner_optin)
    {
        $this->ask_for_partner_optin = $ask_for_partner_optin;
        return $this;
    }

    public function setAskForPassword($ask_for_password)
    {
        $this->ask_for_password = $ask_for_password;
        return $this;
    }

    public function setAskForNewPassword($ask_for_new_password)
    {
        $this->ask_for_new_password = $ask_for_new_password;
        return $this;
    }

    public function setPasswordRequired($password_is_required)
    {
        $this->password_is_required = $password_is_required;
        return $this;
    }

    public function getFormat()
    {
        $format = [];

        $format['id_customer'] = (new FormField)
            ->setName('id_customer')
            ->setType('hidden')
        ;

        $genderField = (new FormField)
            ->setName('id_gender')
            ->setType('radio-buttons')
            ->setLabel(
                $this->translator->trans(
                    'Social title', [], 'Customer'
                )
            )
        ;
        foreach (Gender::getGenders($this->language->id) as $gender) {
            $genderField->addAvailableValue($gender->id, $gender->name);
        }
        $format[$genderField->getName()] = $genderField;

        $format['firstname'] = (new FormField)
            ->setName('firstname')
            ->setLabel(
                $this->translator->trans(
                    'First name', [], 'Customer'
                )
            )
            ->setRequired(true)
        ;

        $format['lastname'] = (new FormField)
            ->setName('lastname')
            ->setLabel(
                $this->translator->trans(
                    'Last name', [], 'Customer'
                )
            )
            ->setRequired(true)
        ;

        $format['email'] = (new FormField)
            ->setName('email')
            ->setType('email')
            ->setLabel(
                $this->translator->trans(
                    'Email', [], 'Customer'
                )
            )
            ->setRequired(true)
        ;

        if ($this->ask_for_password) {
            $format['password'] = (new FormField)
                ->setName('password')
                ->setType('password')
                ->setLabel(
                    $this->translator->trans(
                        'Password', [], 'Customer'
                    )
                )
                ->setRequired($this->password_is_required)
            ;
        }

        if ($this->ask_for_new_password) {
            $format['new_password'] = (new FormField)
                ->setName('new_password')
                ->setType('password')
                ->setLabel(
                    $this->translator->trans(
                        'New password', [], 'Customer'
                    )
                )
            ;
        }

        if ($this->ask_for_birthdate) {
            $format['birthday'] = (new FormField)
                ->setName('birthday')
                ->setType('date')
                ->setLabel(
                    $this->translator->trans(
                        'Birthdate', [], 'Customer'
                    )
                )
                ->addAvailableValue('placeholder', Tools::getDateFormat())
                ->addAvailableValue(
                    'comment',
                    $this->translator->trans('(Ex.: %date_comment)', array('%date_comment' => Tools::formatDateStr('31 May 1970')), 'Shop.Forms.Help')
                )
            ;
        }

        if ($this->ask_for_newsletter) {
            $format['newsletter'] = (new FormField)
                ->setName('newsletter')
                ->setType('checkbox')
                ->setLabel(
                    $this->translator->trans(
                        'Sign up for our newsletter', [], 'Customer'
                    )
                )
            ;
        }

        if ($this->ask_for_partner_optin) {
            $format['optin'] = (new FormField)
                ->setName('optin')
                ->setType('checkbox')
                ->setLabel(
                    $this->translator->trans(
                        'Receive offers from our partners', [], 'Customer'
                    )
                )
            ;
        }

        // TODO: TVA etc.?

        return $this->addConstraints($format);
    }

    private function addConstraints(array $format)
    {
        $constraints = Customer::$definition['fields'];

        foreach ($format as $field) {
            if (!empty($constraints[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $constraints[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }
}

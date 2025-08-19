# Fix: Social Title Display in Customer Forms

## Issue
When all social titles (genders) are deleted from the shop, the "Social title" field still appears in the customer form in the back office, even though there are no options to select.

## Fix
Added a condition in `CustomerType.php` to check if there are any gender choices available before adding the field to the form, similar to how it's already implemented in the front office.

## Technical Details
Modified file: `src/PrestaShopBundle/Form/Admin/Sell/Customer/CustomerType.php`

Added code to check if gender choices exist before adding the field:
```php
$genderChoices = $this->genderByIdChoiceProvider->getChoices();
if (!empty($genderChoices)) {
    $builder->add('gender_id', ChoiceType::class, [
        'choices' => $genderChoices,
        'required' => false,
        'label' => $this->trans('Social title', 'Admin.Global'),
        'placeholder' => $this->trans('--', 'Admin.Actions'),
    ]);
}
```

## Screenshots
### Before Fix
[Insert screenshot showing the social title field displayed even when no titles exist]

### After Fix
[Insert screenshot showing the social title field no longer appearing when no titles exist]

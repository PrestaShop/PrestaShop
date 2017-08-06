<!-- Thank you for contributing to the PrestaShop project! 

Please take the time to edit the "Answers" rows with the necessary information: -->

| Questions     | Answers
| ------------- | -------------------------------------------------------
| Branch?       | Develop
| Description?  | CustomerAddressFormCore validation was not working fine with hooks provided by modules for custom validation.
| Type?         | bug fix
| Category?     | CO
| BC breaks?    | NO
| Deprecations? | NO
| Fixed ticket? | 
| How to test?  | Create a module with ActionValidateCustomerAddressForm hook that validate one of the form fields. at the submit will be possible to proceed to addresses page which shows the error message set in context-controller.

<!-- Click the form's "Preview button" to make sure the table is functional in GitHub. Thank you! -->

# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-product
@restore-products-before-feature
@update-product
# @todo: this feature could be used to update more properties together, but I think its not worth putting extracting all the existing features into this one,
#         because it will be too hard to manage, there are too many of them
#Feature: Update product properties from Back Office (BO) in a single shop context
#  As a BO user
#  I need to be able to update product properties from BO

/* global describe, it */

import * as checkout from '../helpers/checkout';

describe.only("The Checkout Process", function () {

    describe("when the customer doesn't have an account", function () {

        it('should show the account creation form');

        describe("and chooses to order as guest", function () {

            it('should allow the customer not to write a password');

            customerHasNoAddresses();
        });

        describe("and chooses to create an account", function () {

            it('should allow the customer to write a password');

            customerHasNoAddresses();
        });

    });

    describe("when the customer already has an account", function () {

        function customerAlreadyHasAnAddress () {
            describe("and already has an address", function () {

                describe("and uses the same address for delivery and invoice", function () {
                    proceedToShipping();
                });

                describe("and uses a different address for delivery and invoice", function () {
                    proceedToShipping();
                });

                describe("and uses a different, new address for invoice", function () {
                    proceedToShipping();
                });

            });
        }

        function proceedToAddresses () {
            customerHasNoAddresses();
            customerAlreadyHasAnAddress();
        }

        describe("and is already logged-in", function () {

            it("should show step 2 directly");

            proceedToAddresses();
        });

        describe("and has not yet logged-in", function () {

            it("should log the customer in");

            proceedToAddresses();
        });

    });

});

function customerHasNoAddresses () {
    describe("and the customer doesn't have an address", function () {
        it("should not show any addresses");
        it("should show the delivery address form");

        describe("and the customer uses the same address for delivery and invoice", function () {
            it("should show an unchecked checkbox allowing to setup a different address");
            proceedToShipping();
        });

        describe("and the customer uses a different address for invoice", function () {
            it("should allow adding another address for invoice");
            proceedToShipping();
        });
    });
}

function proceedToShipping () {

    it("should show delivery options");

    proceedToPayment();
}

function proceedToPayment () {

    it("should show payment options");
    it("should show a checkbox to accept the terms and conditions");
    it("should have a disabled order button");

    describe("proceeds to payment directly", function () {
        confirmOrder();
    });

    describe("but changes their firstname before paying", function () {
        confirmOrder();
    });

    describe("but edits their delivery address before paying", function () {
        confirmOrder();
    });
}

function confirmOrder () {
    describe("the customer pays", function () {

    });

    describe("the customer pays but there is an error", function () {

    });
}

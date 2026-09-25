<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

// OrderException and InvoiceException carry their wording in the message itself, and
// OrderController::getErrorMessages() renders it with $this->trans($e->getMessage()), so the parser
// never sees the literal at the call site and the merchant always reads these in English.
// Referencing them here is what makes them extractable. Domain: Admin.Orderscustomers.Notification.
trans('An error occurred during payment.', 'Admin.Orderscustomers.Notification');
trans('An error occurred during the CartRule creation', 'Admin.Orderscustomers.Notification');
trans('An error occurred when trying to get info for order processing', 'Admin.Orderscustomers.Notification');
trans('An error occurred while adding CartRule to cart', 'Admin.Orderscustomers.Notification');
trans('An error occurred while editing the product line.', 'Admin.Orderscustomers.Notification');
trans('Can\'t load Currency object', 'Admin.Orderscustomers.Notification');
trans('Can\'t load Order Invoice object', 'Admin.Orderscustomers.Notification');
trans('Cannot add product to shipped order.', 'Admin.Orderscustomers.Notification');
trans('Cart linked to the order cannot be found.', 'Admin.Orderscustomers.Notification');
trans('Could not delete customization from database.', 'Admin.Orderscustomers.Notification');
trans('Could not delete order cart rule from database.', 'Admin.Orderscustomers.Notification');
trans('Could not find the product in cart, meaning Order and Cart are out of sync', 'Admin.Orderscustomers.Notification');
trans('Could not update order invoice in database.', 'Admin.Orderscustomers.Notification');
trans('Delivered order cannot be modified.', 'Admin.Orderscustomers.Notification');
trans('Failed to add order.', 'Admin.Orderscustomers.Notification');
trans('Invalid cart provided.', 'Admin.Orderscustomers.Notification');
trans('Invalid order cart rule provided.', 'Admin.Orderscustomers.Notification');
trans('Invalid order invoice id supplied.', 'Admin.Orderscustomers.Notification');
trans('Invalid price', 'Admin.Orderscustomers.Notification');
trans('Invalid quantity', 'Admin.Orderscustomers.Notification');
trans('Invoice management has been disabled.', 'Admin.Orderscustomers.Notification');
trans('New delivery address is not valid', 'Admin.Orderscustomers.Notification');
trans('New invoice address is not valid', 'Admin.Orderscustomers.Notification');
trans('Order detail could not be found.', 'Admin.Orderscustomers.Notification');
trans('Order detail does not belong to order.', 'Admin.Orderscustomers.Notification');
trans('Product combination not found.', 'Admin.Orderscustomers.Notification');
trans('The Order Detail object could not be loaded.', 'Admin.Orderscustomers.Notification');
trans('The invoice is invalid.', 'Admin.Orderscustomers.Notification');
trans('The invoice note was not saved.', 'Admin.Orderscustomers.Notification');
trans('The invoice object cannot be loaded.', 'Admin.Orderscustomers.Notification');
trans('The order carrier ID is invalid.', 'Admin.Orderscustomers.Notification');
trans('The order carrier cannot be updated.', 'Admin.Orderscustomers.Notification');
trans('The order has already been assigned this status.', 'Admin.Orderscustomers.Notification');
trans('The order object cannot be loaded.', 'Admin.Orderscustomers.Notification');
trans('The selected currency is invalid.', 'Admin.Orderscustomers.Notification');
trans('The tracking number is incorrect.', 'Admin.Orderscustomers.Notification');
trans('This order already has an invoice.', 'Admin.Orderscustomers.Notification');
trans('You cannot change the currency.', 'Admin.Orderscustomers.Notification');
trans('You cannot edit the order detail for this order.', 'Admin.Orderscustomers.Notification');
trans('You cannot generate a partial credit slip.', 'Admin.Orderscustomers.Notification');
trans('You cannot generate a voucher.', 'Admin.Orderscustomers.Notification');
trans('You cannot use this invoice for the order', 'Admin.Orderscustomers.Notification');

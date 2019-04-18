<?php

namespace Laravel\Cashier\Tests\Integration;

use Stripe\Charge;

class ChargesTest extends IntegrationTestCase
{
    public function test_customer_can_be_charged()
    {
        $user = $this->createCustomer('customer_can_be_charged');
        $user->createAsStripeCustomer();
        $user->updateCard($this->getTestToken());

        $response = $user->charge(1000);

        $this->assertInstanceOf(Charge::class, $response);
        $this->assertEquals(1000, $response->amount);
    }

    public function test_customer_can_be_charged_and_invoiced_immediately()
    {
        $user = $this->createCustomer('customer_can_be_charged_and_invoiced_immediately');
        $user->createAsStripeCustomer();
        $user->updateCard($this->getTestToken());

        $user->invoiceFor('Laravel Cashier', 1000);

        $invoice = $user->invoices()[0];
        $this->assertEquals('$10.00', $invoice->total());
        $this->assertEquals('Laravel Cashier', $invoice->invoiceItems()[0]->asStripeInvoiceItem()->description);
    }

    public function test_customer_can_be_refunded()
    {
        $user = $this->createCustomer('customer_can_be_refunded');
        $user->createAsStripeCustomer();
        $user->updateCard($this->getTestToken());

        $invoice = $user->invoiceFor('Laravel Cashier', 1000);
        $refund = $user->refund($invoice->charge);

        $this->assertEquals(1000, $refund->amount);
    }
}

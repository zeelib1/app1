<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Price;
use Stripe\Exception\InvalidRequestException;
use Stripe\Customer;

class StripeController extends Controller
{
    public function checkout()
    {
        $user = Auth::user();
        $priceId = env('STRIPE_PRICE_ID');

        if (!$priceId) {
            throw new \Exception('Stripe Price ID is not set in the environment variables.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // If user doesn't have a Stripe ID, create a new customer
            if (!$user->stripe_id) {
                $stripeCustomer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_id = $stripeCustomer->id;
                $user->save();
            } else {
                // Verify if the customer exists in Stripe
                try {
                    Customer::retrieve($user->stripe_id);
                } catch (InvalidRequestException $e) {
                    // If customer doesn't exist in Stripe, create a new one
                    $stripeCustomer = Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                    ]);
                    $user->stripe_id = $stripeCustomer->id;
                    $user->save();
                }
            }

            return $user->checkout([$priceId => 1], [
                'customer' => $user->stripe_id,
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred during checkout. Please try again.']);
        }
    }

    public function success()
    {
        return 'Payment successful!';
    }

    public function cancel()
    {
        return 'Payment cancelled.';
    }

    public function verifyStripeConfig()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $price = Price::retrieve(env('STRIPE_PRICE_ID'));
            return "Price found: " . $price->id . " - " . $price->unit_amount/100 . " " . strtoupper($price->currency);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

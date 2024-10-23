<template>
    <div>
        <button
            @click="initiateCheckout"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            Checkout
        </button>
    </div>
</template>

<script>
import { loadStripe } from "@stripe/stripe-js";

export default {
    name: "Checkout",
    data() {
        return {
            stripe: null,
        };
    },
    async mounted() {
        this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);
    },
    methods: {
        async initiateCheckout() {
            try {
                const response = await fetch("/create-checkout-session", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                });

                const session = await response.json();

                const result = await this.stripe.redirectToCheckout({
                    sessionId: session.id,
                });

                if (result.error) {
                    console.error(result.error.message);
                }
            } catch (error) {
                console.error("Error:", error);
            }
        },
    },
};
</script>

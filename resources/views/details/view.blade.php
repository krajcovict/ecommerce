<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Details') }}
        </h2>
    </x-slot>
    <div x-data="{
        flashMessage: '{{ \Illuminate\Support\Facades\Session::get('flash_message') }}',
        init() {
            if (this.flashMessage) {
                setTimeout(() => this.$dispatch('notify', { message: this.flashMessage }), 300);
            }
        }
        }" class="container p-5 mx-auto lg:w-2/3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            <div class="bg-white p-3 shadow rounded-lg md:col-span-2">
                <form x-data="{
                    countries: {{ json_encode($countries) }},
                    billingAddress: {{ json_encode([
                        'address1' => old('billingAddress.address1', $billingAddress->address1),
                        'address2' => old('billingAddress.address2', $billingAddress->address2),
                        'city' => old('billingAddress.city', $billingAddress->city),
                        'state' => old('billingAddress.state', $billingAddress->state),
                        'country_code' => old('billingAddress.country_code', $billingAddress->country_code),
                        'zipcode' => old('billingAddress.zipcode', $billingAddress->zipcode),
                    ]) }},
                    shippingAddress: {{ json_encode([
                        'address1' => old('shippingAddress.address1', $shippingAddress->address1),
                        'address2' => old('shippingAddress.address2', $shippingAddress->address2),
                        'city' => old('shippingAddress.city', $shippingAddress->city),
                        'state' => old('shippingAddress.state', $shippingAddress->state),
                        'country_code' => old('shippingAddress.country_code', $shippingAddress->country_code),
                        'zipcode' => old('shippingAddress.zipcode', $shippingAddress->zipcode),
                    ]) }},
                    get billingCountryStates() {
                        const country = this.countries.find(c => c.code === this.billingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states);
                        }
                        return null;
                    },
                    get shippingCountryStates() {
                        const country = this.countries.find(c => c.code === this.shippingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states);
                        }
                        return null;
                    },
                }"
                action="{{ route('details.update') }}" method="post">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">Name and Contact Information</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="text"
                            name="first_name"
                            value="{{ old('first_name', $customer->first_name) }}"
                            placeholder="First Name"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name', $customer->last_name) }}"
                            placeholder="Last Name"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="mb-3">
                        <x-input
                            type="text"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Your Email"
                            class="mb-3 w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $customer->phone) }}"
                            placeholder="Your Phone"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>

                    <h2 class="text-xl font-semibold mb-2 mt-6">Billing Address</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="text"
                            name="billingAddress[address1]"
                            x-model="billingAddress.address1"
                            placeholder="Address 1"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="billingAddress[address2]"
                            x-model="billingAddress.address2"
                            placeholder="Address 2"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="text"
                            name="billingAddress[city]"
                            x-model="billingAddress.city"
                            placeholder="City"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="billingAddress[zipcode]"
                            x-model="billingAddress.zipcode"
                            placeholder="ZipCode"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="select"
                            name="billingAddress[country_code]"
                            x-model="billingAddress.country_code"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        >
                            <option value="">Select Country</option>
                            <template x-for="country of countries" :key="country.code">
                                <option :selected="country.code === billingAddress.country_code"
                                    :value="country.code" x-text="country.name"></option>
                            </template>
                        </x-input>
                        <template x-if="billingCountryStates">
                            <x-input
                                type="select"
                                name="billingAddress[state]"
                                x-model="billingAddress.state"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            >
                                <option value="">Select State</option>
                                <template x-for="[code, state] of Object.entries(billingCountryStates)"
                                    :key="code"
                                >
                                    <option :selected="code === billingAddress.state"
                                        :value="code" x-text="state"></option>
                                </template>
                            </x-input>
                        </template>
                        <template x-if="!billingCountryStates">
                            <x-input
                                type="text"
                                name="billingAddress[state]"
                                x-model="billingAddress.state"
                                placeholder="State"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </template>
                    </div>

                    <div class="flex justify-between mt-6 mb-2 mr-3">
                        <h2 class="text-xl font-semibold mb-2">Shipping Address</h2>
                        <label for="sameAsBillingAddress" class="text-gray-700">
                            <input @change="event.target.checked ? shippingAddress = {...billingAddress} : ''"
                                id="sameAsBillingAddress" type="checkbox"
                                class="text-purple-600 focus:ring-purple-600 mr-2"
                            > Same as Billing
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="text"
                            name="shippingAddress[address1]"
                            x-model="shippingAddress.address1"
                            placeholder="Address 1"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="shippingAddress[address2]"
                            x-model="shippingAddress.address2"
                            placeholder="Address 2"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="text"
                            name="shippingAddress[city]"
                            x-model="shippingAddress.city"
                            placeholder="City"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                        <x-input
                            type="text"
                            name="shippingAddress[zipcode]"
                            x-model="shippingAddress.zipcode"
                            placeholder="ZipCode"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-input
                            type="select"
                            name="shippingAddress[country_code]"
                            x-model="shippingAddress.country_code"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                        >
                            <option value="">Select Country</option>
                            <template x-for="country of countries" :key="country.code">
                                <option :selected="country.code === shippingAddress.country_code"
                                    :value="country.code" x-text="country.name"></option>
                            </template>
                        </x-input>
                        <template x-if="shippingCountryStates">
                            <x-input
                                type="select"
                                name="shippingAddress[state]"
                                x-model="shippingAddress.state"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            >
                                <option value="">Select State</option>
                                <template x-for="[code, state] of Object.entries(shippingCountryStates)"
                                    :key="code"
                                >
                                    <option :selected="code === shippingAddress.state"
                                        :value="code" x-text="state"></option>
                                </template>
                            </x-input>
                        </template>
                        <template x-if="!shippingCountryStates">
                            <x-input
                                type="text"
                                name="shippingAddress[state]"
                                x-model="shippingAddress.state"
                                placeholder="State"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded"
                            />
                        </template>
                    </div>

                    <x-primary-button class="w-full mt-4">
                        {{ __('Update') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

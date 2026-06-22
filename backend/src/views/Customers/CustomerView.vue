<template>
    <div v-if="!customer">
        <Spinner class="my-4 w-full"/>
    </div>
    <div v-else class="animate-fade-in-down">
        <form @submit.prevent="onSubmit">
            <div class="bg-white px-4 pt-5 pb-4">
                <h1 class="text-xl font-bold mt-5 pb-2 border-b border-gray-300">
                    {{ title }}
                </h1>
                <CustomInput class="mb-2" v-model="customer.first_name" label="First Name" />
                <CustomInput class="mb-2" v-model="customer.last_name" label="Last Name" />
                <CustomInput class="mb-2" v-model="customer.email" label="Email" />
                <CustomInput class="mb-2" v-model="customer.phone" label="Phone" />
                <CustomInput class="mb-2 h-4 w-4" type="checkbox" v-model="customer.status" label="Active" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <h2 class="text-xl font-semibold mt-5 pb-2 border-b border-gray-300">Shipping Address</h2>
                        <CustomInput class="mb-2" v-model="customer.shippingAddress.address1" label="Address 1" />
                        <CustomInput class="mb-2" v-model="customer.shippingAddress.address2" label="Address 2" />
                        <CustomInput class="mb-2" v-model="customer.shippingAddress.city" label="City" />
                        <CustomInput class="mb-2" v-model="customer.shippingAddress.zipcode" label="Zip Code" />
                        <CustomInput type="select" :select-options="countries" class="mb-2" v-model="customer.shippingAddress.country_code" label="Country" />
                        <CustomInput v-if="shippingCountry && !shippingCountry.states" class="mb-2" v-model="customer.shippingAddress.state" label="State" />
                        <CustomInput v-else type="select" :select-options="shippingStateOptions" class="mb-2" v-model="customer.shippingAddress.state" label="State" />
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold mt-5 pb-2 border-b border-gray-300">Billing Address</h2>
                        <CustomInput class="mb-2" v-model="customer.billingAddress.address1" label="Address 1" />
                        <CustomInput class="mb-2" v-model="customer.billingAddress.address2" label="Address 2" />
                        <CustomInput class="mb-2" v-model="customer.billingAddress.city" label="City" />
                        <CustomInput class="mb-2" v-model="customer.billingAddress.zipcode" label="Zip Code" />
                        <CustomInput type="select" :select-options="countries" class="mb-2" v-model="customer.billingAddress.country_code" label="Country" />
                        <CustomInput v-if="billingCountry && !billingCountry.states" class="mb-2" v-model="customer.billingAddress.state" label="State" />
                        <CustomInput v-else type="select" :select-options="billingStateOptions" class="mb-2" v-model="customer.billingAddress.state" label="State" />
                    </div>
                </div>
            </div>
            <footer class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <button type="submit"
                class="flex flex-col py-2 px-4 sm:ml-3 border border-gray-300 rounded-md bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 mt-3"
                >
                    Submit
                </button>
                <router-link :to="{name: 'app.customers'}" type="button"
                class="flex flex-col py-2 px-4 border border-gray-300 rounded-md shadow-sm hover:bg-[rgba(0,0,0,0.2)] mt-3 text-center"
                ref="cancelButtonRef"
                >
                    Cancel
                </router-link>
                </div>
            </footer>
        </form>
    </div>
</template>

<script setup>
import Spinner from '../../components/core/Spinner.vue';
import { ref, onMounted, computed } from "vue";
import store from "../../store";
import { useRoute, useRouter } from "vue-router";
import CustomInput from '../../components/core/CustomInput.vue';

const router = useRouter();
const route = useRoute();
const title = ref('');
const customer = ref({
  billingAddress: {},
  shippingAddress: {}
})

const loading = ref(false)

const countries = computed(() => store.state.countries.map(c => ({ key: c.code, text: c.name })))

const shippingCountry = computed(() => store.state.countries.find(c => c.code === customer.value.shippingAddress.country_code))
const shippingStateOptions = computed(() => {
    if (!shippingCountry.value || !shippingCountry.value.states) return [];
    return Object.entries(shippingCountry.value.states).map(c => ({key: c[0], text: c[1]}))
})

const billingCountry = computed(() => store.state.countries.find(c => c.code === customer.value.billingAddress.country_code))
const billingStateOptions = computed(() => {
    if (!billingCountry.value || !billingCountry.value.states) return [];
    return Object.entries(billingCountry.value.states).map(c => ({key: c[0], text: c[1]}))
})

function onSubmit() {
    loading.value = true
    if (customer.value.id) {
    customer.value.status = !!customer.value.status // conversion to boolean
        store.dispatch('updateCustomer', customer.value)
            .then(response => {
                loading.value = false;
                if (response.status === 200) {
                    // TODO show notification
                    store.dispatch('getCustomers')
                    router.push({name: 'app.customers'})
                }
        })
    } else {
        store.dispatch('createCustomer', customer.value)
            .then(response => {
                loading.value = false
                if (response.status === 201) {
                    // TODO show notification
                    store.dispatch('getCustomers')
                    router.push({name: 'app.customers'})
            }
        })
    }
}

onMounted(() => {
    store.dispatch("getCustomer", route.params.id)
        .then(({ data }) => {
            title.value = `Update customer: "${data.first_name} ${data.last_name}"`
            customer.value = {
              ...data,
              shippingAddress: data.shippingAddress || {},
              billingAddress: data.billingAddress || {}
            }
        })
});

</script>

<style scoped>
</style>

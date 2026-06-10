<template>
    <h1 class="text-3xl font-semibold">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <template v-if="!loading.customersCount">
                <label>Active Customers</label>
                <span class="text-3xl font-semibold">{{ customersCount }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <template v-if="!loading.productsCount">
                <label>Active Products</label>
                <span class="text-3xl font-semibold">{{ productsCount }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <template v-if="!loading.paidOrders">
                <label>Paid Orders</label>
                <span class="text-3xl font-semibold">{{ paidOrders }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <template v-if="!loading.totalIncome">
                <label>Total Income</label>
                <span class="text-2xl font-semibold">{{ totalIncome }}</span>
            </template>
            <Spinner v-else/>
        </div>
    </div>
    <div class="grid grid-rows-1 md:grid-rows-2 md:grid-flow-col grid-cols-1 md:grid-cols-3 gap-3 py-3">
        <div class="md:col-span-2 md:row-span-2 bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <label>Orders</label>
            <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro reprehenderit quod nostrum aut? Iusto fuga iure dolore ea esse placeat, veritatis laborum modi omnis quos, sunt, ipsum deleniti dolorum dolorem.</span>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <DoughnutChart :width="140" :height="200" />
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <label>Customers</label>
        </div>
    </div>
</template>

<script setup>
import Spinner from '../components/core/Spinner.vue';
import axiosClient from '../axios.js';
import DoughnutChart from '../components/core/Charts/Doughnut.vue';
import { ref } from 'vue';

const loading = ref({
    customersCount: true,
    productsCount: true,
    paidOrders: true,
    totalIncome: true,
})

const customersCount = ref(0);
const productsCount = ref(0);
const paidOrders = ref(0);
const totalIncome = ref(0);

axiosClient.get(`/dashboard/customers-count`).then(({ data }) => {
    customersCount.value = data
    loading.value.customersCount = false
    });
axiosClient.get(`/dashboard/products-count`).then(({ data }) => {
    productsCount.value = data
    loading.value.productsCount = false
    });
axiosClient.get(`/dashboard/orders-count`).then(({ data }) => {
    paidOrders.value = data
    loading.value.paidOrders = false
    });
axiosClient.get(`/dashboard/income-amount`).then(({data}) => {
    totalIncome.value =
        new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", roundingMode: "halfCeil", maximumFractionDigits: 0 })
            .format(data,),
    loading.value.totalIncome = false
    });
</script>

<style scoped>

</style>

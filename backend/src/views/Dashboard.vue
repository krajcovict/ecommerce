<template>
    <h1 class="text-3xl font-semibold">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 py-3">
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <template v-if="!loading.customersCount">
                <label class="font-semibold">Active Customers</label>
                <span class="text-3xl font-semibold">{{ customersCount }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <template v-if="!loading.productsCount">
                <label class="font-semibold">Active Products</label>
                <span class="text-3xl font-semibold">{{ productsCount }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <template v-if="!loading.paidOrders">
                <label class="font-semibold">Paid Orders</label>
                <span class="text-3xl font-semibold">{{ paidOrders }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <template v-if="!loading.totalIncome">
                <label class="font-semibold">Total Income</label>
                <span class="text-2xl font-semibold">{{ totalIncome }}</span>
            </template>
            <Spinner v-else/>
        </div>
    </div>
    <div class="grid grid-rows-1 md:grid-rows-2 md:grid-flow-col grid-cols-1 md:grid-cols-3 gap-3 py-3">
        <div class="md:col-span-2 md:row-span-2 bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <label class="font-semibold">Orders</label>
            <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro reprehenderit quod nostrum aut? Iusto fuga iure dolore ea esse placeat, veritatis laborum modi omnis quos, sunt, ipsum deleniti dolorum dolorem.</span>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <template v-if="!loading.ordersByCountry">
                <label class="font-semibold">Orders By Country</label>
                <div>
                    <DoughnutChart :width="140" :height="200" :data="ordersByCountry" />
                </div>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <label class="font-semibold">Latest Customers</label>
            <router-link to="/" v-for="c of latestCustomers" :key="c.id" class="flex border border-gray-300 rounded p-3 m-1 w-full hover:bg-gray-200">
                <UserIcon class="w-4" />
                <div class="pl-3">
                    <h3>{{ c.first_name }} {{ c.last_name }}</h3>
                    <p>{{ c.email }}</p>
                </div>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import Spinner from '../components/core/Spinner.vue';
import axiosClient from '../axios.js';
import DoughnutChart from '../components/core/Charts/Doughnut.vue';
import { UserIcon } from '@heroicons/vue/24/solid';
import { ref } from 'vue';

const loading = ref({
    customersCount: true,
    productsCount: true,
    paidOrders: true,
    totalIncome: true,
    ordersByCountry: true,
    latestCustomers: true,
})

const customersCount = ref(0);
const productsCount = ref(0);
const paidOrders = ref(0);
const totalIncome = ref(0);
const ordersByCountry = ref([]);
const latestCustomers = ref([]);

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
axiosClient.get(`/dashboard/orders-by-country`).then(({ data: countries }) => {
    const chartData = {
        labels: [],
        datasets: [{
            backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16'],
            data: []
        }]
    }
    countries.forEach(c => {
        chartData.labels.push(c.name)
        chartData.datasets[0].data.push(c.count);
    })
    ordersByCountry.value = chartData
    loading.value.ordersByCountry = false
});
axiosClient.get(`/dashboard/latest-customers`).then(({data: customers}) => {
    latestCustomers.value = customers;
    loading.value.latestCustomers = false;
});

</script>

<style scoped>

</style>

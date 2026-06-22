<template>
    <div class="mb-2 flex items-center justify-between">
        <h1 class="text-3xl font-semibold mr-2">Dashboard</h1>
        <div class="flex items-center">
            <label class="mr-2">Change Date Period </label>
            <CustomInput type="select" v-model="chosenDate" @change="onDatePickerChange" :select-options="dateOptions"/>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 py-3">
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center animate-fade-in-down" style="animation-delay: 0.2s;">
            <template v-if="!loading.customersCount">
                <label class="font-semibold">Active Customers</label>
                <span class="text-3xl font-semibold">{{ customersCount }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center animate-fade-in-down" style="animation-delay: 0.3s;">
            <template v-if="!loading.productsCount">
                <label class="font-semibold">Active Products</label>
                <span class="text-3xl font-semibold">{{ productsCount }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center animate-fade-in-down" style="animation-delay: 0.4s;">
            <template v-if="!loading.paidOrders">
                <label class="font-semibold">Paid Orders</label>
                <span class="text-3xl font-semibold">{{ paidOrders }}</span>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center animate-fade-in-down" style="animation-delay: 0.5s;">
            <template v-if="!loading.totalIncome">
                <label class="font-semibold">Total Income</label>
                <span class="text-2xl font-semibold">{{ totalIncome }}</span>
            </template>
            <Spinner v-else/>
        </div>
    </div>
    <div class="grid grid-rows-1 lg:grid-rows-2 lg:grid-flow-col grid-cols-1 lg:grid-cols-3 gap-3 py-3">
        <div class="md:col-span-2 md:row-span-2 bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center animate-fade-in-down" style="animation-delay: 0.7s;">
            <label class="font-semibold">Latest Paid Orders</label>
            <template v-if="!loading.latestOrders">
                <router-link :to="{ name: 'app.orders.view', params: {id: o.id} }" v-for="o of latestOrders" :key="o.id" class="py-2 px-3 border border-gray-300 rounded m-1 w-full hover:bg-gray-200">
                    <p>Order <b>#{{ o.id }}</b> contains {{ o.items }} items - ${{ o.total_price }}</p>
                    <p class="flex justify-between"><span>{{ o.first_name }} {{ o.last_name }}</span><span>{{ o.created_at }}</span></p>
                </router-link>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center animate-fade-in-down" style="animation-delay: 0.8s;">
            <label class="font-semibold">Orders By Country</label>
            <template v-if="!loading.ordersByCountry">
                <div>
                    <DoughnutChart :width="140" :height="200" :data="ordersByCountry" />
                </div>
            </template>
            <Spinner v-else/>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center animate-fade-in-down"  style="animation-delay: 1s;">
            <label class="font-semibold">Latest Customers</label>
            <template v-if="!loading.latestCustomers">
                <router-link :to="{name: 'app.customers.view', params: {id: c.id}}" v-for="c of latestCustomers" :key="c.id" class="flex border border-gray-300 rounded py-2 px-3 m-1 w-full hover:bg-gray-200 items-center">
                    <UserIcon class="w-4" />
                    <div class="pl-3">
                        <h3>{{ c.first_name }} {{ c.last_name }}</h3>
                        <p>{{ c.email }}</p>
                    </div>
                </router-link>
            </template>
            <Spinner v-else/>
        </div>
    </div>
</template>

<script setup>
import Spinner from '../components/core/Spinner.vue';
import axiosClient from '../axios.js';
import CustomInput from '../components/core/CustomInput.vue';
import DoughnutChart from '../components/core/Charts/Doughnut.vue';
import { UserIcon } from '@heroicons/vue/24/solid';
import { ref, onMounted } from 'vue';

const loading = ref({
    customersCount: true,
    productsCount: true,
    paidOrders: true,
    totalIncome: true,
    latestOrders: true,
    ordersByCountry: true,
    latestCustomers: true,
})

const dateOptions = ref([
    { key: '1d', text: 'Last Day' },
    { key: '1w', text: 'Last Week' },
    { key: '2w', text: 'Last 2 Weeks' },
    { key: '1m', text: 'Last Month' },
    { key: '3m', text: 'Last 3 Months' },
    { key: '6m', text: 'Last 6 Months' },
    { key: 'all', text: 'All Time' },
])
const chosenDate = ref('all');

const customersCount = ref(0);
const productsCount = ref(0);
const paidOrders = ref(0);
const totalIncome = ref(0);
const latestOrders = ref([]);
const ordersByCountry = ref([]);
const latestCustomers = ref([]);

function updateDashboard() {
    const d = chosenDate.value;

    loading.value = {
        customersCount: true,
        productsCount: true,
        paidOrders: true,
        totalIncome: true,
        latestOrders: true,
        ordersByCountry: true,
        latestCustomers: true,
    }
    axiosClient.get(`/dashboard/customers-count`, {params: {d}}).then(({ data }) => {
        customersCount.value = data
        loading.value.customersCount = false
    });
    axiosClient.get(`/dashboard/products-count`, {params: {d}}).then(({ data }) => {
        productsCount.value = data
        loading.value.productsCount = false
    });
    axiosClient.get(`/dashboard/orders-count`, {params: {d}}).then(({ data }) => {
        paidOrders.value = data
        loading.value.paidOrders = false
    });
    axiosClient.get(`/dashboard/income-amount`, {params: {d}}).then(({data}) => {
        totalIncome.value =
            new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", roundingMode: "halfCeil", maximumFractionDigits: 0 })
                .format(data,),
        loading.value.totalIncome = false
    });
    axiosClient.get(`/dashboard/latest-orders`).then(({ data: orders }) => {
        latestOrders.value = orders.data
        loading.value.latestOrders = false
    });
    axiosClient.get(`/dashboard/orders-by-country`, {params: {d}}).then(({ data: countries }) => {
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
}

function onDatePickerChange(val) {
    updateDashboard(val)
}

onMounted(() => updateDashboard())

</script>

<style scoped>

</style>

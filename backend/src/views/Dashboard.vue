<template>
    <h1 class="text-3xl font-semibold">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <label>Active Customers</label>
            <span class="text-3xl font-semibold">15</span>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <label>Active Products</label>
            <span class="text-3xl font-semibold">15</span>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center justify-center">
            <label>Paid Orders</label>
            <span class="text-3xl font-semibold">15</span>
        </div>
        <div class="bg-white p-5 rounded-lg border border-gray-400 shadow-lg flex flex-col items-center">
            <label>Total Income</label>
            <span class="text-2xl font-semibold">$28,003,562</span>
        </div>
    </div>
    <div>
        <DoughnutChart />
    </div>
</template>

<script setup>
import DoughnutChart from '../components/core/Charts/Doughnut.vue';

</script>

<style scoped>

</style>

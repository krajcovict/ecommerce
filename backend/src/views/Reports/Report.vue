<template>
    <div>
        <div class="my-2">
            <router-link :to="{name: 'reports.orders'}" class="py-2 px-3 text-gray-700 rounded-md mr-2"
                active-class="bg-indigo-500 text-white">
                Orders Report
            </router-link>
            <router-link :to="{name: 'reports.customers'}" class="py-2 px-3 text-gray-700 rounded-md"
                active-class="bg-indigo-500 text-white">
                Customers Report
            </router-link>
        </div>
        <div class="p-4 rounded-lg border border-gray-400 shadow-lg">
            <router-view/>
        </div>
    </div>

</template>

<script setup>
</script>

<template>
    <div>
        <div class="my-2">
            <router-link :to="{name: 'reports.orders'}" class="bg-gray-200 py-2 px-3 text-gray-700 rounded-t-lg ml-1 mr-2"
                active-class="bg-indigo-500 text-white outline-3 outline-indigo-500">
                Orders Report
            </router-link>
            <router-link :to="{name: 'reports.customers'}" class="bg-gray-200 py-2 px-3 text-gray-700 rounded-t-lg"
                active-class="bg-indigo-500 text-white outline-3 outline-indigo-500">
                Customers Report
            </router-link>
        </div>
        <div class="p-4 rounded-lg rounded-tl-none border border-gray-400 shadow-lg">
            <router-view/>
        </div>
    </div>

</template>

<script setup>
</script>

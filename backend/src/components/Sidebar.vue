<template>
  <div class="w-[200px] bg-indigo-700 rounded-l-lg text-gray-200 py-4 px-2">
    <router-link :to="{ name: 'app.dashboard' }"
    class="flex items-center py-2 px-3 rounded hover:bg-black/30 transition-colors duration-600 mb-2">
        <span><HomeIcon class="w-5 mr-2.5"/></span>
        <span class="text-sm">
            Dashboard
        </span>
    </router-link>
    <router-link :to="{ name: 'app.dashboard' }"
    class="flex items-center py-2 px-3 rounded hover:bg-black/30 transition-colors duration-600 mb-2">
        <span><SquaresPlusIcon class="w-5 mr-2.5"/></span>
        <span class="text-sm">
            Products
        </span>
    </router-link>
    <router-link :to="{ name: 'app.dashboard' }"
    class="flex items-center py-2 px-3 rounded hover:bg-black/30 transition-colors duration-600 mb-2">
        <span><UserIcon class="w-5 mr-2.5"/></span>
        <span class="text-sm">
            Users
        </span>
    </router-link>
    <router-link :to="{ name: 'app.dashboard' }"
    class="flex items-center py-2 px-3 rounded hover:bg-black/30 transition-colors duration-600 mb-2">
        <span><ChartBarSquareIcon class="w-5 mr-2.5"/></span>
        <span class="text-sm">
            Reports
        </span>
    </router-link>
  </div>
</template>

<script setup>
import {HomeIcon, SquaresPlusIcon, UserIcon, ChartBarSquareIcon } from '@heroicons/vue/24/outline'
</script>

<style scoped>

</style>

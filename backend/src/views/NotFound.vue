<template>

    <div class="flex justify-center items-center h-full">
        <div class="flex">
            <div class="text-indigo-600 text-5xl font-bold px-4 border-r-2 border-gray-300 leading-9">404</div>
        <div class="px-4">
            <h1 class="font-bold text-5xl mb-4 leading-9">Page not found</h1>
            <p class="text-gray-600 mb-6">Please check the url in address bar and try again</p>
            <router-link :to="{name: 'app.dashboard'}">
                <button
                class="flex justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Go back home
                </button>
            </router-link>
        </div>
        </div>
    </div>


</template>

<script setup>


</script>

<style scoped>

</style>

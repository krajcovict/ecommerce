<template>
    <span class="text-xs text-white py-1 px-2 rounded"
    :class="{
        'bg-emerald-500': order.status === 'paid',
        'bg-gray-400': order.status === 'unpaid',
        'bg-red-600': order.status === 'cancelled',
        'bg-blue-600': order.status === 'shipped',
        'bg-gray-700': order.status === 'completed',
    }">
        {{ order.status }}
    </span>
</template>
<script setup>
const { order } = defineProps({
    order: Object
})
</script>
<style>

</style>

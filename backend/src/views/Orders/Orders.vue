<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold mx-2">Orders</h1>
    </div>
    <OrdersTable @clickView="viewOrder"/>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import store from "../../store";
import OrdersTable from "./OrdersTable.vue";

const orders = computed(() => store.state.orders);

function showOrder(p) {

}

</script>

<style scoped>
</style>

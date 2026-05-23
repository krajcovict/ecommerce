<template>
    {{ order }}
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import store from "../../store";
import OrdersTable from "./OrdersTable.vue";
import { useRoute } from "vue-router";

const route = useRoute();

onMounted(() => {
    store.dispatch("getOrder", route.params.id)
});



</script>

<style scoped>
</style>

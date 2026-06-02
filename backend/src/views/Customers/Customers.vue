<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">Customers</h1>
    </div>
    <CustomerModal v-model="showModal" :customer="customerModel" @close="onModalClose" />
    <CustomersTable @clickEdit="editCustomer" />

</template>

<script setup>
import CustomerModal from './CustomerModal.vue';
import CustomersTable from './CustomersTable.vue';
import { ref } from 'vue';
import store from '../../store/index.js';

const DEFAULT_EMPTY_OBJECT = {}

const showModal = ref(false);
const customerModel = ref({...DEFAULT_EMPTY_OBJECT});

function showCustomerModal() {
    showModal.value = true;
}

function editCustomer(c) {
  store.dispatch('getCustomer', c.id)
    .then(({data}) => {
      customerModel.value = data;
        showCustomerModal()
    })
}

function onModalClose() {
    customerModel.value = {...DEFAULT_EMPTY_OBJECT}
}

</script>

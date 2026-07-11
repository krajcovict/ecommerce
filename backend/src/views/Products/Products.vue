<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold mx-2">Products</h1>
        <router-link
            :to="{name: 'app.products.create'}"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Add new Product
        </router-link>
    </div>
    <ProductModal v-model="showModal" :product="productModel" @close="onModalClose" />
    <ProductsTable @clickEdit="editProduct" />

</template>

<script setup>
import ProductModal from './ProductModal.vue';
import ProductsTable from './ProductsTable.vue';
import { ref } from 'vue';
import store from '../../store';

const DEFAULT_EMPTY_OBJECT = {
    id: '',
    title: '',
    image: '',
    description: '',
    price: '',
}

const showModal = ref(false);
const productModel = ref({...DEFAULT_EMPTY_OBJECT});

function editProduct(product) {
    store.dispatch('getProduct', product.id)
        .then(({ data }) => {
            productModel.value = data
        })
}

function onModalClose() {
    productModel.value = {...DEFAULT_EMPTY_OBJECT}
}

</script>

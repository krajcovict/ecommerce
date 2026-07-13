<template>
    <div class="flex items-center justify-between mb-3">
        <h1 v-if="!loading" class="text-3xl pl-2 font-semibold">
          {{ product.id ? `Update product: "${product.title}"` : 'Create new Product' }}
        </h1>
    </div>
    <div class="bg-white rounded-lg shadow animate-fade-in-down">
        <Spinner v-if="loading" class="absolute place-self-center flex items-center justify-center"/>
        <form v-if="!loading" @submit.prevent="onSubmit">
            <div class="bg-white px-4 pt-5 pb-4">
                <CustomInput class="mb-2" v-model="product.title" label="ProductTitle" />
                <CustomInput type="file" class="mb-2" label="Product Image" @change="onImageChange" />
                <CustomInput type="textarea" class="mb-2" v-model="product.description" label="Description" />
                <CustomInput type="number" class="mb-2" v-model="product.price" label="price" prepend="$" />
                <CustomInput type="checkbox" class="mb-2 h-4 w-4" v-model="product.published" label="Published" />
            </div>
            <footer class="bg-gray-50 rounded-b-lg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    @click="onSubmit($event, true)"
                    class="mt-3 w-full inline-flex justify-center sm:ml-3 border border-gray-300 rounded-md bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 sm:mt-0 sm:w-auto px-4 py-2"
                    >
                    Save & Close
                </button>
                <button type="submit"
                    class="mt-3 w-full inline-flex justify-center sm:ml-3 border border-gray-300 rounded-md bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 sm:mt-0 sm:w-auto px-4 py-2"
                    >
                    Save
                </button>
                <router-link :to="{name: 'app.products'}"
                class="mt-3 w-full inline-flex justify-center border border-gray-300 rounded-md shadow-sm hover:bg-[rgba(0,0,0,0.2)] sm:mt-0 sm:ml-3 sm:w-auto px-4 py-2"
                ref="cancelButtonRef"
                >
                    Cancel
                </router-link>
            </footer>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import store from '../../store/index.js';
import Spinner from '../../components/core/Spinner.vue';
import CustomInput from '../../components/core/CustomInput.vue';
import { useRoute, useRouter } from 'vue-router';

const loading = ref(false)
const router = useRouter();
const route = useRoute()

const product = ref({
    id: null,
    title: null,
    image: null,
    description: null,
    price: null,
    published: false,
})

const emit = defineEmits(['update:modelValue'])

onMounted(() => {
    if (route.params.id) {
        loading.value = true;
        store.dispatch('getProduct', route.params.id)
            .then((response) => {
                product.value = response.data
                loading.value = false
        })
    }
})

function onSubmit($event, close = false) {
    loading.value = true
    if (product.value.id) {
        store.dispatch('updateProduct', product.value)
            .then(response => {
                loading.value = false;
                if (response.status === 200) {
                    store.commit('showToast', 'Product has been updated.');
                    store.dispatch('getProducts')
                    if (close) {
                        router.push({name: 'app.products'})
                    }

                }
        })
    } else {
        store.dispatch('createProduct', product.value)
            .then(response => {
                loading.value = false
                if (response.status === 201) {
                    store.commit('showToast', 'Product has been created.');
                    store.dispatch('getProducts')
                    if (close) {
                        router.push({name: 'app.products'})
                    } else {
                        product.value = response.data
                        router.push({name: 'app.products.edit', params: {id: response.data.id}})
                    }
                }
        })
    }
}

function onImageChange(file) {
    product.value.image = file || null
}
</script>

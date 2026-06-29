<template>
    <div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex flex-nowrap">
                <div class="text-nowrap mr-2">
                    <router-link :to="{name: 'reports.orders', params: route.params}" class="bg-gray-200 py-2 px-3 text-gray-700 rounded-t-lg ml-1 mr-2"
                        active-class="bg-indigo-500 text-white outline-3 outline-indigo-500">
                        Orders Report
                    </router-link>
                </div>
                <div class="text-nowrap">
                    <router-link :to="{name: 'reports.customers', params: route.params}" class="bg-gray-200 py-2 px-3 text-gray-700 rounded-t-lg"
                        active-class="bg-indigo-500 text-white outline-3 outline-indigo-500">
                        Customers Report
                    </router-link>
                </div>
            </div>
            <div class="flex items-center my-2 md:my-0">
                <label class="mr-2">Change Date Period </label>
                <CustomInput type="select" v-model="chosenDate" @change="onDatePickerChange" :select-options="dateOptions"/>
            </div>
        </div>
        <div class="p-4 rounded-lg rounded-tl-none border border-gray-400 shadow-lg">
            <router-view/>
        </div>
    </div>

</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';
import CustomInput from '../../components/core/CustomInput.vue';
import { ref, computed } from 'vue';
import { useStore } from "vuex";

const store = useStore();
const router = useRouter();
const route = useRoute();
const dateOptions = computed(() => store.state.dateOptions)
const chosenDate = ref('all');

function onDatePickerChange(val) {
    router.push({name: route.name, params: {date: chosenDate.value}})
}

</script>

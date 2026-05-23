<template>
    <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
        <div class="flex justify-between border-b-2 pb-3">
            <div class="flex items-center">
                <span class="whitespace-nowrap mr-3">
                    Per Page
                </span>
                <select @change="getOrders(null)" v-model="perPage"
                class="appearance-none relative block w-12 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="ml-3">Found {{ orders.total }} orders</span>
            </div>
            <div>
                <input v-model="search" @change="getOrders(null)"
                class="appearance-none relative block w-24 px-2 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Search">
            </div>
        </div>
        <div class="">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <TableHeaderCell @click="sortOrder" class="border-b-2 p-2 text-left" field="id"
                        :sort-field="sortField" :sort-direction="sortDirection">ID</TableHeaderCell>
                        <TableHeaderCell @click="sortOrder" class="border-b-2 p-2 text-left text-xs" field="status"
                        :sort-field="sortField" :sort-direction="sortDirection">Status</TableHeaderCell>
                        <TableHeaderCell @click="sortOrder" class="border-b-2 p-2 text-left" field="created_at"
                        :sort-field="sortField" :sort-direction="sortDirection">Date</TableHeaderCell>
                        <TableHeaderCell @click="sortOrder" class="border-b-2 p-2 text-left" field="total_price"
                        :sort-field="sortField" :sort-direction="sortDirection">Total Price</TableHeaderCell>
                        <TableHeaderCell @click="sortOrder" class="border-b-2 p-2 text-left" field="number_of_items"
                        :sort-field="sortField" :sort-direction="sortDirection"><UserCircleIcon class="size-5"/></TableHeaderCell>
                        <TableHeaderCell field="actions"></TableHeaderCell>
                    </tr>
                </thead>
                <tbody v-if="orders.loading || !orders.data.length">
                    <tr>
                        <td colspan="6">
                            <Spinner class="my-4 w-full"/>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else class="bg-white">
                    <tr v-for="(order, index) of orders.data" :key="order.id">
                        <td class="border-b p-2">{{ order.id }}</td>
                        <td class="border-b p-2 animate-fade-in-down" :style="{ animationDelay: `${index * 0.05}s` }">
                            <span class="text-xs">{{ order.status }}</span>
                        </td>
                        <td class="border-b p-2 max-w-[180px] text-pretty overflow-hidden text-ellipsis">
                            {{ order.created_at }}
                        </td>
                        <td class="border-b p-2">{{ order.total_price }}</td>
                        <td class="border-b p-2">{{ order.customer.id }}</td>
                        <td class="border-b p-2">
                            <router-link :to="{ name: 'app.orders.view', params: { id: order.id } }"
                                class="w-8 h-6 rounded-full border-1 border-indigo-700 text-indigo-700 flex justify-center items-center hover:text-white hover:bg-indigo-700">
                                <EyeIcon class="size-5"/>
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!orders.loading" class="flex justify-between items-center mt-5">
                <span>
                    Showing from {{ orders.from }} to {{ orders.to }} of {{ orders.total }} orders
                </span>
                <nav v.if="orders.total > orders.limit"
                    class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
                    aria-label="Pagination">
                    <a v-for="(link, i) of orders.links"
                    :key="i"
                    :disabled="!link.url"
                    aria-current="page"
                    href="#"
                    @click.prevent="getForPage($event, link)"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
                    :class="[
                        link.active
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        !link.url ? 'pointer-events-none opacity-50' : '',
                        i === 0 ? 'rounded-l-md' : '',
                        i === orders.links.length -1 ? 'rounded-r-md' : ''
                    ]"
                    v-html="link.label"
                    >
                    </a>
                </nav>
            </div>
        </div >
    </div >

</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import Spinner from '../../components/core/Spinner.vue';
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import store from '../../store/index.js';
import { ORDERS_PER_PAGE } from '../../constants.js';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { EllipsisVerticalIcon, TrashIcon, PencilSquareIcon, UserCircleIcon, EyeIcon } from '@heroicons/vue/20/solid'

const emit = defineEmits(['clickEdit'])

const perPage = ref(ORDERS_PER_PAGE);
const search = ref('');
const orders = computed(() => store.state.orders);
const sortField = ref('created_at');
const sortDirection = ref('desc');

onMounted(() => {
    getOrders();
});

function getOrders(url = null) {
    store.dispatch('getOrders', {
        url,
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
        search: search.value,
        perPage: perPage.value,
    });
};

function getForPage(ev, link) {
    if (!link.url || link.active) {
        return;
    };
    getOrders(link.url);
}

function sortOrder(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    getOrders();
}

function showOrder(order) {
    emit('clickShow', order)
}

</script>

<template>
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between border-b-2 pb-3">
            <div class="flex items-center">
                <span class="whitespace-nowrap mr-3">
                    Per Page
                </span>
                <select @change="getCustomers(null)" v-model="perPage"
                class="appearance-none block w-12 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div>
                <input v-model="search" @change="getCustomers(null)"
                class="appearance-none block w-32 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Type to Search">
            </div>
        </div>
        <div>
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <TableHeaderCell @click="sortCustomer" class="border-b-2 p-2 text-left" field="id"
                            :sort-field="sortField" :sort-direction="sortDirection">ID</TableHeaderCell>
                        <TableHeaderCell @click="sortCustomer" class="border-b-2 p-2 text-left" field="name"
                            :sort-field="sortField" :sort-direction="sortDirection">Name</TableHeaderCell>
                        <TableHeaderCell @click="sortCustomer" class="border-b-2 p-2 text-left" field="email"
                            :sort-field="sortField" :sort-direction="sortDirection">Email</TableHeaderCell>
                        <TableHeaderCell @click="sortCustomer" class="border-b-2 p-2 text-left" field="phone"
                            :sort-field="sortField" :sort-direction="sortDirection">Phone</TableHeaderCell>
                        <TableHeaderCell @click="sortCustomer" class="border-b-2 p-2 text-left" field="status"
                            :sort-field="sortField" :sort-direction="sortDirection">Status</TableHeaderCell>
                        <TableHeaderCell @click="sortCustomer" class="border-b-2 p-2 text-left" field="created_at"
                            :sort-field="sortField" :sort-direction="sortDirection">Registration Date</TableHeaderCell>
                        <TableHeaderCell field="actions"></TableHeaderCell>
                    </tr>
                </thead>
                <tbody v-if="customers.loading">
                    <tr>
                        <td colspan="7">
                            <Spinner class="my-4 w-full"/>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else-if="!customers.data.length">
                    <tr>
                        <td colspan="7">
                            <p class="text-center py-8">There are no customers.</p>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else class="bg-white">
                    <tr v-for="(customer, index) of customers.data" :key="customer.id">
                        <td class="border-b p-2">{{ customer.id }}</td>
                        <td class="border-b p-2 animate-fade-in-down" :style="{ animationDelay: `${index * 0.1}s` }">
                            {{ customer.first_name }} {{ customer.last_name }}
                        </td>
                        <td class="border-b p-2 max-w-[200px] text-pretty overflow-hidden text-ellipsis">
                            {{ customer.email }}
                        </td>
                        <td class="border-b text-pretty p-2">{{ customer.phone }}</td>
                        <td class="border-b text-pretty p-2">{{ customer.status }}</td>
                        <td class="border-b text-pretty p-2">{{ customer.created_at }}</td>
                        <td class="border-b p-2">
                            <Menu as="div" class="relative inline-block text-left">
                                <div>
                                  <MenuButton
                                    class="inline-flex z-0 w-full justify-center rounded-md bg-black/20 px-1 py-2 text-sm font-medium text-white hover:bg-black/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
                                  >
                                    <EllipsisVerticalIcon
                                      class="h-5 w-5 text-gray-900 hover:text-indigo-400"
                                      aria-hidden="true"
                                    />
                                  </MenuButton>
                                </div>
                                <transition
                                    enter-active-class="transition duration-100 ease-out"
                                    enter-from-class="transform scale-95 opacity-0"
                                    enter-to-class="transform scale-100 opacity-100"
                                    leave-active-class="transition duration-75 ease-in"
                                    leave-from-class="transform scale-100 opacity-100"
                                    leave-to-class="transform scale-95 opacity-0"
                                ></transition>
                                <MenuItems
                                  class="absolute z-10 right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                                >

                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active }">
                                          <router-link
                                            :to="{name: 'app.customers.view', params: {id: customer.id}}"
                                            :class="[
                                              active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                              'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                            ]"
                                          >
                                            <PencilSquareIcon
                                              :active="active"
                                              class="mr-2 h-5 w-5 text-indigo-400"
                                              aria-hidden="true"
                                            />
                                            Edit
                                          </router-link>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                          <button
                                            :class="[
                                              active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                              'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                            ]"
                                            @click="deleteCustomer(customer)"
                                          >
                                            <TrashIcon
                                              :active="active"
                                              class="mr-2 h-5 w-5 text-indigo-400"
                                              aria-hidden="true"
                                            />
                                            Delete
                                          </button>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </Menu>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!customers.loading" class="flex justify-between items-center mt-5">
                <span>
                    Showing from {{ customers.from }} to {{ customers.to }} of {{ customers.total }} customers
                </span>
                <nav v.if="customers.total > customers.limit"
                    class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
                    aria-label="Pagination">
                    <a v-for="(link, i) of customers.links"
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
                        i === customers.links.length -1 ? 'rounded-r-md' : ''
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
import { CUSTOMERS_PER_PAGE } from '../../constants.js';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { EllipsisVerticalIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/20/solid'

const emit = defineEmits(['clickEdit'])

const perPage = ref(CUSTOMERS_PER_PAGE);
const search = ref('');
const customers = computed(() => store.state.customers);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

onMounted(() => {
    getCustomers();
});

function getCustomers(url = null) {
    store.dispatch('getCustomers', {
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
    getCustomers(link.url);
}

function sortCustomer(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    getCustomers();
}

function deleteCustomer(customer) {
    if (!confirm(`Are you sure you want to delete the customer?`)) {
        return
    }
    store.dispatch('deleteCustomer', customer)
        .then(res => {
            // TODO show notification
            store.dispatch('getCustomers')
        })
}

</script>

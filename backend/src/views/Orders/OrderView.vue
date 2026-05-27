<template>
  <div v-if="!order">
    <Spinner class="my-4 w-full"/>
  </div>
  <div v-else>
    <table class="table-sm m-3 mb-10">
        <tbody>
          <tr>
            <td class="font-bold px-2 py-1">Order # </td>
            <td class="flex justify-between"> {{ order.id }} <OrderStatus :order="order" /></td>
          </tr>
          <tr>
            <td class="font-bold px-2 py-1">Order Date </td>
            <td> {{ order.created_at }}</td>
          </tr>
          <tr>
            <td class="font-bold px-2 py-1">Status </td>
            <td>
                <select v-model="order.status" @change="onStatusChange">
                    <option v-for="status of orderStatuses" :value="status">{{status}}</option>
                </select>
            </td>
          </tr>
          <tr>
            <td class="font-bold px-2 py-1">SubTotal </td>
            <td>${{ order.total_price }}</td>
          </tr>
        </tbody>
    </table>

    <h2 class="text-xl font-semibold m-3">Customer</h2>
    <table class="table-sm m-3 mb-10">
        <tbody>
          <tr>
            <td class="font-bold px-2 py-1">Full Name</td>
            <td> {{ order.customer.first_name }} {{ order.customer.last_name }}</td>
          </tr>
          <tr>
            <td class="font-bold px-2 py-1">Email</td>
            <td> {{ order.customer.email }}</td>
          </tr>
          <tr>
            <td class="font-bold px-2 py-1">Phone </td>
            <td>{{ order.customer.phone }}</td>
          </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-1 md:grid-cols-2">
        <div>
            <h2 class="text-xl font-semibold m-3">Shipping Address</h2>
            <div class="m-3">{{ order.customer.shippingAddress.address1 }},
            {{ order.customer.shippingAddress.address2 }} <br>
            {{ order.customer.shippingAddress.city }},
            {{ order.customer.shippingAddress.zipcode }} <br>
            {{ order.customer.shippingAddress.state }},
            {{ order.customer.shippingAddress.country }} <br>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold m-3">Billing Address</h2>
            <div class="m-3">{{ order.customer.billingAddress.address1 }},
            {{ order.customer.billingAddress.address2 }} <br>
            {{ order.customer.billingAddress.city }},
            {{ order.customer.billingAddress.zipcode }} <br>
            {{ order.customer.billingAddress.state }},
            {{ order.customer.billingAddress.country }} <br>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-semibold m-3">Order Items</h2>

    <div v-for="item in order.items" class="flex gap-6 pb-2 m-3">
      <a href="#"
        class="w-16 h-16 flex items-center justify-center overflow-hidden border border-gray-300">
        <img :src="item.product.image" class="object-cover" alt="" />
      </a>
      <div class="flex-1 flex flex-col justify-between pb-3">
        <h3 class="text-ellipsis mb-4">
          {{ item.product.title }}
        </h3>
      </div>
      <div class="flex flex-col justify-between items-end pb-3 w-32">
        <div class="text-lg mb-4">${{ item.unit_price }}</div>
      </div>
      <div class="flex flex-col justify-between items-end pb-3 w-32">
        <div class="text-lg mb-4">Quantity: {{ item.quantity }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Spinner from '../../components/core/Spinner.vue';
import { ref, onMounted, computed } from "vue";
import store from "../../store";
import OrdersTable from "./OrdersTable.vue";
import { useRoute } from "vue-router";
import axiosClient from "../../axios";
import OrderStatus from './OrderStatus.vue';

const route = useRoute();

const order = ref(null);
const orderStatuses = ref([]);

onMounted(() => {
    store.dispatch("getOrder", route.params.id)
        .then(({ data }) => {
            order.value = data
        })

    axiosClient.get('/orders/statuses')
        .then(({ data }) => orderStatuses.value = data)
});

function onStatusChange() {
    axiosClient.post(`/orders/change-status/${order.value.id}/${order.value.status}`)
        .then(({ data }) => {
            store.commit('showToast', `Order status was updated to "${order.value.status}" successfully.`);
            //console.log('status updated');
        })
};

</script>

<style scoped>
</style>

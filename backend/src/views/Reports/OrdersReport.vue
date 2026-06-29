<template>
    <div class="h-100">
        <BarChart :data="chartData"/>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import axiosClient from '../../axios.js';
import BarChart from '../../components/core/Charts/Bar.vue';
import { ref, watch } from 'vue';

const route = useRoute();

const chartData = ref({
  labels: [],
  datasets: [{ data: [] }]
})

watch(route, (rt) => {
    getData();
}, {immediate: true})

function getData() {
    axiosClient.get('report/orders', {params: {d: route.params.date}})
        .then(({ data }) => {
            chartData.value = data
        })
}



</script>

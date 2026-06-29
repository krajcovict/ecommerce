<template>
    <div class="h-100">
        <LineChart :data="chartData"/>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import axiosClient from '../../axios.js';
import LineChart from '../../components/core/Charts/Line.vue';
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
    axiosClient.get('report/customers', {params: {d: route.params.date}})
        .then(({ data }) => {
            chartData.value = data
        })
}

</script>

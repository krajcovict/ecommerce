<template>
    <LineChart :data="chartData" :height="100"/>
</template>

<script setup>
import axiosClient from '../../axios.js';
import LineChart from '../../components/core/Charts/Line.vue';
import { ref } from 'vue';

const chartData = ref({
  labels: [],
  datasets: [{ data: [] }]
})

axiosClient.get('report/customers')
    .then(({ data }) => {
        chartData.value = data
    })

</script>

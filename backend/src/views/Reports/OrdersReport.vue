<template>
    <BarChart :data="chartData" :height="200"/>
</template>

<script setup>
import axiosClient from '../../axios.js';
import BarChart from '../../components/core/Charts/Bar.vue';
import { ref } from 'vue';

const chartData = ref({
  labels: [],
  datasets: [{ data: [] }]
})

axiosClient.get('report/orders')
    .then(({ data }) => {
        chartData.value = data
    })

</script>

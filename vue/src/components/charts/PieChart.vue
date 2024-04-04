<template>
  <Pie :data="props.data" :options="props.options" />
</template>

<script setup>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

const props = defineProps(['data', 'options'])
ChartJS.register(ArcElement, Tooltip, Legend)

</script>

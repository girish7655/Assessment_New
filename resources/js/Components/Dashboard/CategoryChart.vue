<script setup>
import { onMounted, ref } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    categories: Array
});

const chartCanvas = ref(null);

onMounted(() => {
    const ctx = chartCanvas.value.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.categories.map(category => category.name),
            datasets: [{
                label: 'Books per Category',
                data: props.categories.map(category => category.books_count),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<template>
    <canvas ref="chartCanvas"></canvas>
</template>
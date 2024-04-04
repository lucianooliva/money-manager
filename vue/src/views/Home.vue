<template>
  <div class="home-container">
    <div v-if="initialLoading" class="h-full w-full flex justify-center items-center">
      <div class="h-7 w-7">
        <Spinner></Spinner>
      </div>
    </div>
    <div v-if="!initialLoading" class="h-full w-full">
      <div class="panel-container justify-between">
        <div class="panel shadow-md rounded-lg p-6 col-2">
          <h2 class="text-l font-semibold mb-4">Despesas concluídas</h2>
          <h2 class="text-xl font-semibold">R$ {{summary.expenseTotalDone}}</h2>
        </div>
        <div class="panel shadow-md rounded-lg p-6 col-2">
          <h2 class="text-l font-semibold mb-4">Despesas a concluir</h2>
          <h2 class="text-xl font-semibold">R$ {{summary.expenseTotalTodo}}</h2>
        </div>
      </div>
      <div class="panel-container justify-between">
        <div class="panel shadow-md rounded-lg p-6 col-2">
          <h2 class="text-l font-semibold mb-4">Despesas totais</h2>
          <h2 class="text-xl font-semibold">R$ {{summary.expenseTotal}}</h2>
        </div>
        <div class="panel shadow-md rounded-lg p-6 col-2">
          <h2 class="text-l font-semibold mb-4">Receitas totais</h2>
          <h2 class="text-xl font-semibold">R$ {{summary.incomeTotal}}</h2>
        </div>
      </div>
      <div class="panel-container justify-between">
        <div class="panel shadow-md rounded-lg p-6 col-1">
          <div class="bg-gray-200 w-full h-8 rounded-lg overflow-hidden relative">
            <div class="bg-good-green h-full text-white text-center leading-none py-2" :style="{'width': summary.expenseTotalDonePercentage}">
            </div>
            <span class="absolute left-2 text-white" style="top: 13%">{{summary.expenseTotalDonePercentage}} de despesas concluídas</span>
          </div>
        </div>
      </div>
      <div class="panel-container justify-between">
        <div class="panel shadow-md rounded-lg p-6 col-2" style="height: 348px" v-if="categories && categories.length > 0 && incomes && incomes.list.length > 0">
          <PieChart :data="expenseVsIncomeChartData" :options="pieChartOptions" :key="expenseVsIncomePieChartKey" v-if="expenseVsIncomePieChartKey > 0"></PieChart>
        </div>
        <div class="panel shadow-md rounded-lg p-6 col-2 flex justify-center" style="height: 348px" v-if="!categories || categories.length === 0 || !incomes || incomes.list.length === 0">
          <b v-if="!categories || categories.length === 0" @click="jumpTo('/monthly-bills')" class="flex items-center text-center text-gray-300 cursor-pointer hover:text-primary">Nada a exibir. Clique aqui para adicionar uma despesa</b>
          <b v-if="!incomes || incomes.list.length === 0" @click="jumpTo('/monthly-incomes')" class="flex items-center text-center text-gray-300 cursor-pointer hover:text-primary">Nada a exibir. Clique aqui para adicionar uma receita</b>
        </div>
        <div class="panel shadow-md rounded-lg p-6 col-2" style="height: 348px" v-if="categories && categories.length > 0">
          <PieChart :data="categoryChartData" :options="pieChartOptions" :key="categoriesPieChartKey" v-if="categoriesPieChartKey > 0"></PieChart>
        </div>
        <div class="panel shadow-md rounded-lg p-6 col-2 flex justify-center" style="height: 348px" v-if="!categories || categories.length === 0">
          <b class="flex items-center text-center text-gray-300">Nada a exibir</b>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref } from 'vue';
import store from '../store';
import PieChart from '../components/charts/PieChart.vue';
import Spinner from '../components/Spinner.vue';
import router from '../router';
import moneyUtils from '../utils/money'

const categories = ref([])
const incomes = ref([])
const summary = ref({
  expenseTotalTodo: "0",
  expenseTotalDone: "0",
  expenseTotal: "0",
  incomeTotal: "0",
  expenseTotalDonePercentage: "0"
})
const expenseVsIncomeChartData = ref(makeInitialPieChartData())
const categoryChartData = ref(makeInitialPieChartData())
const pieChartOptions = ref({
  responsive: true,
  maintainAspectRatio: false
})
let expenseVsIncomePieChartKey = ref(0)
let categoriesPieChartKey = ref(1)
const initialLoading = ref(false)

initialize();

function makeInitialPieChartData() {
  return {
    labels: [],
    datasets: [
      {
        backgroundColor: [],
        data: []
      }
    ]
  }
}
async function initialize() {
  initialLoading.value = true
  const categoryPromise = loadCategories()
  const incomePromise = loadIncomes()
  const result = await Promise.all([categoryPromise, incomePromise]);
  [categories.value, incomes.value] = result
  summary.value = makeSummary(categories.value, incomes.value)
  expenseVsIncomeChartData.value = makeExpenseChartData(categories.value, incomes.value)
  categoryChartData.value = makeCategoryChartData(categories.value)
  expenseVsIncomePieChartKey.value += 2
  categoriesPieChartKey.value += 2
  initialLoading.value = false
}
function makeCategoryChartData(categories) {
  const labels = []
  categories.forEach(cat => {
    labels.push(cat.name)
  });
  const data = []
  categories.forEach(cat => {
    data.push(parseFloat(cat.total))
  });
  return  {
    labels: labels,
    datasets: [
      {
        backgroundColor: makeRandomBgColorArray(labels.length),
        data: data
      }
    ]
  };
}
function makeExpenseChartData(categories, incomes) {
  let allExpenses = categories.reduce((sum, item) => {
    return sum + Math.floor(parseFloat(item.total) * 100)
  }, 0)
  const incomeTotal = Math.round(parseFloat(incomes.total)*100)
  const diff = incomeTotal - allExpenses
  let A = null
  let B = null
  if (diff < 0) {
    A = (incomeTotal / 100).toFixed(2)
    B = 0
  }
  else {
    A = (allExpenses / 100).toFixed(2)
    B = (diff / 100).toFixed(2)
  }
  return  {
    labels: ['Dinheiro comprometido', 'Dinheiro não empregado'],
    datasets: [
      {
        backgroundColor: ['#E46651', '#41B883'],
        data: [A, B]
      }
    ]
  };
}
function makeRandomBgColorArray(amount) {
  const list = [
    '#00A6A6',
    '#FFB997',
    '#D4A5A5',
    '#355C7D',
    '#6D6875',
    '#C5D86D',
    '#34344A',
    '#E63946',
    '#A8DADC',
    '#457B9D',
    '#F8E16C',
    '#F67280',
    '#C06C84',
    '#F8B195',
    '#FF5733',
    '#FF6F61',
    '#6B5B95',
    '#70A288',
    '#D64161',
    '#FFCBA4',
    '#36454F',
    '#E94B3C',
    '#A7C5EB',
    '#5E1741',
    '#BEDB39',
    '#4F6D7A',
    '#B7D7D8',
    '#FFD166',
    '#EF476F',
    '#F8961E',
    '#43AA8B',
    '#2E294E',
    '#009B77',
    '#FF6F61',
    '#5D2E46',
    '#F4A261',
    '#F9C74F',
    '#F3722C',
    '#00B2CA',
    '#577590',
    '#283655',
    '#80CED7',
    '#F4978E',
    '#E63946',
    '#B5EAD7',
    '#9B5DE5',
    '#F7E4C6',
    '#009B77',
    '#F18F01',
    '#FF5733',
  ];
  return list.slice(0, amount);
}
function makeSummary(categories, incomes) {
  let allExpenses = moneyUtils.sumFromArrayOfObjects(categories, 'total')
  let allExpensesTodo = moneyUtils.sumFromArrayOfObjects(categories, 'totalTodo')
  let allDoneExpenses = moneyUtils.sumFromArrayOfObjects(categories, 'totalDone')
  
  const expenseTotal = moneyUtils.convertMoneyValueToStringPtBr(allExpenses)
  const expenseTotalTodo = moneyUtils.convertMoneyValueToStringPtBr(allExpensesTodo)
  const expenseTotalDone = moneyUtils.convertMoneyValueToStringPtBr(allDoneExpenses)
  const incomeTotal = moneyUtils.convertMoneyValueToStringPtBr(incomes.total)
  const expenseTotalDonePercentage = (allExpenses > 0 ? Math.round(allDoneExpenses / allExpenses * 100) : 0) + '%'
  return {expenseTotalTodo, expenseTotalDone, expenseTotal, incomeTotal, expenseTotalDonePercentage}
}
async function loadIncomes() {
    const returnValue = await store.dispatch('expenses/getAllIncomes');
    return returnValue;
}
async function loadCategories() {
  try {
    const returnValue = await store.dispatch('expenses/getAllCategories');
    return returnValue;
  } catch (err) {
    console.error(err);
  }
}
function jumpTo(destination) {
  router.push(destination)
}
</script>
<style scoped>
.home-container {
  display: block;
  width: 100%;
  height: 100%;
  padding: 1em;
}
</style>
  
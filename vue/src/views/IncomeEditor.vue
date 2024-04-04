<template>
  <div class="income-container">
    <div class="h-full w-full flex justify-center items-center" v-if="initialLoading">
      <div class="h-7 w-7">
        <Spinner></Spinner>
      </div>
    </div>
    <div :class="{'h-full': !income}" v-if="!initialLoading">
      <div class="h-full w-full" v-if="!income">
        <NoDataFiller :message="'Ops! Algo deu errado'" :icon="ExclamationTriangleIcon"></NoDataFiller>
      </div>
      <div class="panel shadow-md rounded-lg p-6" v-if="income">
        <div class="relative">
          <h2 class="text-xl font-semibold mb-4">Detalhes da receita</h2>
          <a @click="changeToEditMode" v-show="mode==='view'" class="cursor-pointer text-gray-600 hover:text-red-600 absolute top-0 right-0 z-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
          </a>
          <form @submit.prevent="saveIncome">
            <div class="form-info flex mt-4 text-sm text-left">
              <label>Nome</label>
              <input v-model="income.name" ref="incomeNameInput" type="text" id="income-name" name="income-name" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
            </div>
            <div class="form-info flex mt-4 text-sm text-left">
              <label>Valor</label>
              <input v-model.lazy="income.value" v-money3="config" type="text" id="income-value" name="income-value" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
            </div>
            <div class="form-info flex mt-4 mb-4 text-sm text-left">
              <label for="income-description">Descrição</label>
              <textarea v-model="income.description" class="form-input resize-none h-20 focus:border-indigo-500" id="income-description">Cobranças mensais no cartão de crédito que são pagas antecipadamente</textarea>
            </div>
            <div class="form-footer" v-if="mode==='edit' || mode==='add'">
              <button type="submit" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker">Salvar</button>
              <button type="button" @click="$router.go(-1)" class="py-2 px-4 rounded text-primary hover:text-primary hover:bg-gray-100">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue"
import router from "../router"
import { useRoute } from "vue-router"
import store from "../store"
import { Money3Directive as vMoney3 } from 'v-money3'
import Spinner from "../components/Spinner.vue"
import NoDataFiller from "../components/NoDataFiller.vue"
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid"

const config = ref({
  prefix: 'R$ ',
  suffix: '',
  thousands: '.',
  decimal: ',',
  precision: 2,
  disableNegative: false,
  disabled: false,
  min: null,
  max: null,
  allowBlank: false,
  minimumNumberOfCharacters: 0,
  shouldRound: true,
  focusOnRight: false,
});

const route = useRoute()
const routeId = route.params.id ?? null
const mode = ref(routeId ? 'edit' : 'add')
const income = ref({
  name: null,
  description: "",
  value: null,
})
const initialLoading = ref(false)

initializeData();

async function initializeData() {
  if (mode.value === 'add') {
    return;
  }
  initialLoading.value = true
  income.value = await getIncome()
  initialLoading.value = false
}
async function getIncome() {
  const incomeId = routeId
  const returnValue = await store.dispatch('expenses/getIncome', incomeId)
  if (returnValue.errorCode) return null;
  return returnValue;
}
function turnValueNumeric(value) {
  let newValue = value.replace(/[R\$\s\.]+/g, '');
  newValue = newValue.replace(/,/, '.');
  return newValue;
}
async function addIncome() {
  const newIncome = income.value
  newIncome.value = turnValueNumeric(income.value.value)
  try {
    await store.dispatch('expenses/newIncome', newIncome)
  } catch (err) {
    alert(err)
  }
}
async function updateIncome() {
  const exp = income.value
  exp.value = turnValueNumeric(income.value.value)
  await store.dispatch('expenses/updateIncome', income.value)
}
async function saveIncome() {
  if (mode.value === 'add') {
    await addIncome();
    router.go(-1)
    return;
  }
  await updateIncome();
  router.go(-1)
}
function changeToEditMode() {
  setTimeout(() => incomeNameInput.value.focus(), 500)
  mode.value = 'edit'
}
</script>
<style scoped>
.income-container {
  display: block;
  width: 100%;
  height: 100%;
  padding: 1em;
}
.form-input {
  padding-bottom: 5px;
}
</style>
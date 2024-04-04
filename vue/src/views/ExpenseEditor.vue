<template>
  <div class="expense-container">
    <div class="h-full w-full flex justify-center items-center" v-if="initialLoading">
      <div class="h-7 w-7">
        <Spinner></Spinner>
      </div>
    </div>
    <div :class="{'h-full': mode !== 'add' && !expense.id}" v-if="!initialLoading">
      <div class="h-full w-full" v-if="mode !== 'add' && !expense.id">
        <NoDataFiller :message="'Ops! Algo deu errado'" :icon="ExclamationTriangleIcon"></NoDataFiller>
      </div>
      <div class="panel-container" v-if=" !(mode !== 'add' && !expense.id) ">
        <div class="panel shadow-md rounded-lg p-6 w-full">
          <div class="relative">
            <h2 class="text-xl font-semibold mb-4">Detalhes da despesa</h2>
            <a @click="changeToEditMode" v-show="mode==='view'" class="cursor-pointer text-gray-600 hover:text-red-600 absolute top-0 right-0 z-10">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </a>
            <form @submit.prevent="checkThenSave">
              <div class="form-info flex mt-4 text-sm text-left">
                <label>Nome</label>
                <input v-model="expense.name" ref="expenseNameInput" type="text" id="expense-name" name="expense-name" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
              </div>
              <div class="form-info flex mt-4 text-sm text-left">
                <label>Valor</label>
                <input v-model.lazy="expense.value" v-money3="config" type="text" id="expense-value" name="expense-value" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
              </div>
              <div class="form-info flex mt-4 mb-4 text-sm text-left">
                <label for="expense-description">Descrição</label>
                <textarea v-model="expense.description" class="form-input resize-none h-20 focus:border-indigo-500" id="expense-description"></textarea>
              </div>
              <div class="form-info flex mt-4 mb-4 text-sm text-left">
                <label for="expense-category">Categoria</label>
                <select v-model="expense.category_id" class="form-input focus:border-indigo-500" id="expense-category" required>
                  <option v-for="c in categories" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="form-footer" v-if="mode==='edit' || mode==='add'">
                <button type="submit" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker">Salvar</button>
                <button @click="$router.go(-1)" type="button" class="py-2 px-4 rounded text-primary hover:text-primary hover:bg-gray-100">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <QuestionPopUp v-if="questionPopUpVisible" :title="popupTitle" :text="popupText" :buttons="popupButtons" @pop-up-option-selected="popupButtonPressedHandler"></QuestionPopUp>
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
import QuestionPopUp from "../components/QuestionPopUp.vue"

const popupTitle = ref(null)
const popupText = ref(null)
const popupButtons = ref(null)
const questionPopUpVisible = ref(false)
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
const expenseNameInput = ref(null)
const expense = ref({
  name: null,
  description: null,
  value: null,
  done: null,
  category_id: store.state.expenses.categoryId
})
const categories = ref([])
const initialLoading = ref(false)

initializeData();

async function initializeData() {
  initialLoading.value = true
  if (mode.value === 'add') {
    categories.value = await getAllCategories()
    initialLoading.value = false
    return;
  }
  const expensePromise = getExpense()
  const categoryPromise = getAllCategories()
  const result = await Promise.all([expensePromise, categoryPromise]);
  [expense.value, categories.value] = result
  initialLoading.value = false
}
function makePopupForDuplicateError() {
  popupButtons.value = {
    "ok":  { "text": "OK", type: "primary",   action: dismissPopup }
  };
  popupTitle.value = "O nome escolhido já existe"
  popupText.value = "Já existe outra despesa com esse nome. Tente novamente"
}
function makePopupForMissingField() {
  popupButtons.value = {
    "ok":  { "text": "OK", type: "primary",   action: dismissPopup }
  };
  popupTitle.value = "Categoria não selecionada"
  popupText.value = "Erro ao criar despesa: Categoria não selecionada. Tente novamente"

}
function makePopupForValueInputUntouched() {
  popupButtons.value = {
    "yes":  { "text": "Sim", type: "primary",   action: saveExpense },
    "no":   { "text": "Não", type: "secondary", action: dismissPopup }
  };
  popupTitle.value = "Despesa com valor zero"
  popupText.value = "Você deseja realmente criar uma despesa com valor R$ 0,00?"

}
function dismissPopup() {
  questionPopUpVisible.value = false
}
function showPopup() {
  questionPopUpVisible.value = true
}
function popupButtonPressedHandler(buttonName) {
  if (!buttonName in popupButtons.value) return;
  popupButtons.value[buttonName].action();
}
async function getExpense() {
  const expenseId = routeId
  try {
    const returnValue = await store.dispatch('expenses/getExpense', expenseId)
    return returnValue
  } catch (err) {
    return null;
  }
}
async function getAllCategories() {
  try {
    const returnValue = await store.dispatch('expenses/getAllCategories');
    return returnValue;
  } catch (err) {
    console.error(err);
  }
}
function turnValueNumeric(value) {
  let newValue = value.replace(/[R\$\s\.]+/g, '');
  newValue = newValue.replace(/,/, '.');
  return newValue;
}
function addExpense() {
  const newExpense = expense.value
  newExpense.value = turnValueNumeric(expense.value.value)
  store.dispatch('expenses/newExpense', newExpense)
    .then((returnValue) => {
      if (returnValue.error === "Duplicate name") {
        makePopupForDuplicateError()
        showPopup()
        return;
      }
      else if (returnValue.error === "Missing category_id") {
        makePopupForMissingField()
        showPopup()
        return;
      }
      router.go(-1)
    })
}
function updateExpense() {
  const exp = expense.value
  exp.value = turnValueNumeric(expense.value.value)
  store.dispatch('expenses/updateExpense', expense.value)
    .then(updatedExpense => {
      router.go(-1)
    })
}
function checkThenSave() {
  if (expense.value.value === "R$ 0,00") {
    makePopupForValueInputUntouched()
    showPopup()
    return;
  }
  saveExpense()
}
function saveExpense() {
  if (mode.value === 'add') {
    addExpense();
    return;
  }
  updateExpense();
}
function changeToEditMode() {
  setTimeout(() => expenseNameInput.value.focus(), 500)
  mode.value = 'edit'
}
</script>
<style scoped>
.expense-container {
  display: block;
  width: 100%;
  height: 100%;
  padding: 1em;
}
.form-input {
  padding-bottom: 5px;
}
</style>
<template>
  <div class="category-container">
    <Header :extra="extra" @button-pressed="informPanelAButtonHasBeenPressed"/>
    <div class="h-full w-full flex justify-center items-center" v-if="initialLoading">
      <div class="h-7 w-7">
        <Spinner></Spinner>
      </div>
    </div>
    <div :class="{'h-full': mode !== 'add' && !category.id}" v-if="!initialLoading">
      <div class="h-full w-full" v-if="mode !== 'add' && !category.id">
        <NoDataFiller :message="'Ops! Algo deu errado ('+mode+'), ('+category.id+')'" :icon="ExclamationTriangleIcon"></NoDataFiller>
      </div>
      <div class="panel-container">
        <div class="panel shadow-md rounded-lg p-6 w-full" v-if="!(mode !== 'add' && !category.id)">
          <div class="relative">
            <h2 class="text-xl font-semibold mb-4">Detalhes da categoria</h2>
            <a @click="changeToEditMode" v-show="mode==='view'" class="cursor-pointer text-gray-600 hover:text-red-600 absolute top-0 right-0 z-10">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </a>
            <form @submit.prevent="saveCategory">
              <div class="form-info flex mt-4 text-sm text-left">
                <label>Nome</label>
                <input v-model="category.name" ref="categoryNameInput" type="text" id="category-name" name="category-name" :disabled="mode==='view'" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
              </div>
              <div class="form-info flex mt-4 mb-4 text-sm text-left">
                <label for="category-description">Descrição</label>
                <textarea v-model="category.description" :disabled="mode==='view'" class="form-input resize-none h-20 focus:border-indigo-500" id="category-description">Cobranças mensais no cartão de crédito que são pagas antecipadamente</textarea>
              </div>
              <div class="form-info flex mt-4 mb-4 text-sm text-left">
                <label>Imagem</label>
                <div class="icon-selector relative" @click="toggleDropdown($event)">
                  <div class="icon-selector-header" :class="category.icon"  v-show="category.icon">
                    <div class="w-6 h-6 mr-2">
                      <component :is="category.icon.value"></component>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                      <path fill-rule="evenodd" d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="icon-selector-header" :class="category.icon"  v-show="!category.icon">
                    <span>Clique para selecionar</span>
                  </div>
                  <div v-show="isOpen" class="dropdown absolute flex flex-row flex-wrap bg-white shadow-md rounded-md mt-1 w-full">
                    <div v-for="(icon, index) in icons" :key="index" @click="selectIcon($event, icon)" class="icon-option flex justify-center items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                      <div class="w-6 h-6">
                        <component :is="icon.value"></component>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-footer" v-if="mode==='edit' || mode==='add'">
                <button type="submit" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker">Salvar</button>
                <button @click="cancelHandler" type="button" class="py-2 px-4 rounded text-primary hover:text-primary-lighter hover:bg-gray-100">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-container justify-between" v-show="mode!=='add' && category.id">
      <div class="panel shadow-md rounded-lg p-6 col-3">
        <h2 class="text-l font-semibold mb-4">Concluído</h2>
        <h2 class="text-xl font-semibold">R$ {{category.totalDone}}</h2>
      </div>
      <div class="panel shadow-md rounded-lg p-6 col-3">
        <h2 class="text-l font-semibold mb-4">A concluir</h2>
        <h2 class="text-xl font-semibold">R$ {{category.totalTodo}}</h2>
      </div>
      <div class="panel shadow-md rounded-lg p-6 col-3">
        <h2 class="text-l font-semibold mb-4">Total</h2>
        <h2 class="text-xl font-semibold">R$ {{category.total}}</h2>
      </div>
    </div>
   <ExpensesList v-if="mode!=='add' && category.expenses != null" :category="category" @item-selected="updateHeaderOptions" :eventFromHome="expensesListEvent"/>
  </div>
</template>
  
<script setup>
import { ref } from "vue"
import router from "../router"
import { useRoute } from "vue-router"
import store from "../store"
import moneyUtils from "../utils/money"
import ExpensesList from "../components/ExpensesList.vue"
import Spinner from "../components/Spinner.vue"
import NoDataFiller from "../components/NoDataFiller.vue"
import Header from "../components/Header.vue"
import categoryIcons from "../data/category-icons"
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid"

const extra = ref({headerOptions: {}})
let expensesListEvent = ref({event: null, data: null})
const route = useRoute()
let routeId = null
const mode = ref(null)
const categoryNameInput = ref(null)
const icons = ref( categoryIcons )
const category = ref({
  name: null,
  description: null,
  expenses: null,
  icon: icons.value[0]
})
const isOpen = ref(false)
const initialLoading = ref(false)

initialize();

function addCategoryIdToStore(value) {
  store.commit('expenses/setCategoryId', value)
}
async function initialize() {
  routeId = route.params.id ?? routeId
  mode.value = routeId ? 'view' : 'add'
  addCategoryIdToStore(routeId)
  if (mode.value === 'add') {
    return;
  }
  initialLoading.value = true
  category.value = await getCategoryWithExpenses()
  changeIconToObject();
  convertTotalValuesToStringPtBr()
  initialLoading.value = false
}
function convertTotalValuesToStringPtBr() {
  category.value.total = moneyUtils.convertMoneyValueToStringPtBr(category.value.total)
  category.value.totalDone = moneyUtils.convertMoneyValueToStringPtBr(category.value.totalDone)
  category.value.totalTodo = moneyUtils.convertMoneyValueToStringPtBr(category.value.totalTodo)
}
function changeIconToObject() {
  category.value.icon = category.value.icon ?? 'CurrencyDollarIcon'
  const idx = icons.value.findIndex(icon => icon.name === category.value.icon)
  category.value.icon = idx === -1 ? icons.value[0] : icons.value[idx]
}
async function getCategoryWithExpenses() {
  const categoryId = routeId
  try {
    const returnValue = await store.dispatch('expenses/getCategoryWithExpenses', categoryId)
    return returnValue
  } catch (err) {
    console.error(err)
  }
}
function updateHeaderOptions(buttons) {
  extra.value.headerOptions = buttons
}
function informPanelAButtonHasBeenPressed(buttonName) {
  expensesListEvent.value.event = buttonName
  expensesListEvent.value.data = null
  setTimeout(() => {
    expensesListEvent.value.event = null
    expensesListEvent.value.data = null
  }, 500)
}
async function addCategory() {
  return await store.dispatch('expenses/newCategory', category.value)
    .then((addedCategory) => {
      return addedCategory;
    })
    .catch(err => {
      alert(err)
    })
}
async function updateCategory() {
  return await store.dispatch('expenses/updateCategory', category.value)
    .then(updatedCategory => {
      return updatedCategory
    })
}
async function saveCategory() {
  if (mode.value === 'add') {
    const addedCategory = await addCategory();
    router.replace({
      name: "CategoryView",
      params: { id: addedCategory.id }
    });
    routeId = addedCategory.id
    initialize()
    return;
  }
  await updateCategory();
  mode.value = 'view'
}
function cancelHandler() {
  const modeHandlers = {
    "add": () => {
      router.go(-1);
    },
    "edit": () => {
      mode.value = "view";
    },
    "view": () => {
      // do nothing
    }
  }
  if ( !(mode.value in modeHandlers) ) {
    console.error(`Mode handler for ${mode.value} not found`)
    return;
  }
  modeHandlers[mode.value]()
}
function changeToEditMode() {
  setTimeout(() => categoryNameInput.value.focus(), 500)
  mode.value = 'edit'
}
function toggleDropdown(event) {
  mode.value = mode.value === 'view' ? 'edit' : mode.value
  event.stopPropagation();
  const newValue = !isOpen.value
  isOpen.value = newValue
  if (newValue) {
    document.body.addEventListener("click", closeDropDown)
  }
  else {
    document.body.removeEventListener("click", closeDropDown)
  }
}
function closeDropDown(event) {
  event.stopPropagation();
  isOpen.value = false
  document.body.removeEventListener("click", closeDropDown)
}
function selectIcon(event, icon) {
  category.value.icon = icon
  closeDropDown(event)
}
</script>
<style scoped>
.category-container {
  display: block;
  width: 100%;
  height: 100%;
  padding: 1em;
}
.icon-selector {
  display: inline-block;
  border: 1px solid rgb(240, 240, 240);
  color: gray;
}
.icon-selector-header {
  display: flex;
  justify-content: space-between;
  width: 100%;
  padding: 6px;
  cursor: pointer;
}
.dropdown {
  min-width: 160px; /* Adjust the dropdown width as needed */
  z-index: 10;
}
.icon-option {
  transition: background-color 0.2s ease-in-out;
  color: gray;
}
.icon-option:hover {
  background-color: #f3f4f6;
}
</style>

<template>
  <ContextMenu :key="reloadContextMenu" :contextMenuOptions="contextMenuOptions" @on-click-option="onClickContextMenuOption" :extra="extra" @visibility-changed="visibilityChangeHandler"/>
  <Header :extra="extra" @button-pressed="headerButtonPressedHandler"/>
  <div class="monthlybills-container">
    <div class="py-5">
      <button @click="jumpTo('/category/create')" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker">
        Nova categoria
      </button>
      <button @click="jumpTo('/expense/create')" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker" v-if="categories && categories.length > 0">
        Nova despesa
      </button>
    </div>
    <div class="h-full w-full flex justify-center items-center" v-if="initialLoading">
      <div class="h-7 w-7">
        <Spinner></Spinner>
      </div>
    </div>
    <div class="panel-container justify-between w-full mb-3" v-if="!initialLoading" style="gap: 0.375em;">
      <div class="panel shadow-md rounded-lg p-6 col-3">
        <h2 class="text-l font-semibold mb-4">Despesas concluídas</h2>
        <h2 class="text-xl font-semibold">R$ {{summary.expenseTotalDone}}</h2>
      </div>
      <div class="panel shadow-md rounded-lg p-6 col-3">
        <h2 class="text-l font-semibold mb-4">Despesas a concluir</h2>
        <h2 class="text-xl font-semibold">R$ {{summary.expenseTotalTodo}}</h2>
      </div>
      <div class="panel shadow-md rounded-lg p-6 col-3">
        <h2 class="text-l font-semibold mb-4">Despesas totais</h2>
        <h2 class="text-xl font-semibold">R$ {{summary.expenseTotal}}</h2>
      </div>
    </div>
    <div class="panel-container justify-between w-full mb-3" v-if="!initialLoading">
      <div class="panel shadow-md rounded-lg p-6 col-1">
        <div class="bg-gray-200 w-full h-8 rounded-lg overflow-hidden relative">
          <div class="bg-good-green h-full text-white text-center leading-none py-2" :style="{'width': summary.expenseTotalDonePercentage}">
          </div>
          <span class="absolute left-2 text-white" style="top: 13%">{{summary.expenseTotalDonePercentage}} de despesas concluídas</span>
        </div>
      </div>
    </div>
    <div v-if="!initialLoading" class="shadow-md bg-white rounded-lg p-6 w-full" :class="{'h-full': !categories || categories.length === 0}">
      <NoDataFiller v-if="!categories || categories.length === 0" :message="'Nenhuma categoria'" :icon="CurrencyDollarIcon"></NoDataFiller>
      <div class="category-list flex flex-wrap justify-start" v-if="categories && categories.length > 0">
        <div :key="idx" v-for="(c, idx) in categories" class="relative">
          <span v-show="c.selected" class="absolute top-[-0.5rem] left-[-0.5rem]" style="z-index: 2;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 p-1 bg-red-500 text-white rounded-full">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
          </span>
          <CategoryCard @contextmenu.prevent="renderContextMenu($event, c)" @three-dot-menu-click="renderContextMenu" :category="c" :isVisible="threeDotMenuVisible" @click="clickCategoryHandler(c)" class="cursor-pointer"></CategoryCard>
        </div>
      </div>
    </div>
    <Transition>
      <div class="popup-card rounded flex justify-center bg-primary" style="right: 10; bottom: 5" v-show="selectedCategories && selectedCategories.length">
        <b class="mr-2">Soma:</b><span>R$ {{ sumSelected }}</span>
      </div>
    </Transition>
  </div>
  <QuestionPopUp v-if="questionPopUpVisible" :title="confirmTitle" :text="confirmText" :buttons="confirmButtons" @pop-up-option-selected="confirmButtonPressedHandler"></QuestionPopUp>
</template>
<script setup>
import { ref, watch } from 'vue';
import router from '../router';
import store from '../store';
import moneyUtils from '../utils/money'
import CategoryCard from '../components/CategoryCard.vue';
import ContextMenu from "../components/ContextMenu.vue"
import QuestionPopUp from "../components/QuestionPopUp.vue"
import Spinner from "../components/Spinner.vue"
import NoDataFiller from "../components/NoDataFiller.vue"
import Header from '../components/Header.vue';
import categoryIcons from "../data/category-icons"
import { ClipboardDocumentCheckIcon, PencilSquareIcon, TrashIcon, CheckBadgeIcon } from '@heroicons/vue/24/outline';
import { ClipboardDocumentCheckIcon as ClipboardDocumentCheckIconSolid, CheckBadgeIcon as CheckBadgeIconSolid, CurrencyDollarIcon } from '@heroicons/vue/24/solid';

const categories = ref([])
const reloadContextMenu = ref(0)
const contextMenuOptions = ref({})
let threeDotMenuVisible = ref(true)
const checkboxVisible = ref(false);
const extra = ref({})
const selectedCategories = ref(null)
const confirmTitle = ref(null)
const confirmText = ref(null)
const confirmButtons = ref(null)
const questionPopUpVisible = ref(false)
let contextMenuIsVisible = false
let buttons = {}
const sumSelected = ref(0)
const icons = ref(categoryIcons)
const initialLoading = ref(false)
const summary = ref({
  expenseTotalTodo: "0",
  expenseTotalDone: "0",
  expenseTotal: "0",
  expenseTotalDonePercentage: "0"
})


initializeData();

function headerButtonPressedHandler(buttonName) {
  if ( !(buttonName in buttons) ) return;
  buttons[buttonName].action();
}
function openExpenseEditorFromHeader() {
  if (!selectedCategories.value || selectedCategories.value.length !== 1) return;
  jumpToCategoryView(selectedCategories.value[0]);
}
function selectAll() {
  checkboxVisible.value = true
  categories.value.map(item => item.selected = true)
}
function deselectAll() {
  checkboxVisible.value = false
  categories.value.map(item => item.selected = false)
}
function getSelectedCategory() {
  if (selectedCategories.value?.length === 1) {
    return selectedCategories.value[0]
  }
  return extra.value.item ?? null
}
function getSelectedCategories() {
  if (selectedCategories.value?.length > 0) {
    return selectedCategories.value
  }
  return extra.value.item ? [extra.value.item] : null
}
function showHeader() {
  const item = getSelectedCategory()
  const doneText = isDone(item) ? "Marcar como não concluída" : "Marcar como concluída"
  const doneAction = isDone(item) ? markAsNotDone : markAsDone
  const doneIcon = isDone(item) ? CheckBadgeIcon : CheckBadgeIconSolid
  buttons = {
    "Selecionar todos": { action: selectAll, icon: ClipboardDocumentCheckIconSolid    },
    "Desmarcar todos":  { action: deselectAll, icon: ClipboardDocumentCheckIcon  },
    "Editar":           { action: openExpenseEditorFromHeader, icon: PencilSquareIcon },
    "Excluir":          { action: showPopUpQuestion, icon: TrashIcon },
  };
  buttons[doneText] = { action: doneAction, icon: doneIcon }
  updateHeaderOptions(buttons)
}
function updateHeaderOptions(buttons) {
  extra.value.headerOptions = buttons
}
function ensureHeaderIsNotVisible() {
  buttons = {}
  updateHeaderOptions(buttons)
}
function manageHeaderButtonsAndCheckboxes() {
  const selectedCount = categories.value.reduce((total, item) => total + (item.selected === true ? 1 : 0), 0)
  const doneCount = categories.value.reduce((total, item) => total + (item.selected === true && isDone(item) === true ? 1 : 0), 0)
  switch (selectedCount) {
    case 0:
      hideCheckboxes()
      buttons = {}
      updateHeaderOptions(buttons)
      break;
    case 1:
      showHeader();
      break;
    case categories.value.length:
      buttons = {
        "Desmarcar todos": { action: deselectAll, icon: ClipboardDocumentCheckIcon },
        "Excluir":         { action: showPopUpQuestion, icon: TrashIcon },
      };
      if (selectedCount === doneCount) {
        buttons["Marcar como não concluídas"] = { action: markAsNotDone, icon: CheckBadgeIcon }
      }
      else {
        buttons["Marcar como concluídas"] = { action: markAsDone, icon: CheckBadgeIconSolid }
      }
      updateHeaderOptions(buttons)
      break;
    default:
      buttons = {
        "Selecionar todos":       { action: selectAll, icon: ClipboardDocumentCheckIconSolid         },
        "Desmarcar todos":        { action: deselectAll, icon: ClipboardDocumentCheckIcon       },
        "Excluir":                { action: showPopUpQuestion, icon: TrashIcon },
      };
      if (selectedCount === doneCount) {
        buttons["Marcar como não concluídas"] = { action: markAsNotDone, icon: CheckBadgeIcon }
      }
      else {
        buttons["Marcar como concluídas"] = { action: markAsDone, icon: CheckBadgeIconSolid }
      }
      updateHeaderOptions(buttons)
  }
}
function selectionController() {
  manageHeaderButtonsAndCheckboxes();
  updateSelectedCategories();
  updateSumOfSelectedCategories();
  threeDotMenuVisible.value = !selectedCategories.value || !selectedCategories.value.length
}
function updateSumOfSelectedCategories() {
  let sum = selectedCategories.value.reduce((total, cat) => Math.round(parseFloat(cat.total) * 100) + total, 0)
  sum =  (sum / 100).toFixed(2)
  sumSelected.value = sum ? `${sum}`.replace(/,/, '.') : null
}
function clickCategoryHandler(category) {
  const selectionCount = selectedCategories.value.length
  if (selectionCount > 0) {
    category.selected = !category.selected
    return;
  }
  if (contextMenuIsVisible) return;
  openCategory(category)
}
function visibilityChangeHandler(visible) {
  contextMenuIsVisible = visible
}
const onClickContextMenuOption = function(optionName) {
  if ( !(optionName in contextMenuOptions.value) ) {
    console.error("Option clicked not found");
    return;
  }
  contextMenuOptions.value[optionName].action();
}
async function deleteMultipleCategories(categories) {
  try {
    await store.dispatch('expenses/deleteMultipleCategories', categories);
    reloadCategoriesList();
  } finally {
    hidePopUpQuestion();
  }
}
async function deleteCategory(category) {
  try {
    await store.dispatch('expenses/deleteCategory', category);
    reloadCategoriesList();
  } finally {
    hidePopUpQuestion();
  }
}
function hidePopUpQuestion() {
  questionPopUpVisible.value = false
}
function removeCategories() {
  const categories = getSelectedCategories()
  if (categories === null) {
    console.error("Unable to get the selected expense")
    hidePopUpQuestion()
    return;
  }
  if (categories.length === 1) {
    deleteCategory(categories[0]);
    return;
  }
  deleteMultipleCategories(categories)
}
function confirmButtonPressedHandler(buttonName) {
  if (!buttonName in confirmButtons.value) return;
  confirmButtons.value[buttonName].action();
}
function makeQuestionParams() {
  const multiple = selectedCategories.value?.length > 1
  if (multiple) {  
    confirmTitle.value = "Remover categorias";
    confirmText.value = "Você tem certeza que deseja remover essas categorias e suas despesas?";
  }
  else {
    confirmTitle.value = "Remover categoria";
    confirmText.value = "Você tem certeza que deseja remover essa categoria e suas despesas?";
  }
  confirmButtons.value = {
    "yes": { "text": "Sim", type: "primary",   action: removeCategories },
    "no":  { "text": "Não", type: "secondary", action: hidePopUpQuestion },
  };
}
function showPopUpQuestion() {
  makeQuestionParams()
  questionPopUpVisible.value = true
}
function openExpenseEditorFromContextMenu() {
  if (!extra.value.item) return;
  jumpToCategoryView(extra.value.item);
}
function jumpToCategoryView(category) {
  router.push({
    name: "CategoryView",
    params: {
      id: category.id
    }
  })
}
function selectItem() {
  checkboxVisible.value = true
  extra.value.item.selected = true
}
function hideCheckboxes() {
  checkboxVisible.value = false
}
function addSelectedField() {
  categories.value.map(i => i.selected = false)
}
async function reloadCategoriesList() {
  categories.value = await loadCategories()
  addSelectedField()
  changeIconToObject()
  summary.value = makeSummary(categories.value)
}
function updateSelectedCategories() {
  selectedCategories.value = categories.value.filter(cat => cat.selected)
}
async function updateDoneStatusMultiple(expenses, doneStatus) {
  await store.dispatch('expenses/updateDoneStatusMultiple', {expenses: expenses, done: doneStatus})
  .then(response => {
    return response
  })
}
async function markAsDone() {
  const categories = getSelectedCategories()
  if (categories === null) {
    console.error("Unable to get the selected categories")
    return;
  }
  let expenses = []
  categories.map(cat => {
    expenses = [ ...expenses, ...cat.expenses ]
  })
  await updateDoneStatusMultiple(expenses, true)
  reloadCategoriesList()
}
async function markAsNotDone() {
  const categories = getSelectedCategories()
  if (categories === null) {
    console.error("Unable to get the selected categories")
    return;
  }
  let expenses = []
  categories.map(cat => {
    expenses = [ ...expenses, ...cat.expenses ]
  })
  await updateDoneStatusMultiple(expenses, false)
  reloadCategoriesList()
}
function renderContextMenu(event, item) {
  deselectAll()
  const doneText = isDone(item) ? "Marcar como não concluída" : "Marcar como concluída"
  const doneAction = isDone(item) ? markAsNotDone : markAsDone
  contextMenuOptions.value = {
    "1": {text: "Selecionar", action: selectItem},
    "2": {text: "Editar",     action: openExpenseEditorFromContextMenu},
    "3": {text: "Excluir",    action: showPopUpQuestion},
    "4": {text: doneText,     action: doneAction},
  }
  extra.value.item = item
  extra.value.event = event
  forceContextMenuRerendering()
}
function isDone(category) {
  return category.expenses.reduce((cur, exp) => exp.done && cur, true)
}
function forceContextMenuRerendering() {
  reloadContextMenu.value += 1
}
function jumpTo(destination) {
  router.push(destination)
}
async function initializeData() {
  initialLoading.value = true
  categories.value = await loadCategories();
  addSelectedField()
  changeIconToObject()
  ensureHeaderIsNotVisible()
  summary.value = makeSummary(categories.value)
  initialLoading.value = false
}
function makeSummary(categories) {
  let allExpenses = moneyUtils.sumFromArrayOfObjects(categories, 'total')
  let allExpensesTodo = moneyUtils.sumFromArrayOfObjects(categories, 'totalTodo')
  let allDoneExpenses = moneyUtils.sumFromArrayOfObjects(categories, 'totalDone')
  
  const expenseTotal = moneyUtils.convertMoneyValueToStringPtBr(allExpenses)
  const expenseTotalTodo = moneyUtils.convertMoneyValueToStringPtBr(allExpensesTodo)
  const expenseTotalDone = moneyUtils.convertMoneyValueToStringPtBr(allDoneExpenses)
  const expenseTotalDonePercentage = (allExpenses > 0 ? Math.round(allDoneExpenses / allExpenses * 100) : 0) + '%'
  return {expenseTotalTodo, expenseTotalDone, expenseTotal, expenseTotalDonePercentage}
}
function convertMoneyValue(val) {
  let result = val
  if (typeof val == 'number') {
    result = val.toFixed(2).toString()
  }
  result = result.replace(/\./, ',')
  return result
}
function changeIconToObject() {
  categories.value.map((category) => {
    category.icon = category.icon ?? 'CurrencyDollarIcon'
    const idx = icons.value.findIndex(icon => icon.name === category.icon)
    category.icon = idx === -1 ? icons.value[0] : icons.value[idx]
  })
}
async function loadCategories() {
  try {
    const returnValue = await store.dispatch('expenses/getAllCategories');
    return returnValue;
  } catch (err) {
    console.log(err);
  }
}
function openCategory(category) {
  router.push({
    name: "CategoryView",
    params: { id: category.id }
  });
}
watch(() => categories, () => {
  selectionController()
}, {deep: true})
</script>
<style scoped>
.monthlybills-container {
  display: block;
  width: 100%;
  height: calc(100% - 4em);
  padding: 1em;
  position: relative;
}
.action-btn {
  min-width: 60px;
  padding: 5px;
}
.category-list {
  padding: 0;
  margin: 0;
  gap: 0em;
}
.category-list>div {
  width: 20%;
}
.popup-card {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 999;
  color: white;
  padding: 10px;
}
.v-enter-active,
.v-leave-active {
  transition: bottom 0.5s ease;
}
.v-enter-from,
.v-leave-to {
  bottom: -100px;
}
@media (max-width: 1491px) {
  .category-list>div {
    width: 25%;
  }
}
@media (max-width: 1130px) {
  .category-list>div {
    width: 33%;
  }
}
@media (max-width: 890px) {
  .category-list>div {
    width: 50%;
  }
}
@media (max-width: 768px) {
  .category-list>div {
    width: 50%;
  }
  .popup-card {
    bottom: 90px;
  }
  .v-enter-active,
  .v-leave-active {
    transition: bottom 0.5s ease;
  }
  .v-enter-from,
  .v-leave-to {
    bottom: -100px;
  }
}
@media (max-width: 512px) {
  .category-list>div {
    width: 100%;
  }
}

</style>
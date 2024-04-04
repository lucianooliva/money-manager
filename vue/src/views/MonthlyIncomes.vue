<template>
  <ContextMenu :key="reloadContextMenu" :contextMenuOptions="contextMenuOptions" @on-click-option="onClickContextMenuOption" :extra="extra" @visibility-changed="visibilityChangeHandler"/>
  <Header :extra="extra" @button-pressed="headerButtonPressedHandler"/>
  <div class="monthlyincomes-container">
    <div class="py-5">
      <button @click="jumpTo('/income/create')" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker">
        Nova receita
      </button>
    </div>
    <div class="h-full w-full flex justify-center items-center" v-if="initialLoading">
      <div class="h-7 w-7">
        <Spinner></Spinner>
      </div>
    </div>
    <div v-if="!initialLoading" class="h-full w-full">
      <div class="panel-container flex-wrap justify-between h-full" v-if="!incomes || !incomes.list || incomes.list.length === 0">
        <NoDataFiller :message="'Nenhuma receita'" :icon="WalletIcon"></NoDataFiller>
      </div>
      <div class="panel-container flex-wrap justify-between" v-if="incomes && incomes.list && incomes.list.length > 0">
        <div :key="idx" v-for="(inc, idx) in incomes.list" class="income-card-container relative">
          <span v-show="inc.selected" class="absolute top-2 left-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 p-1 bg-red-500 text-white rounded-full">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
          </span>
          <IncomeCard @contextmenu.prevent="renderContextMenu($event, inc)" @three-dot-menu-click="renderContextMenu" :income="inc" @click="clickIncomeHandler(inc)" class="cursor-pointer"></IncomeCard>
        </div>
      </div>
    </div>
  </div>
  <QuestionPopUp v-if="questionPopUpVisible" :title="confirmTitle" :text="confirmText" :buttons="confirmButtons" @pop-up-option-selected="confirmButtonPressedHandler"></QuestionPopUp>
</template>
<script setup>
import { ref, watch } from 'vue';
import router from '../router';
import store from '../store';
import IncomeCard from '../components/IncomeCard.vue';
import ContextMenu from "../components/ContextMenu.vue"
import QuestionPopUp from "../components/QuestionPopUp.vue"
import NoDataFiller from "../components/NoDataFiller.vue"
import Header from '../components/Header.vue';
import { CheckBadgeIcon, ClipboardDocumentCheckIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ClipboardDocumentCheckIcon as ClipboardDocumentCheckIconSolid, CheckBadgeIcon as CheckBadgeIconSolid, WalletIcon } from '@heroicons/vue/24/solid';
import Spinner from '../components/Spinner.vue';

const incomes = ref([])
const reloadContextMenu = ref(0)
const contextMenuOptions = ref({})
const checkboxVisible = ref(false);
const extra = ref({})
let selectedIncomes = null
const confirmTitle = ref(null)
const confirmText = ref(null)
const confirmButtons = ref(null)
const questionPopUpVisible = ref(false)
let contextMenuIsVisible = false
let buttons = {}
const initialLoading = ref(false)

initializeData();

function headerButtonPressedHandler(buttonName) {
  if ( !(buttonName in buttons) ) return;
  buttons[buttonName].action();
}
function openIncomeEditorFromHeader() {
  if (!selectedIncomes || selectedIncomes.length !== 1) return;
  jumpToIncomeView(selectedIncomes[0]);
}
function selectAll() {
  checkboxVisible.value = true
  incomes.value.list.map(item => item.selected = true)
}
function deselectAll() {
  checkboxVisible.value = false
  incomes.value.list.map(item => item.selected = false)
}
function getSelectedIncome() {
  if (selectedIncomes?.length === 1) {
    return selectedIncomes[0]
  }
  return extra.value.item ?? null
}
function getSelectedIncomes() {
  if (selectedIncomes?.length > 0) {
    return selectedIncomes
  }
  return extra.value.item ? [extra.value.item] : null
}
function showHeader() {
  buttons = {
    "Selecionar todos":       { action: selectAll, icon: ClipboardDocumentCheckIconSolid    },
    "Desmarcar todos":        { action: deselectAll, icon: ClipboardDocumentCheckIcon  },
    "Editar":                 { action: openIncomeEditorFromHeader, icon: PencilSquareIcon },
    "Excluir":                { action: showPopUpQuestion, icon: TrashIcon },
  };
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
  const selectedCount = incomes.value.list.reduce((total, item) => total + (item.selected === true ? 1 : 0), 0)
  switch (selectedCount) {
    case 0:
      hideCheckboxes()
      buttons = {}
      updateHeaderOptions(buttons)
      break;
    case 1:
      showHeader();
      break;
    case incomes.value.list.length:
      buttons = {
        "Desmarcar todos": { action: deselectAll, icon: ClipboardDocumentCheckIcon },
        "Excluir":         { action: showPopUpQuestion, icon: TrashIcon },
      };
      updateHeaderOptions(buttons)
      break;
    default:
      buttons = {
        "Selecionar todos":       { action: selectAll, icon: ClipboardDocumentCheckIconSolid },
        "Desmarcar todos":        { action: deselectAll, icon: ClipboardDocumentCheckIcon },
        "Excluir":                { action: showPopUpQuestion, icon: TrashIcon },
      };
      updateHeaderOptions(buttons)
  }
}
function selectionController() {
  manageHeaderButtonsAndCheckboxes();
  updateSelectedIncomes();
}
function clickIncomeHandler(income) {
  if (selectedIncomes.length > 0) {
    income.selected = !income.selected
    return;
  }
  if (contextMenuIsVisible) return;
  openIncome(income)
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
async function deleteMultipleIncomes(incomes) {
  try {
    await store.dispatch('expenses/deleteMultipleIncomes', incomes);
    reloadIncomesList();
  } finally {
    hidePopUpQuestion();
  }
}
async function deleteIncome(income) {
  try {
    await store.dispatch('expenses/deleteIncome', income);
    reloadIncomesList();
  } finally {
    hidePopUpQuestion();
  }
}
function hidePopUpQuestion() {
  questionPopUpVisible.value = false
}
function removeIncomes() {
  const incomes = getSelectedIncomes()
  if (incomes === null) {
    console.error("Unable to get the selected expense")
    hidePopUpQuestion()
    return;
  }
  if (incomes.length === 1) {
    deleteIncome(incomes[0]);
    return;
  }
  deleteMultipleIncomes(incomes)
}
function confirmButtonPressedHandler(buttonName) {
  if (!buttonName in confirmButtons.value) return;
  confirmButtons.value[buttonName].action();
}
function makeQuestionParams() {
  const multiple = selectedIncomes?.length > 1
  if (multiple) {  
    confirmTitle.value = "Remover receitas";
    confirmText.value = "Você tem certeza que deseja remover essas receitas?";
  }
  else {
    confirmTitle.value = "Remover receita";
    confirmText.value = "Você tem certeza que deseja remover essa receita?";
  }
  confirmButtons.value = {
    "yes": { "text": "Sim", type: "primary",   action: removeIncomes },
    "no":  { "text": "Não", type: "secondary", action: hidePopUpQuestion },
  };
}
function showPopUpQuestion() {
  makeQuestionParams()
  questionPopUpVisible.value = true
}
function openExpenseEditorFromContextMenu() {
  if (!extra.value.item) return;
  jumpToIncomeView(extra.value.item);
}
function jumpToIncomeView(income) {
  router.push({
    name: "IncomeEditor",
    params: {
      id: income.id
    }
  })
}
function showCheckboxes() {
  checkboxVisible.value = true
  extra.value.item.selected = true
}
function hideCheckboxes() {
  checkboxVisible.value = false
}
function addSelectedField() {
  incomes.value.list.map(i => i.selected = false)
}
async function reloadIncomesList() {
  incomes.value = await loadIncomes()
  addSelectedField()
}
function updateSelectedIncomes() {
  selectedIncomes = incomes.value.list.filter(cat => cat.selected)
}
async function updateDoneStatusMultiple(expenses, doneStatus) {
  await store.dispatch('expenses/updateDoneStatusMultiple', {expenses: expenses, done: doneStatus})
  .then(response => {
    return response
  })
}
async function markAsDone() {
  const incomes = getSelectedIncomes()
  if (incomes === null) {
    console.error("Unable to get the selected incomes")
    return;
  }
  let expenses = []
  incomes.map(cat => {
    expenses = [ ...expenses, ...cat.expenses ]
  })
  await updateDoneStatusMultiple(expenses, true)
  reloadIncomesList()
}
async function markAsNotDone() {
  const incomes = getSelectedIncomes()
  if (incomes === null) {
    console.error("Unable to get the selected incomes")
    return;
  }
  let expenses = []
  incomes.map(cat => {
    expenses = [ ...expenses, ...cat.expenses ]
  })
  await updateDoneStatusMultiple(expenses, false)
  reloadIncomesList()
}
function renderContextMenu(event, item) {
  contextMenuOptions.value = {
    "1": {text: "Selecionar", action: showCheckboxes},
    "2": {text: "Editar",     action: openExpenseEditorFromContextMenu},
    "3": {text: "Excluir",    action: showPopUpQuestion},
  }
  extra.value.item = item
  extra.value.event = event
  forceContextMenuRerendering()
}
function forceContextMenuRerendering() {
  reloadContextMenu.value += 1
}
function jumpTo(destination) {
  router.push(destination)
}
async function initializeData() {
  initialLoading.value = true
  incomes.value = await loadIncomes();
  addSelectedField()
  ensureHeaderIsNotVisible()
  initialLoading.value = false
}
async function loadIncomes() {
    const returnValue = await store.dispatch('expenses/getAllIncomes');
    return returnValue;
}
function openIncome(income) {
  router.push({
    name: "IncomeEditor",
    params: { id: income.id }
  });
}
watch(() => incomes, () => {
  selectionController()
}, {deep: true})
</script>
<style scoped>
.monthlyincomes-container {
  display: block;
  width: 100%;
  height: calc(100% - 4em);
  padding: 1em;
}
.action-btn {
  min-width: 60px;
  padding: 5px;
}
.panel-container>.income-card-container {
  width: calc(50% - 0.375em);
  margin: 0.375em;
  margin-left: 0;
  margin-right: 0;
}
@media (max-width: 1024px) {
  .panel-container {
    flex-direction: column;
  }
  .panel-container>div,
  .panel-container>.income-card-container {
    width: 100%;
  }
}
</style>
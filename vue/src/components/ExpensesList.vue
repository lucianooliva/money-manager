<template>
  <ContextMenu :key="reloadContextMenu" :contextMenuOptions="contextMenuOptions" @on-click-option="onClickContextMenuOption" :extra="extra" @visibility-changed="visibilityChangeHandler"/>
  <div class="panel-container">
    <div class="panel shadow-md rounded-lg pt-6 w-full overflow-auto">
      <div class="panel-header px-6 pb-1">
        <button @click="addExpense" class="bg-primary text-white mr-0 py-2 px-4 rounded-md hover:bg-primary-darker">Nova despesa</button>
      </div>
      <div v-if="!expenses || !expenses.length">
        <NoDataFiller :message="'Nenhuma despesa'" :icon="ClipboardDocumentListIcon"></NoDataFiller>
      </div>
      <ul class="list-container py-6" v-if="expenses && expenses.length">
        <li class="list-item px-6 py-2" :key="li.id" v-for="li in expenses" @contextmenu.prevent="renderContextMenu($event, li)" :class="{'done-expense': li.done}">
          <div class="select-col" v-show="checkboxVisible">
            <label class="relative flex items-center cursor-pointer">
              <input type="checkbox" class="hidden" v-model="li.selected" />
              <div class="relative w-8 h-8 border rounded-md border-gray-400 mr-3" :class="{'checkbox-selected': li.selected}"></div>
              <svg class="absolute checkmark w-7 h-7" :class="{'hidden': !li.selected}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
              </svg>
            </label>
          </div>
          <div class="name-desc-value flex flex-row">
            <div class="primary-col" @click="clickItemHandler(li)">
              <p class="item-title text-xl py-2">{{li.name}}</p>
              <span class="py-2">{{li.description}}</span>
            </div>
            <div class="secondary-col" @click="clickItemHandler(li)">
              <span class="text-xl">R$ {{li.value}}</span>
            </div>
          </div>
          <div class="menu-col" @click.prevent="renderContextMenu($event, li)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 hover:bg-gray-200">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
            </svg>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <QuestionPopUp v-if="questionPopUpVisible" :title="confirmTitle" :text="confirmText" :buttons="confirmButtons" @pop-up-option-selected="confirmButtonPressedHandler"></QuestionPopUp>
</template>

<script setup>
import { ref, watch } from 'vue'
import router from '../router'
import store from '../store'
import moneyUtils from '../utils/money'
import ContextMenu from "./ContextMenu.vue"
import QuestionPopUp from './QuestionPopUp.vue';
import { CheckBadgeIcon, ClipboardDocumentCheckIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ClipboardDocumentCheckIcon as ClipboardDocumentCheckIconSolid, CheckBadgeIcon as CheckBadgeIconSolid, ClipboardDocumentListIcon } from '@heroicons/vue/24/solid';
import NoDataFiller from './NoDataFiller.vue';

const props = defineProps(['eventFromHome', 'category'])
const emit = defineEmits(['itemSelected', 'expenseRemoved'])
const expenses = ref([])
const checkboxVisible = ref(false);
let selectedExpenses = null
const confirmTitle = ref(null)
const confirmText = ref(null)
const confirmButtons = ref(null)
const questionPopUpVisible = ref(false)
const extra = ref({})
let buttons = {}
let contextMenuIsVisible = false
const contextMenuOptions = ref({})
const reloadContextMenu = ref(0)


initialize()


async function initialize() {
  expenses.value = await loadExpenses(props.category.id)
  addSelectedField()
  convertValueToString()
  ensureHeaderIsNotVisible()
}
function convertValueToString() {
  expenses.value.map((obj) => obj.value = moneyUtils.convertMoneyValueToStringPtBr(obj.value))
}
async function loadExpenses(categoryId) {
  return await store.dispatch('expenses/getAllExpensesOfACategory', categoryId)
  .then(returnValue => {
    return returnValue
  })
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
async function updateDoneStatus(expense, doneStatus) {
  await store.dispatch('expenses/updateDoneStatus', {id: expense.id, done: doneStatus})
  .then(response => {
    return response
  })
}
async function updateDoneStatusMultiple(expenses, doneStatus) {
  await store.dispatch('expenses/updateDoneStatusMultiple', {expenses: expenses, done: doneStatus})
  .then(response => {
    return response
  })
}
async function reloadExpensesList() {
  expenses.value = await loadExpenses(props.category.id)
  addSelectedField()
  convertValueToString()
}
async function markAsDone() {
  const selectedExpenses = getSelectedExpenses()
  if (selectedExpenses === null) {
    console.error("Unable to get the selected expense")
    return;
  }
  if (selectedExpenses.length === 1) {
    await updateDoneStatus(selectedExpenses[0], true);
    reloadExpensesList()
    return;
  }
  await updateDoneStatusMultiple(selectedExpenses, true)
  reloadExpensesList()
}
async function markAsNotDone() {
  const selectedExpenses = getSelectedExpenses()
  if (selectedExpenses === null) {
    console.error("Unable to get the selected expense")
    return;
  }
  if (selectedExpenses.length === 1) {
    await updateDoneStatus(selectedExpenses[0], false);
    reloadExpensesList()
    return;
  }
  await updateDoneStatusMultiple(selectedExpenses, false)
  reloadExpensesList()
}
function ensureHeaderIsNotVisible() {
  buttons = {}
  emit('itemSelected', buttons)
}
function confirmButtonPressedHandler(buttonName) {
  if (!buttonName in confirmButtons.value) return;
  confirmButtons.value[buttonName].action();
}
function makeQuestionParams() {
  const multiple = selectedExpenses?.length > 1
  if (multiple) {  
    confirmTitle.value = "Remover despesas";
    confirmText.value = "Você tem certeza que deseja remover essas despesas?";
  }
  else {
    confirmTitle.value = "Remover despesa";
    confirmText.value = "Você tem certeza que deseja remover essa despesa?";
  }
  confirmButtons.value = {
    "yes": { "text": "Sim", type: "primary",   action: removeExpenses },
    "no":  { "text": "Não", type: "secondary", action: hidePopUpQuestion },
  };
}
function getSelectedExpense() {
  if (selectedExpenses?.length === 1) {
    return selectedExpenses[0]
  }
  return extra.value.item ?? null
}
function getSelectedExpenses() {
  if (selectedExpenses?.length > 0) {
    return selectedExpenses
  }
  return extra.value.item ? [extra.value.item] : null
}
function removeExpenses() {
  const expenses = getSelectedExpenses()
  if (expenses === null) {
    console.error("Unable to get the selected expense")
    hidePopUpQuestion()
    return;
  }
  if (expenses.length === 1) {
    deleteExpense(expenses[0]);
    return;
  }
  deleteMultipleExpenses(expenses)
}
async function deleteMultipleExpenses(expenses) {
  try {
    const updatedExpense = await store.dispatch('expenses/deleteMultipleExpenses', expenses);
    reloadExpensesList();
  } finally {
    hidePopUpQuestion();
  }
}
async function deleteExpense(expense) {
  try {
    await store.dispatch('expenses/deleteExpense', expense);
    reloadExpensesList();
  } finally {
    hidePopUpQuestion();
  }
}
function hidePopUpQuestion() {
  questionPopUpVisible.value = false
}
function showPopUpQuestion() {
  makeQuestionParams()
  questionPopUpVisible.value = true
}
function addSelectedField() {
  expenses.value.map(i => i.selected = false)
}
function showCheckboxes() {
  checkboxVisible.value = true
  extra.value.item.selected = true
}
function hideCheckboxes() {
  checkboxVisible.value = false
}
function openExpenseEditorFromHeader() {
  if (!selectedExpenses || selectedExpenses.length !== 1) return;
  jumpToExpenseEditor(selectedExpenses[0]);
}
function openExpenseEditorFromContextMenu() {
  if (!extra.value.item) return;
  jumpToExpenseEditor(extra.value.item);
}
function jumpToExpenseEditor(expense) {
  router.push({
    name: "ExpenseEditor",
    params: {
      id: expense.id
    }
  })
}
function renderContextMenu(event, item) {
  deselectAll()
  const doneText = item.done ? "Marcar como não concluída" : "Marcar como concluída"
  const doneAction = item.done ? markAsNotDone : markAsDone
  contextMenuOptions.value = {
    "1": {text: "Selecionar", action: showCheckboxes},
    "2": {text: "Editar",     action: openExpenseEditorFromContextMenu},
    "3": {text: "Excluir",    action: showPopUpQuestion},
    "4": {text: doneText,     action: doneAction},
  }
  extra.value.item = item
  extra.value.event = event
  if (checkboxVisible.value) {
    item.selected = true
  }
  forceContextMenuRerendering()
}
function forceContextMenuRerendering() {
  reloadContextMenu.value += 1
}
function showHeader() {
  const item = getSelectedExpense()
  const doneText = item.done ? "Marcar como não concluída" : "Marcar como concluída"
  const doneAction = item.done ? markAsNotDone : markAsDone
  const doneIcon = item.done ? CheckBadgeIcon : CheckBadgeIconSolid
  buttons = {
    "Selecionar todos": { action: selectAll, icon: ClipboardDocumentCheckIconSolid    },
    "Desmarcar todos":  { action: deselectAll, icon: ClipboardDocumentCheckIcon  },
    "Editar":           { action: openExpenseEditorFromHeader, icon: PencilSquareIcon },
    "Excluir":          { action: showPopUpQuestion, icon: TrashIcon },
  };
  buttons[doneText] = { action: doneAction, icon: doneIcon }
  emit('itemSelected', buttons)
}
function selectionController() {
  manageHeaderButtonsAndCheckboxes();
  updateSelectedExpenses();
}
function updateSelectedExpenses() {
  selectedExpenses = expenses.value.filter(exp => exp.selected)
}
function manageHeaderButtonsAndCheckboxes() {
  const selectedCount = expenses.value.reduce((total, item) => total + (item.selected === true ? 1 : 0), 0)
  const doneCount = expenses.value.reduce((total, item) => total + (item.selected === true && item.done === true ? 1 : 0), 0)
  switch (selectedCount) {
    case 0:
      hideCheckboxes()
      buttons = {}
      emit('itemSelected', buttons)
      break;
    case 1:
      showHeader();
      break;
    case expenses.value.length:
      buttons = {
        "Desmarcar todos":        { action: deselectAll, icon: ClipboardDocumentCheckIcon       },
        "Excluir":                { action: showPopUpQuestion, icon: TrashIcon },
      };
      if (selectedCount === doneCount) {
        buttons["Marcar como não concluídas"] = { action: markAsNotDone, icon: CheckBadgeIcon }
      }
      else {
        buttons["Marcar como concluídas"] = { action: markAsDone, icon: CheckBadgeIconSolid }
      }
      emit('itemSelected', buttons)
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
      emit('itemSelected', buttons)
  }
}
function eventFromHomeHandler(event) {
  if ( !event.event || !(event.event in buttons) ) return;
  buttons[event.event].action();
}
watch(() => expenses, () => {
  selectionController()
}, {deep: true})

watch(props.eventFromHome, () => {
  eventFromHomeHandler(props.eventFromHome)
}, {deep: true})

function clickItemHandler(item) {
  if (selectedExpenses?.length > 0) {
    item.selected = !item.selected
    return;
  }
  if (contextMenuIsVisible) return;
  viewItem(item)
}
function viewItem(item) {
  router.push(`/expense/${item.id}`)
}
function selectAll() {
  checkboxVisible.value = true
  expenses.value.map(item => item.selected = true)
}
function deselectAll() {
  checkboxVisible.value = false
  expenses.value.map(item => item.selected = false)
}
async function addExpense() {
  router.push({
    name: "ExpenseCreator"
  })
}
</script>

<style scoped>
.list-container {
  margin: 0;
}
.list-item:first-child {
  border-top: 1px #ddd solid;
}
.list-item {
  cursor: pointer;
  transition: background .18s ease-out;
  border-bottom: 1px #ddd solid;
  color: #333;
  display: flex;
  flex-direction: row;
}
.list-item:hover {
  background-color: #eee;
}
.name-desc-value {
  align-items: center;
  display: flex;
  flex-direction: row;
  flex-grow: 1;
}
.select-col {
  flex-grow: 0;
  overflow: hidden;
  padding: 14px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.select-col label div:not(.checkbox-selected):hover {
  background-color: #cdcdcd !important;
}
.select-col label div {
  padding: 0;
  margin: 0;
}
.primary-col {
  flex-grow: 2;
  overflow: hidden;
}
.secondary-col {
  flex-grow: 0;
  overflow: hidden;
  text-wrap: nowrap;
  min-width: 7em;
  text-align: right;
  padding: 1em;
}
.menu-col {
  flex-grow: 0;
  width: 36px;
  justify-content: right;
  display: flex;
  align-items: center;
}
@media (max-width: 360px) {
  .name-desc-value {
    flex-direction: column;
  }
  .menu-col {
    align-items: flex-start;
    padding-top: 0.5em;
  }
  .primary-col {
    width: 100%;
  }
  .secondary-col {
    width: 100%;
    padding-right: 0;
  }
}
ul {
  list-style-type: none;
}
.item-title {
  font-size: 1.5rem;
}
.checkmark {
  border-radius: 50%;
  stroke-width: 5px;
  stroke: white;
  stroke-miterlimit: 10;
  stroke-dashoffset: 0;
}
.checkbox-selected {
  /*background-color: hsl(0, 100%, 42%);*/
  background-color: #e3686d;
}
.done-expense {
  background-color: #d5ffd5;
}
.done-expense:hover {
  background-color: #bfe9bf;
}
</style>

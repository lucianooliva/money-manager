<template>
  <div class="income-card-container">
    <div class="card shadow hover:shadow-red-600" :class="{'cat-selected': props.income.selected}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="main-icon text-gray-400">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
      </svg>
      <div class="card-info">
        <div class="flex">
          <div class="name-col">
            <h2 :title="props.income.name">{{props.income.name}}</h2>
          </div>
          <div class="menu-col flex items-center" @click.prevent="informMenuClick($event, props.income)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 hover:bg-gray-200">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
            </svg>
          </div>
        </div>
        <p :title="props.income.description">{{props.income.description}}</p>
        <p class="price">R$ {{value}}</p>
      </div>
    </div>
  </div>
</template>
<script setup>

import { ref } from 'vue';
const props = defineProps(['income'])
const emit = defineEmits(['threeDotMenuClick'])

const value = ref(props.income.value.replace(/\./, ','))

function informMenuClick(event, income) {
  event.stopPropagation();
  emit('threeDotMenuClick', event, income)
}


</script>
<style scoped>
.income-card-container {
  width: 100%;
}
.card {
  background-color: #fff;
  border-radius: 8px;
  padding: 10px;
  box-sizing: border-box;
  display: flex;
  flex-direction: row;
}
.card .main-icon {
  padding: 10px 5px 10px 0px;
  height: 140px;
  border-radius: 8px;
  flex-grow: 0;
  min-width: 122px;
}
.card .card-info {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  padding: 10px 10px 10px 5px;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}
.card h2 {
  font-size: 1.5rem;
  margin-bottom: 10px;
}
.card p {
  color: #666;
  margin-bottom: 10px;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}
.card .price {
  font-size: 1.2rem;
  font-weight: bold;
  color: #333;
}
.cat-selected {
  border: 3px solid hsl(0, 100%, 42%);
  border: 3px solid #e3686d;
}
.menu-col {
  flex-grow: 0;
  min-width: 24px;
  text-align: right;
}
.name-col {
  flex-grow: 1;
  overflow: hidden;
  white-space: nowrap;
}
.name-col h2 {
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}
</style>
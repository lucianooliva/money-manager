<template>
  <div class="category-card-container">
    <div class="card relative shadow" :class="{'cat-selected': props.category.selected, 'hover-border': !props.category.selected}">
      <div class="flex w-full justify-center h-40">
        <div class="cat-thumb">
          <p data-testid="user-initials" aria-hidden="false" v-if="!props.category.icon">{{initials}}</p>
          <component :is="props.category.icon.value" v-if="props.category.icon"></component>
        </div>
      </div>
      <div class="flex">
        <div class="name-col">
          <h2 :title="props.category.name">{{props.category.name}}</h2>
        </div>
        <div class="menu-col flex items-center" @click.prevent="informMenuClick($event, props.category)" v-show="props.isVisible">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 hover:bg-gray-200">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
          </svg>
        </div>
      </div>
      <p :title="props.category.description" style="min-height: 24px;">{{props.category.description}}</p>
      <p class="price">R$ {{total}}</p>
      <div class="flex flex-row relative h-1" :title="donePercent + '% concluído'">
        <div class="bg-gray-200 w-full h-full h-8 rounded-lg overflow-hidden absolute">
          <div class="bg-green-400 h-full text-white text-center leading-none py-2" :style="{'width': donePercent+'%'}">
          </div>
          <span class="absolute left-2" style="top: 13%"></span>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue';
const props = defineProps(['category', 'isVisible'])
const emit = defineEmits(['threeDotMenuClick'])
const total = ref(props.category.total.replace(/\./, ','))
const donePercent = ref( calcPercentageOfDoneExpenses() )
const initials = ref( makeInitials(props.category.name) )

function makeInitials(name) {
  if (!name) return "";
  if (name.indexOf(' ') === -1) {
    return name.substring(0, 2).toUpperCase();
  }
  const parts = name.split(' ');
  if (!parts || parts.length < 1) return "";
  const firstNameInitial = parts[0][0].toUpperCase();
  const lastNameInitial = parts[parts.length - 1][0].toUpperCase();
  return `${firstNameInitial}${lastNameInitial}`;
}
function calcPercentageOfDoneExpenses() {
  const total = Math.round(parseFloat(props.category.total) * 100)
  let totalDone = Math.round(parseFloat(props.category.totalDone) * 100)
  return totalDone > 0 ? Math.round(totalDone / total * 100) : 0
}
function informMenuClick(event, category) {
  event.stopPropagation();
  emit('threeDotMenuClick', event, category)
}
watch(() => props.category, () => {
  donePercent.value = calcPercentageOfDoneExpenses()
}, {deep: true})

</script>
<style scoped>
.category-card-container {
  width: 100%;
}
.card {
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-sizing: border-box;
  border: 1px solid #eee;
}
.card.hover-border:hover {
  border: 1px solid red;
}

.card .main-icon {
  width: 100%;
  height: 140px;
  border-radius: 8px;
  margin-bottom: 10px;
}

.card h2 {
  font-size: 1rem;
}

.card p {
  color: #666;
  margin-bottom: 10px;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
  font-size: 0.75rem;
}

.card .price {
  font-size: 1.2rem;
  font-weight: bold;
  color: #333;
}

.cat-selected {
  /*border: 3px solid hsl(0, 100%, 42%);*/
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
.cat-thumb {
  color: gray;
  height: 8rem;
  width: 8rem;
  font-size: 3rem;
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, .07);
  border-radius: 50%;
  overflow: hidden;
  font-weight: 400;
  line-height: 1;
  align-items: center;
  display: flex;
  justify-content: center;
}
.cat-thumb p {
  color: rgba(0, 0, 0, .9);
}
</style>
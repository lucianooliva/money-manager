<template>
  <div id="questionpopup" class="fixed top-0 left-0 w-full h-full bg-gray-800 bg-opacity-50 flex justify-center items-center">
    <Transition>
      <div class="question-body bg-white rounded-lg shadow-lg p-6" v-show="show === 1">
        <h2 class="text-lg font-semibold mb-4">{{title}}</h2>
        <p class="mb-4">{{text}}</p>
        <div class="flex flex-row justify-end">
          <button :key="idx" v-for="(b, idx) in buttons" @click="clickHandler(idx)" :class="{'bg-primary': b.type==='primary', 'bg-gray-300': b.type==='secondary'}" class="mx-2 text-white px-4 py-2 rounded-lg hover:bg-primary-darker">
            {{ b.text }}
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { onMounted, ref, onUnmounted } from 'vue';

const props = defineProps(['title', 'text', 'buttons'])
const show = ref(0)

const emit = defineEmits(['popUpOptionSelected'])

async function clickHandler(idx) {
  show.value = 0
  emit('popUpOptionSelected', idx)
}
onMounted(() => show.value = 1);
</script>
<style scoped>
#questionpopup {
  z-index: 9999;
}
.question-body {
  transform: translateY(20px);
}
.v-enter-active,
.v-leave-active {
  transform: translateY(20px);
  transition: opacity 0.5s ease, transform 0.5s ease;
}
.v-enter-from,
.v-leave-to {
  opacity: 0;
  transform: translateY(-20px);
  transition: opacity 0.5s ease, transform 0.5s ease;
}

</style>
<template>
<div class="no-data-container text-bodybg-darker">
  <div>
    <div class="text-bodybg-darker text-center">
      <component :is="props.icon"/>
    </div>
    <b class="text-2xl text-nowrap">{{ props.message }}</b>
  </div>
</div>
</template>
<script setup>
const props = defineProps(['message', 'icon'])
</script>
<style scoped>

.no-data-container {
  display: flex;
  width: 100%;
  height: 100%;
  justify-content: center;
  align-items: center;
  padding: 2em;
}
.no-data-container>div {
  display: flex;
  align-items: center;
  flex-direction: column;
}
.no-data-container>div>div {
  width: 10em;
  height: 10em;
}
</style>
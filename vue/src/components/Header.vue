<template>
  <div class="top-options bg-primary" v-show="thereAreButtons()">
    <a class="text-options hover:bg-primary-lighter" v-for="(ho,index) in props.extra.headerOptions" :key="index" @click="onClickLink(index)">{{ index }}</a>
    <div class="icon-options hover:bg-primary-lighter" v-for="(ho,index) in props.extra.headerOptions" :key="index" @click="onClickLink(index)">
      <component :is="ho.icon"></component>
    </div>
  </div>
</template>
<script setup>
  const props = defineProps(['extra']);
  const emit = defineEmits([ "buttonPressed" ])
  
  function onClickLink(buttonName) {
    emit("buttonPressed", buttonName)
  }
  function thereAreButtons() {
    if (!props.extra.headerOptions) return false;
    return Object.keys(props.extra.headerOptions).length > 0
  }
  
  
  </script>
  <style>
  .top-options {
    align-items: center;
    /*background-color: hsl(0, 100%, 42%);*/
    border-right: 1px solid rgba(0, 0, 0, .1);
    box-shadow: 0 1px 4px 0 rgba(0,0,0,.32);
    cursor: pointer;
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    padding: 0 8px 0 1.125em;
    position: sticky;
    top: 0;
    width: 100%;
    height: 64px;
    z-index: 17;
  }
  .text-options {
    padding-right: 1em;
    padding-left: 1em;
    color: white;
    height: 100%;
    align-items: center;
    display: flex;
  }
  .icon-options {
    display: flex;
    width: 4em;
    padding-left: 1em;
    padding-right: 1em;
    justify-content: center;
    align-items: center;
    color: white;
    display: none;
    height: 100%;
  }
  @media (max-width: 950px) {
    .icon-options {
      display: flex;
    }
    .text-options {
      display: none;
    }
  }
  </style>
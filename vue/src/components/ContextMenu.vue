<template>
    <div>
      <div id="mycontextmenu" v-show="isContextMenuVisible" class="context-menu" :style="{ top: contextMenuTop + 'px', left: contextMenuLeft + 'px' }">
        <ul>
          <li v-for="(option, index) in props.contextMenuOptions" :key="index" @click="handleOptionClick(index)">
            <span>{{ option.text }}</span>
          </li>
        </ul>
      </div>
    </div>
  </template>
    
  <script setup>
  import { ref } from 'vue'
  
  const props = defineProps(['contextMenuOptions', 'extra']);
  const emit = defineEmits(['onClickOption', 'visibilityChanged'])
  let isContextMenuVisible = ref(false)
  let contextMenuLeft = ref(0)
  let contextMenuTop = ref(0)

  initialize();
  
  function initialize() {
    if (!props.extra?.event) {
      return;
    }
    showContextMenu(props.extra.event)
  }
  function closeContextMenu() {
    isContextMenuVisible.value = false
    emit('visibilityChanged', false)
    document.body.removeEventListener("click", closeContextMenu)
  }
  async function showContextMenu(event) {
    isContextMenuVisible.value = true
    emit('visibilityChanged', true)
    const contextMenuHeight = Object.keys(props.contextMenuOptions).length * 40
    const contextMenuWidth = 232
    const viewPortHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
    const viewPortWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    const cursorYPosition = event.clientY
    const cursorXPosition = event.clientX
    contextMenuLeft.value = cursorXPosition + contextMenuWidth < viewPortWidth ? cursorXPosition : viewPortWidth - contextMenuWidth
    contextMenuTop.value = cursorYPosition + contextMenuHeight < viewPortHeight ? cursorYPosition : viewPortHeight - contextMenuHeight
    setTimeout( () => document.body.addEventListener("click", closeContextMenu), 500 )
  }
  function handleOptionClick(optionName) {
    isContextMenuVisible.value = false
    emit('onClickOption', optionName)
  }
  </script>
    
  <style>
    .context-menu {
      position: fixed;
      background-color: #ffffff;
      border: 1px solid #ccc;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      z-index: 1000;
      cursor: default;
    }
    
    .context-menu ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }
    
    .context-menu ul li {
      padding: 8px 12px;
    }
    
    .context-menu ul li:hover {
      background-color: #f0f0f0;
    }
  </style>
    
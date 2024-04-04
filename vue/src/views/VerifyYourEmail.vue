<template>
  <div class="verifyyouremail-container">
    <div class="panel-container flex justify-center w-full">
      <div class="panel shadow-md rounded-lg p-6 flex flex-col gap-5 text-center">
        <p v-if="!emailSent" class="text-left">{{message}}</p>
        <button type="button" @click="sendVerificationEmail" v-if="!emailSent && timer <= 0" class="bg-primary text-white py-2 px-4 rounded-md hover:bg-primary-darker">
          <Spinner class="spinner" v-if="loading"/>
          {{ loading ? '' : 'Enviar e-mail de verificação' }}
        </button>
        <p v-if="emailSent">E-mail enviado. Verifique a sua caixa de entrada. Caso não localize, busque na caixa de spam.</p>
        <button type="button" @click="recheckEmail" v-if="emailSent" class="bg-good-green text-white py-2 px-4 rounded-md hover:bg-good-green-darker">
          <Spinner class="spinner" v-if="loading"/>
          {{ loading ? '' : 'Já completei a verificação' }}
        </button>
        <button type="button" v-if="recheckedEmail && timer > 0" class="bg-gray-200 text-black py-2 px-4 rounded-md">
          <Spinner class="spinner" v-if="loading"/>
          {{ loading ? '' : 'Aguarde '+timer+' segundos' }}
        </button>
      </div>
    </div>
    <a @click="logout" class="absolute left-0 bottom-0 p-3 hover:text-primary flex gap-1 cursor-pointer">
      <span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
        </svg>
      </span>
      <span>Sair</span>
    </a>
  </div>
</template>
<script setup>
import { onUnmounted, ref } from 'vue';
import router from '../router';
import store from '../store';
import Spinner from '../components/Spinner.vue';

const emailSent = ref(false)
const message = ref(makeInitialMessage())
const recheckedEmail = ref(false)
const timer = ref(0)
const loading = ref(false)
let timerInterval = null
let checkInterval = null

initialize();

function resetTimer() {
  if (timerInterval) {
    clearInterval(timerInterval)
  }
  timer.value = 10//60
  timerInterval = setInterval(() => timer.value--, 1000)
}
function resetCheckInterval() {
  if (checkInterval) {
    clearInterval(checkInterval)
  }
  checkInterval = setInterval(async () => {
    const returnValue = await checkIfEmailHasBeenVerified()
    if (returnValue.emailVerified) {
      jumpTo('/home')
      return;
    }
  }, 10000)
}
async function initialize() {
  const returnValue = await checkIfEmailHasBeenVerified();
  if (returnValue.emailVerified) {
    router.push({
      name: 'Home'
    })
  }
}
function logout() {
  store.dispatch('auth/logout')
  .then(res => {
    router.push({
      name: 'Login'
    })
  })
  .catch(err => {
    console.error(err);
  })
  
}
async function sleep() {
    const returnValue = [
      {id: 1},
      {id: 2},
      {id: 3},
    ]
    return new Promise(resolve => {
    setTimeout(() => {
      resolve(returnValue);
    }, 3000);
  });    
}
async function sendVerificationEmail() {
  loading.value = true
  //await sleep()
  await store.dispatch('auth/verifyEmail')
  loading.value = false
  recheckedEmail.value = false
  emailSent.value = true
  resetTimer()
  resetCheckInterval()
}
async function checkIfEmailHasBeenVerified() {
  loading.value = true
  const res = await store.dispatch('auth/checkIfEmailHasBeenVerified');
  loading.value = false
  return res;
}
function jumpTo(destination) {
  router.push(destination)
}
async function recheckEmail() {
  recheckedEmail.value = true
  emailSent.value = false
  message.value = makeErrorMessage()
  const returnValue = await checkIfEmailHasBeenVerified()
  if (returnValue.emailVerified) {
    jumpTo('/home')
    return;
  }
}
function makeErrorMessage() {
  return 'Não foi possível verificar o seu e-mail. Tente novamente.'
}
function makeInitialMessage() {
  return 'Obrigado pelo seu cadastro! Antes de prosseguir, \
clique no botão abaixo para receber o e-mail de verificação. Siga as \
instruções do e-mail para completar o seu cadastro e acessar o sistema.'
}
onUnmounted(() => {
  if (checkInterval) {
    clearInterval(checkInterval);
  }
  if (timerInterval) {
    clearInterval(timerInterval);
  }
});
</script>
<style scoped>
.verifyyouremail-container {
  position: relative;
  padding: 1em;
  height: 100vh;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.spinner {
  height: auto;
  width: 1em;
}
.panel button {
  min-height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
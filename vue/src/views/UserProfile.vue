<template>
  <div class="userprofile-container">
    <div class="panel-container">
      <div class="panel shadow-md rounded-lg p-6 w-full">
        <div class="relative">
          <h2 class="text-xl font-semibold mb-4">Seu perfil</h2>
          <form @submit.prevent="saveUserProfile">
            <div class="form-info flex mt-4 text-sm text-left">
              <label>Nome</label>
              <input v-model="userProfile.name" ref="userprofileNameInput" type="text" id="userprofile-name" name="userprofile-name" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
            </div>
            <div class="form-info flex mt-4 text-sm text-left">
              <label>E-mail</label>
              <input v-model.lazy="userProfile.email" type="email" id="userprofile-email" name="userprofile-email" class="form-input w-full block rounded-md focus:border-indigo-500" autocomplete="on"  required>
            </div>
            <div class="form-info flex mt-4 mb-4 text-sm text-left">
              <label for="userprofile-description">Verificação de e-mail</label>
              <button v-show="userProfile.emailVerifiedAt" type="button" class="py-2 px-4 rounded text-black-600 bg-gray-200 hover:bg-gray-100">Email verificado com sucesso</button>
              <button @click="verifyEmail" v-show="!userProfile.emailVerifiedAt" type="button" class="py-2 px-4 rounded text-black-600 bg-gray-200 hover:bg-gray-100">Verificar e-mail</button>
            </div>
            <div class="form-info flex mt-4 mb-4 text-sm text-left">
              <label for="userprofile-description">Senha</label>
              <button type="button" class="py-2 px-4 rounded text-black-600 bg-gray-200 hover:bg-gray-100">Alterar senha</button>
            </div>
            <div class="form-footer">
              <button type="submit" class="bg-primary text-white mr-2 py-2 px-4 rounded-md hover:bg-primary-darker">Salvar</button>
              <button @click="$router.go(-1)" type="button" class="py-2 px-4 rounded text-primary hover:text-primary hover:bg-gray-100">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </template>
  <script setup>
  import { onMounted, ref } from "vue"
  import router from "../router"
  import store from "../store"
  
  const userprofileNameInput = ref(null)
  const userProfile = ref({
    name: null,
    email: null,
    emailVerifiedAt: null
  })
  
  initializeData();
  onMounted(()=>userprofileNameInput.value.focus())
  
  async function initializeData() {
    userProfile.value = await getUserProfile()
  }
  async function getUserProfile() {
    try {
      const returnValue = await store.dispatch('auth/getUserProfile', userProfile.value.email)
      return returnValue
    } catch (err) {
      console.error(err)
    }
  }
  function updateUserProfile() {
    const exp = userProfile.value
    exp.value = turnValueNumeric(userProfile.value.value)
    store.dispatch('auth/updateUserProfile', userProfile.value)
      .then(updatedUserProfile => {
        router.go(-1)
      })
  }
  function saveUserProfile() {
    updateUserProfile();
  }
  function verifyEmail() {
    store.dispatch('auth/verifyEmail')
  }
  </script>
  <style scoped>
  .userprofile-container {
    display: block;
    width: 100%;
    padding: 1em;
  }
  .form-input {
    padding-bottom: 5px;
  }
  </style>
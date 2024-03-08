<template>
    <div class="register-container">
      <div class="bg-white p-8 rounded shadow-md max-w-md w-full">
        <h2 class="text-2xl font-semibold text-center mb-4">Register</h2>
        <form @submit.prevent="handleSubmit" class="max-w-md mx-auto">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="user.name" type="name" name="name" id="name" @blur="handleBlur('name')" @keyup="handleKeyUp('name')" :class="{ 'border-red-500': emailInvalid }" class="register-input w-full mt-1 block rounded-md focus:border-indigo-500" autocomplete="on" required>
            <span class="text-red-500 text-sm" v-show="emailInvalid">Digite um nome válido</span>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input v-model="user.email" type="email" name="email" id="email" @blur="handleBlur('email')" @keyup="handleKeyUp('email')" :class="{ 'border-red-500': emailInvalid }" class="register-input w-full mt-1 block rounded-md focus:border-indigo-500" autocomplete="on" required>
            <span class="text-red-500 text-sm" v-show="emailInvalid">Digite um email válido</span>
          </div>
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input v-model="user.password" type="password" name="password" id="password" @blur="handleBlur('password')" @keyup="handleKeyUp('password')" :class="{ 'border-red-500': passwordInvalid }" class="register-input w-full mt-1 block rounded-md focus:border-indigo-500" autocomplete="off" required>
            <span class="text-red-500 text-sm" v-show="passwordInvalid">Digite uma senha</span>
          </div>
          <div class="mb-4">
            <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input v-model="user.confirmPassword" type="password" name="confirm-password" id="confirm-password" @blur="handleBlur('confirmPassword')" @keyup="handleKeyUp('confirmPassword')" :class="{ 'border-red-500': confirmPasswordInvalid }" class="register-input w-full mt-1 block rounded-md focus:border-indigo-500" autocomplete="off" required>
            <span class="text-red-500 text-sm" v-show="confirmPasswordInvalid">A confirmação de senha não pode ser em branco e deve ser igual à senha digitada anteriormente</span>
          </div>
          <div class="flex items-center justify-between">
            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Register</button>
            <p class="text-gray-600">Already have an account? <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a></p>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  import { useRouter } from "vue-router"
  import store from "../store"
  
  const router = useRouter();
  
  
  
  const user = ref( { name: null, email: null, password: null, confirmPassword: null } )
  const nameInvalid = ref(false)
  const emailInvalid = ref(false)
  const passwordInvalid = ref(false)
  const confirmPasswordInvalid = ref(false)
  function handleBlur(field) {
    const name = user.value.name
    const email = user.value.email
    const password = user.value.password
    const confirmPassword = user.value.confirmPassword
    const settings = {
      name: () => nameInvalid.value = !name,
      email: () => emailInvalid.value = !email,
      password: () => passwordInvalid.value = !password,
      confirmPassword: () => confirmPasswordInvalid.value = !confirmPassword
    }
    if ( !(field in settings) ) return;
    settings[field]()
  }
  function handleKeyUp(field) {
    const email = user.value.email
    const password = user.value.password
    const confirmPassword = user.value.confirmPassword
    const settings = {
      email: () => emailInvalid.value = !email,
      password: () => {
        passwordInvalid.value = !password;
        confirmPasswordInvalid.value = password !== confirmPassword
      },
      confirmPassword: () => confirmPasswordInvalid.value = !confirmPassword || password !== confirmPassword
    }
    if ( !(field in settings) ) return;
    settings[field]()
  }
  const handleSubmit = () => {
    if (user.value.password !== user.value.confirmPassword) {
      alert('Passwords do not match');
      return;
    }
    store.dispatch('register', user.value)
      .then(() => {
        router.push({
          name: 'Home'
        })
      })
  }
  
  </script>
  
  <style>
  .register-container {
    width: 100%;
    min-height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .register-input {
    height: 46px;
    padding: 4px 10px;
    border: 1px solid rgb(240, 240, 240);
    border-bottom: 1px solid rgb(210, 210, 210);
  }
  </style>
  
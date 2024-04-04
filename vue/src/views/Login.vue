<template>
    <div class="login-container">
      <div class="bg-white p-8 rounded shadow-md max-w-md w-full">
        <h2 class="text-2xl font-semibold text-center mb-4">Login</h2>
        <form @submit.prevent="login">
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input type="email" id="email" name="email" v-model="user.email" @blur="handleBlur('email')" :class="{ 'border-red-500': emailInvalid }" class="login-input w-full mt-1 block rounded-md focus:border-indigo-500" autocomplete="on"  required>
            <span class="text-red-500 text-sm" v-show="emailInvalid">Digite um email válido</span>
          </div>
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" v-model="user.password" @blur="handleBlur('password')" :class="{ 'border-red-500': passwordInvalid }" class="login-input w-full mt-1 block rounded-md focus:border-indigo-500" autocomplete="off" required>
            <span class="text-red-500 text-sm" v-show="passwordInvalid">Digite uma senha</span>
          </div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" v-model="user.rememberMe" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
            <div class="text-sm">
              <a href="#" class="font-medium text-primary hover:text-primary-lighter">Forgot your password?</a>
            </div>
          </div>
          <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-primary-darker">Login</button>
        </form>
        <div class="mt-4 text-sm text-center">
          <p class="text-gray-600">Don't have an account? <a href="/register" class="font-medium text-primary hover:text-primary-lighter">Register</a></p>
        </div>
      </div>
    </div>
  </template>
    
  <script setup>
  import { ref } from "vue"
  import { useRouter } from "vue-router"
  import store from "../store"
  
  const router = useRouter();
  
  
  
  const user = ref( { email: null, password: null, rememberMe: false } )
  const emailInvalid = ref(false)
  const passwordInvalid = ref(false)
  const errorMsg = ref(null)
  
  function handleBlur(field) {
    const settings = {
      email: () => emailInvalid.value = !user.value.email,
      password: () => passwordInvalid.value = !user.value.password
    }
    settings[field]()
  }
  function login() {
    store.dispatch('auth/login', user.value)
      .then(() => {
        router.push({
          name: 'Home'
        })
      })
      .catch(err => {
        errorMsg.value = err.response?.data.error
      })
  
  }
    
  </script>
  <style scoped>
  .login-container {
    width: 100%;
    min-height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .login-input {
    height: 46px;
    padding: 4px 10px;
    border: 1px solid rgb(240, 240, 240);
    border-bottom: 1px solid rgb(210, 210, 210);
  }
  </style>
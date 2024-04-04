<template>
  <aside class="aside-sidebar">
    <a @click="jumpTo('/user')" class="profile-header-container">
      <div class="profile-header-content bg-primary">
        <div class="back-container w-14 ml-0" @click="goBack()">
          <ArrowLeftIcon class="text-white pr-3"/>
        </div>
        <div class="thumb-container">
          <div class="thumb thumb--circle thumb--48 thumb--neutral">
            <p data-testid="user-initials" aria-hidden="false">{{initials}}</p>
          </div>
        </div>
        <div class="profile-header-welcome">
          <div class="profile-header-hello">
            <p class="profile-header-name u-truncate">Olá, {{userName}}</p>
          </div>
          <div class="profile-header-your-profile u-truncate">
            <span>Seu perfil</span>
          </div>
        </div>
      </div>
    </a>
    <nav class="nav-sidebar">
      <div class="sidebar-section">
        <a class="sidebar-section-link is-active" @click="jumpTo('/home')" :class="{'current-section': curPage === '/home', 'hover:bg-bodybg': curPage !== '/home'}">
          <span class="sidebar-section-link-icon nav-icon-home">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
          </svg>
          </span>
          <span class="sidebar-section-link-text">Início</span>
        </a><hr>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-section-collaps-dropdown ">
          <a class="sidebar-section-link" @click="jumpTo('/monthly-bills')" :class="{'current-section': curPage === '/monthly-bills', 'hover:bg-bodybg': curPage !== '/monthly-bills'}">
            <span class="sidebar-section-link-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
              </svg>
            </span>
            <span class="sidebar-section-link-text">Despesas mensais</span>
          </a>
          <a class="sidebar-section-link" @click="jumpTo('/monthly-incomes')" :class="{'current-section': curPage === '/monthly-incomes', 'hover:bg-bodybg': curPage !== '/monthly-incomes'}">
            <span class="sidebar-section-link-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
              </svg>
            </span>
            <span class="sidebar-section-link-text">Receitas mensais</span>
          </a>
        </div>
        <hr>
      </div>
      <!-- Logout -->
      <div class="sidebar-section bottom">
        <a @click="logout" class="sidebar-section-link is-active hover:text-primary">
          <span class="sidebar-section-link-icon nav-icon-home">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
            </svg>
          </span>
          <span class="sidebar-section-link-text">Sair</span>
        </a><hr>
      </div>
    </nav>
  </aside>
  <div class="bottom-menu fixed bottom-0 left-0 w-full shadow-md mx-auto px-4 py-2 flex justify-around">
    <div class="flex flex-col justify-center align-center cursor-pointer hover:bg-bodybg-darker" @click="jumpTo('/home')">
      <HomeIcon class="h-6 w-6" v-if="$route.fullPath !== '/home'"></HomeIcon>
      <HomeIconSolid class="h-6 w-6" v-if="$route.fullPath === '/home'"></HomeIconSolid>
      <a class="text-sm mr-4">Início</a>
    </div>
    <div class="flex flex-col justify-center align-center cursor-pointer hover:bg-bodybg-darker" @click="jumpTo('/monthly-bills')">
      <TableCellsIcon class="h-6 w-6" v-if="$route.fullPath !== '/monthly-bills'"></TableCellsIcon>
      <TableCellsIconSolid class="h-6 w-6" v-if="$route.fullPath === '/monthly-bills'"></TableCellsIconSolid>
      <a class="text-sm mr-4">Despesas</a>
    </div>
    <div class="flex flex-col justify-center align-center cursor-pointer hover:bg-bodybg-darker" @click="jumpTo('/monthly-incomes')">
      <BanknotesIcon class="h-6 w-6" v-if="$route.fullPath !== '/monthly-incomes'"></BanknotesIcon>
      <BanknotesIconSolid class="h-6 w-6" v-if="$route.fullPath === '/monthly-incomes'"></BanknotesIconSolid>
      <a class="text-sm">Receitas</a>
    </div>
    <div class="flex flex-col justify-center align-center cursor-pointer hover:bg-bodybg-darker" @click="jumpTo('/user-menu')">
      <Bars3Icon class="h-6 w-6" v-if="$route.fullPath !== '/user-menu'"></Bars3Icon>
      <Bars3IconSolid class="h-6 w-6" v-if="$route.fullPath === '/user-menu'"></Bars3IconSolid>
      <a class="text-sm">Menu</a>
    </div>
  </div>

  <div class="page-container">
    <router-view></router-view>
  </div>
</template>
  
<script setup>

import { ref } from "vue"

import { useRouter, useRoute } from "vue-router"
import store from "../store"
import { HomeIcon, TableCellsIcon, BanknotesIcon, Bars3Icon, ArrowLeftIcon } from "@heroicons/vue/24/outline";
import { HomeIcon as HomeIconSolid, TableCellsIcon as TableCellsIconSolid, BanknotesIcon as BanknotesIconSolid, Bars3Icon as Bars3IconSolid } from "@heroicons/vue/24/solid";

const router = useRouter()
const userName = ref(getFirstName(store.state.auth.user.data.name))
const initials = ref(getInitials(store.state.auth.user.data.name))
let curPage = ref(useRoute().path)

function getFirstName(fullName) {
  if (!fullName) return "";
  const parts = fullName.trim().split(' ')
  if (!parts || parts.length < 1) return "";
  return parts[0];
}
function getInitials(fullName) {
  if (!fullName) return "";
  if (fullName.indexOf(' ') === -1) {
    return fullName.substring(0, 2).toUpperCase();
  }
  const parts = fullName.split(' ');
  if (!parts || parts.length < 1) return "";
  const firstNameInitial = parts[0][0].toUpperCase();
  const lastNameInitial = parts[parts.length - 1][0].toUpperCase();
  return `${firstNameInitial}${lastNameInitial}`;
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
function jumpTo(destination) {
  router.push(destination)
  curPage = destination
}
function goBack() {
  router.go(-1)
}
</script>
  
<style scoped>
.aside-sidebar {
  bottom: 0;
  height: 100%;
  left: 0em;
  min-height: 100vh;
  overflow: hidden;
  position: fixed;
  top: 0;
  user-select: none;
  visibility: visible;
  width: 16em;
  z-index: 80;
}
.bottom-menu {
  display: none;
  z-index: 9999;
  background-color: #ede6d8;
  color: black;
  height: 5em;
}
.bottom-menu div {
  padding-right: 1em;
  padding-left: 1em;
  height: 100%;
  display: flex;
  align-items: center;
}
.bottom-menu div a {
  color: black;
  text-align: center;
  margin-right: 0;
  font-size: 0.75em;
}
*, :after, :before {
  box-sizing: border-box;
}
aside {
  display: block;
}
.page-container {
  padding-left: 16em;
  width: 100%;
}
@media (max-width: 768px) {
  aside {
    display: none;
  }
  .page-container {
    padding-left: 0;
    padding-bottom: 5em;
    width: 100%;
  }
  .bottom-menu {
    display: flex;
  }
}
body {
  margin: 0;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  -webkit-tap-highlight-color: rgba(0, 0, 0, .1);
}
:root {
  --swiper-navigation-size: 44px;
}
:root {
  --swiper-theme-color: #007aff;
}
html {
  text-size-adjust: 100%;
}
.nav-sidebar {
  position: relative;
  background-color: #ede6d8;
  overflow: auto;
  padding-bottom: 1em;
  padding-top: 1em;
}
.nav-sidebar {
  border-right: 1px solid rgba(0, 0, 0, .1);
  height: calc(100% - 64px);
  width: 100%;
}
.sidebar-section {
  display: flex;
  flex-direction: column;
}
.sidebar-section-collaps-dropdown, .sidebar-section-collaps-dropdown.expanded {
  height: auto;
  transition: max-height .4s cubic-bezier(.73,-.02,.46,1);
}
.sidebar-section-collaps-dropdown {
  max-height: 11.625em;
  overflow-y: hidden;
}

.sidebar-section-link {
  box-shadow: inset 0.25em 0 0 transparent;
  color: #333;
  cursor: pointer;
  max-height: 2.875em;
  padding: 16px 0 16px 24px;
  position: relative;
  text-decoration: none;
  transition: background .12s ease-out, box-shadow .12s ease-out, color .12s ease-out;
}
.sidebar-section-link {
  align-items: center;
  display: flex;
}

.sidebar-section-link-icon {
  align-items: center;
  display: flex;
  height: 1.5em;
  justify-content: center;
  margin: 0 16px 0 0;
  width: 1.5em;
}

.sidebar-section-link-text {
  display: flex;
  font-size: 14px;
  font-weight: 600;
  line-height: 1em;
  width: 100%;
}
.profile-header-content {
  align-items: center;
  box-shadow: 0 1px 4px 0 rgba(0,0,0,.32);
  cursor: pointer;
  display: flex;
  justify-content: flex-start;
  padding: .625em 8px .625em 1.125em;
  position: relative;
  width: 100%;
  z-index: 2;
}

@media (min-width: 1024px) {
  .profile-header-content {
    height: 4em;
  }
}

.thumb.thumb--48 {
  font-size: 20px;
}
.profile-header-container .thumb--circle {
  border: none;
  z-index: 9999;
}
.profile-header-container .thumb {
  height: 32px;
  width: 32px;
}
.thumb--neutral {
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, .07);
}
.thumb--circle {
  border: 1px solid rgba(0, 0, 0, .07);
  border-radius: 50%;
  overflow: hidden;
}
.thumb {
  color: rgba(0, 0, 0, .9);
  font-weight: 400;
  line-height: 1;
}
.thumb--48 {
  height: 46px;
  width: 46px;
}
.thumb--40, .thumb--48 {
  align-items: center;
  background-color: #fff;
  display: flex;
  justify-content: center;
}
.thumb--neutral {
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, .07);
}
.thumb--circle {
  border: 1px solid rgba(0, 0, 0, .07);
  border-radius: 50%;
  overflow: hidden;
}
.thumb {
  color: rgba(0, 0, 0, .9);
  font-weight: 400;
  line-height: 1;
}
.thumb--48 {
  height: 46px;
  width: 46px;
}
.thumb--40, .thumb--48 {
  align-items: center;
  background-color: #fff;
  display: flex;
  justify-content: center;
}
a {
  color: #009ee3;
  text-decoration: none;
}
p {
  font-size: 14px;
}
.profile-header-welcome {
  align-items: flex-start;
  display: flex;
  flex-direction: column;
  justify-content: center;
  margin-left: 12px;
  width: 100%;
}
.profile-header-hello {
  display: flex;
}
.u-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.profile-header-name {
  font-size: 16px;
  font-weight: 600;
  line-height: 1.25;
  max-width: 9.375em;
}

.profile-header-your-profile, .profile-header-name {
  color: #fff;
}
.profile-header-your-profile span {
  font-size: 14px;
  font-weight: 400;
  line-height: 1.1428571429;
  padding-right: .375em;
}
.bottom {
  position: absolute;
  bottom: 0;
  display: flex;
  background-color: inherit;
  width: 100%;
}
.current-section {
  background-color: hsl(0, 0%, 100%);
  box-shadow: inset 0.25em 0 0 black;
}
</style>
import {createStore} from "vuex";

const store = createStore({
  state: {
    user: {
      data:{
        name: null
      },
      token: sessionStorage.getItem("TOKEN")
    }
  },
  getters: {},
  actions: {
    login: async ({commit}, user) => {
      const requestBody = JSON.stringify({
        email: user.email,
        password: user.password
      })
      const url = `http://127.0.0.1:8000/api/login`
      const res = await fetch(`${url}`, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json"
        },
        method: "POST",
        body: requestBody
      });
      const authResponse = await res.json();
      commit("setUser", authResponse);
      return authResponse;
    },
    register: async ({commit}, user) => {
      const requestBody = JSON.stringify({
        name: user.name,
        email: user.email,
        password: user.password,
        password_confirmation: user.confirmPassword
      })
      const url = `http://127.0.0.1:8000/api/register`
      const res = await fetch(`${url}`, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json"
        },
        method: "POST",
        body: requestBody
      });
      const authResponse = await res.json();
      commit("setUser", authResponse);
      return authResponse;
    },
    
  },
  mutations: {
    setUser: (state, userData) => {
      state.user.token = userData.token;
      state.user.data = userData.user;
      sessionStorage.setItem("TOKEN", userData.token)
    },
    logout: state => {
      state.user.data = {}
      state.user.token = null
      sessionStorage.removeItem("TOKEN")
    }
  },
  modules: {}
});

export default store;

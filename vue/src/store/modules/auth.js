import axiosClient from "../../server/axios";

export default {
  namespaced: true,
  state: {
    user: {
      data:{
        name: localStorage.getItem("userName")
      },
      token: localStorage.getItem("TOKEN"),
      verifiedEmail: localStorage.getItem("verifiedEmail")
    },
  },
  getters: {},
  actions: {
    login: async ({commit}, user) => {
      const requestBody = JSON.stringify({
        email: user.email,
        password: user.password,
        remember: user.rememberMe
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
      commit("setVerifiedEmail", authResponse.user.email_verified_at);
      
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
      authResponse.user.email_verified_at = null
      commit("setUser", authResponse);
      commit("setVerifiedEmail", authResponse.user.email_verified_at);
      return authResponse;
    },
    logout: ({commit}) => {
      return axiosClient.post(`/logout`)
      .then(res => {
        console.log(res);
        commit('logout')
      },
      msg => {
        console.error(msg);
      })
    },
    getUserProfile: ({commit}) => {
      return axiosClient.get(`/user`)
      .then(res => {
        return res.data;
      },
      msg => {
        console.error(msg);
      })
    },
    verifyEmail: ({commit}) => {
      return axiosClient.post(`/email/send`)
      .then(
        res => res.data,
        msg => console.error(msg)
      )
    },
    checkIfEmailHasBeenVerified: ({commit}) => {
      return axiosClient.get(`/email/verify`)
      .then(
        res => {
          commit('setVerifiedEmail', res.data.emailVerified ? true : null)
          return res.data;
        },
        msg => console.error(msg)
      )
    }
    
  },
  mutations: {
    setUser: (state, userData) => {
      state.user.token = userData.token;
      state.user.data = userData.user;
      localStorage.setItem("TOKEN", userData.token)
      localStorage.setItem("userName", userData.user.name)
    },
    setVerifiedEmail: (state, email_verified_at) => {
      const emailVerified = email_verified_at !== null ? 'true' : 'false'
      state.user.verifiedEmail = emailVerified
      localStorage.setItem("verifiedEmail", emailVerified)
    },
    logout: state => {
      state.user.data = {}
      state.user.token = null
      localStorage.removeItem("TOKEN")
      localStorage.removeItem("userName")
      localStorage.removeItem("verifiedEmail")
    }
  },
  modules: {}
};
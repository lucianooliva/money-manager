import {createStore} from "vuex";
import auth from "./modules/auth.js";
import expenses from "./modules/expenses.js";

const store = createStore({
  modules: {
    auth, expenses
  }
});

export default store;

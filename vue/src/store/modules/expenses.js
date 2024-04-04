import axiosClient from "../../server/axios";

export default {
  namespaced: true,
  state: {
    categoryId: null
  },
  actions: {
    newCategory: ({commit}, category) => {
      const requestBody = {
        name: category.name,
        description: category.description,
        icon: category.icon.name,
      }
      return axiosClient.post(`/expense-category`, requestBody)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    newExpense: async ({commit}, expense) => {
      const requestBody = {
        name: expense.name,
        description: expense.description,
        done: false,
        category_id: expense.category_id,
        value: expense.value,
        notes: "-"
      }
      try {
        const res = await axiosClient.post(`/expense`, requestBody);
        return res.data.data;
      } catch (error) {
        if (error.response?.status === 422) {
          const msg = error.response?.data.message
          if (msg === "The name has already been taken."
          || msg === "validation.unique_for_user") {
            return { error: "Duplicate name" };
          }
          else if (msg === "The category id field is required.") {
            return { error: "Missing category_id" };
          }
        }
      }
    },
    newIncome: ({commit}, income) => {
      const requestBody = {
        name: income.name,
        description: income.description,
        value: income.value
      }
      return axiosClient.post(`/income`, requestBody)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    getAllCategories: () => {
      return axiosClient.get(`/expense-category`)
      .then(res => {
        return res.data.data;
      },
      err => {
        console.error(err);
      })
    },
    getAllExpensesOfACategory: async ({commit}, categoryId) => {
      return axiosClient.get(`/expense/category/${categoryId}`)
      .then(res => {
        return res.data.data;
      },
      err => {
        console.error(err);
      })
    },
    getCategoryWithExpenses: async ({commit}, categoryId) => {
      try {
        const res = await axiosClient.get(`/expense-category-with-expenses/${categoryId}`);
        return res.data.data;
      } catch (error) {
        if (error.response?.status === 403) {
          return { errorCode: 403 };
        }
      }
    },
    getExpense: async ({commit}, expenseId) => {
      try {
        const res = await axiosClient.get(`/expense/${expenseId}`);
        return res.data.data;
      } catch (error) {
        if (error.response?.status === 403) {
          return { errorCode: 403 };
        }
      }
    },
    getAllIncomes: () => {
      return axiosClient.get(`/income`)
      .then(res => {
        return res.data;
      },
      err => {
        console.error(err);
      })
    },    
    getIncome: async ({commit}, incomeId) => {
      try {
        const res = await axiosClient.get(`/income/${incomeId}`);
        return res.data.data;
      } catch (error) {
        if (error.response?.status === 403) {
          return { errorCode: 403 };
        }
      }
    },
    updateCategory: async ({commit}, category) => {
      const requestBody = {
        name: category.name,
        description: category.description,
        icon: category.icon.name,
      }
      return axiosClient.put(`/expense-category/${category.id}`, requestBody)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    updateExpense: async ({commit}, expense) => {
      const requestBody = {
        name: expense.name,
        description: expense.description,
        value: expense.value,
        done: expense.done,
        category_id: expense.category_id
      }
      return axiosClient.put(`/expense/${expense.id}`, requestBody)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    updateIncome: async ({commit}, income) => {
      const requestBody = {
        name: income.name,
        description: income.description,
        value: income.value
      }
      return axiosClient.put(`/income/${income.id}`, requestBody)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    deleteMultipleCategories: async ({commit}, categories) => {
      const ids = []
      categories.map(item => ids.push(item.id))
      const requestBody = { ids }
      return axiosClient.post(`/expense-category/delete-multiple`, requestBody)
      .then(res => {
        return res;
      },
      msg => {
        console.error(msg);
      })
    },
    deleteMultipleIncomes: async ({commit}, incomes) => {
      const ids = []
      incomes.map(item => ids.push(item.id))
      const requestBody = { ids }
      return axiosClient.post(`/income/delete-multiple`, requestBody)
      .then(res => {
        return res;
      },
      msg => {
        console.error(msg);
      })
    },
    deleteCategory: async ({commit}, category) => {
      return axiosClient.delete(`/expense-category/${category.id}`)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    deleteMultipleExpenses: async ({commit}, expenses) => {
      const ids = []
      expenses.map(item => ids.push(item.id))
      const requestBody = { ids }
      return axiosClient.post(`/expense/delete-multiple`, requestBody)
      .then(res => {
        return res;
      },
      msg => {
        console.error(msg);
      })
    },
    deleteExpense: async ({commit}, expense) => {
      return axiosClient.delete(`/expense/${expense.id}`)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    deleteIncome: async ({commit}, income) => {
      return axiosClient.delete(`/income/${income.id}`)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    updateDoneStatus: async ({commit}, values) => {
      const requestBody = {
        done: values.done
      }
      return axiosClient.patch(`/expense/${values.id}`, requestBody)
      .then(res => {
        return res.data.data;
      },
      msg => {
        console.error(msg);
      })
    },
    updateDoneStatusMultiple: async ({commit}, values) => {
      const ids = []
      values.expenses.map(item => ids.push(item.id))
      const requestBody = {
        ids: ids,
        done: values.done,
      }
      return axiosClient.post(`/expense/update-done-status-multiple`, requestBody)
      .then(res => {
        return res;
      },
      msg => {
        console.error(msg);
      })
    },

  },
  mutations: {
    setCategoryId: (state, value) => {
      state.categoryId = value;
    },
  },
};
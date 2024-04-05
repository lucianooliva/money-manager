import axios from 'axios';
import store from '../store';
import { backendBaseURL } from '../../config'
const axiosClient = axios.create({
	baseURL: backendBaseURL
})
axiosClient.interceptors.request.use(config => {
	config.headers.Authorization = `Bearer ${store.state.auth.user.token}`
	return config;
})
export default axiosClient;
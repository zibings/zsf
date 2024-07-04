import axios from 'axios';
import type { AxiosInstance } from 'axios';

let api: AxiosInstance | null;

export function createApi(url: string | null) {
	api = axios.create({
		baseURL: url ?? "",
		withCredentials: true,
	});

	return api;
}

export function useApi() {
	if (!api) {
		api = createApi(null);
	}

	return api;
}

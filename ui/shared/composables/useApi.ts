import axios from 'axios';
import type { AxiosInstance } from 'axios';

let api: AxiosInstance | null;

export function createApi(url: string | null): AxiosInstance {
	api = axios.create({
		baseURL: url ?? "",
		withCredentials: true,
	});

	return api;
}

export function useApi(): AxiosInstance {
	if (!api) {
		api = createApi(null);
	}

	return api;
}

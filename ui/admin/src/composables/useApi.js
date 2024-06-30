import axios from "axios";

let api;

export function createApi(url) {
	// Here we set the base URL for all requests made to the api
	api = axios.create({
		baseURL: url ?? "",
	});

	return api;
}

export function useApi() {
	if (!api) {
		createApi();
	}
	return api;
}

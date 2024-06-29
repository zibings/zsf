import Axios from "axios";

function apiCreate(url, headers) {
	Axios.defaults.baseURL = url ?? "";
	Axios.defaults.headers = headers ?? {};

	return Axios;
}

export { apiCreate };

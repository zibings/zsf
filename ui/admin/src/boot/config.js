export function configParse(conf) {
	const config = conf ?? {};

	// Options defaults for $admConfig
	config.api = config.api ?? {};
	config.api.useApi = config.api.useApi ?? true;
	config.api.baseUrl = config.api.baseUrl ?? null;
	config.api.headers = config.api.headers ?? {};
	config.api.openApiUrl = config.api.openApiUrl ?? null;

	config.displayNames = config.displayNames ?? {};
	config.displayNames.title = config.displayNames?.title ?? "Admin";
	config.displayNames.header = config.displayNames?.header ?? "ADMIN";

	config.environment = config.environment ?? "development";
	config.ripple = config.ripple ?? false;
	config.menuMode = config.menuMode ?? "static";
	config.inputStyle = config.inputStyle ?? "outlined";

	config.theme = config.theme ?? "arya-blue";
	config.darkMode = config.darkMode ?? true;

	config.darkTheme = config.darkTheme ?? "arya-blue";
	config.lightTheme = config.lightTheme ?? "saga-blue";

	return config;
}

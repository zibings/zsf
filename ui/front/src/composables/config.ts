interface ApiConfig {
	useApi: boolean;
	baseUrl: string;
}

interface SiteConfig {
	environment: 'development' | 'staging' | 'production';
	api: ApiConfig;
}

export function parseConfig(conf: any): SiteConfig {
	return {
		environment: conf.environment ?? 'development',
		api: {
			useApi: conf.api.useApi ?? false,
			baseUrl: conf.api.baseUrl ?? '/',
		},
	};
};

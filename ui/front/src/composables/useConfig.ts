interface ApiConfig {
	useApi: boolean;
	baseUrl: string;
}

interface SiteConfig {
	environment: 'development' | 'staging' | 'production';
	api: ApiConfig;
}

/* eslint-disable  @typescript-eslint/no-explicit-any */
export function useConfig(conf: any): SiteConfig {
	return {
		environment: conf.environment ?? 'development',
		api: {
			useApi: conf.api.useApi ?? false,
			baseUrl: conf.api.baseUrl ?? '/',
		},
	};
};

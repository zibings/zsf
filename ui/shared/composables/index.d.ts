import type { AxiosInstance } from 'axios';

export declare function createApi(url: string): AxiosInstance;
export declare function useApi(): AxiosInstance;
export { default as useAuthGuard } from './useAuthGuard';

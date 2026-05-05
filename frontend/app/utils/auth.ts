export type ServiceAccessState = 'ready' | 'loading' | 'telegram-required' | 'auth-error'

export function canUseDevInitData(hostname: string, devInitData?: string | null, isDev = false): boolean {
	if (!devInitData) return false
	if (isDev) return true

	return hostname === 'localhost' || hostname === '127.0.0.1'
}

export function getServiceAccessState(options: {
	isAuthenticated: boolean
	isLoading: boolean
	isInTelegram: boolean
	hasAuthError: boolean
}): ServiceAccessState {
	if (options.isAuthenticated) return 'ready'
	if (options.isLoading) return 'loading'
	if (options.isInTelegram) return options.hasAuthError ? 'auth-error' : 'loading'
	return 'telegram-required'
}

export function resolveRuntimeApiBase(apiBase: string | undefined, protocol: string): string {
	if (!apiBase) return '/api'
	if (protocol === 'https:' && apiBase.startsWith('http://')) return '/api'

	return apiBase
}

export function assertStaticApiBase(html: string): void {
	if (!html.includes('apiBase:"/api"')) {
		throw new Error('Static HTML must bake same-origin /api as public apiBase')
	}

	if (/apiBase:"http:\/\//.test(html)) {
		throw new Error('Static HTML must not bake insecure absolute apiBase values')
	}
}

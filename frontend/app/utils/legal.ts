export interface LegalLink {
	key: 'terms' | 'privacy' | 'safety'
	to: string
}

export function getAyanLegalLinks(): LegalLink[] {
	return [
		{ key: 'terms', to: '/legal/ayan-terms' },
		{ key: 'privacy', to: '/legal/privacy' },
		{ key: 'safety', to: '/legal/ayan-safety' }
	]
}

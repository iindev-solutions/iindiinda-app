export type LegalScope = 'platform' | 'ayan' | 'uus' | 'tal' | 'agal'

export interface LegalLink {
	key: string
	label: string
	to: string
}

export interface LegalLinkGroup {
	title: string
	description: string
	links: LegalLink[]
}

export function getPlatformLegalLinks(t: (key: string) => string): LegalLink[] {
	return [
		{ key: 'center', label: t('legal.links.center'), to: '/legal' },
		{ key: 'userAgreement', label: t('legal.links.userAgreement'), to: '/legal/user-agreement' },
		{ key: 'privacy', label: t('legal.links.privacy'), to: '/legal/privacy' },
		{ key: 'dataConsent', label: t('legal.links.dataConsent'), to: '/legal/data-consent' },
		{ key: 'support', label: t('legal.links.support'), to: '/legal/support' }
	]
}

export function getAyanLegalLinks(t: (key: string) => string): LegalLink[] {
	return [
		{ key: 'ayanRules', label: t('legal.links.ayanRules'), to: '/legal/ayan-terms' },
		{ key: 'safety', label: t('legal.links.safety'), to: '/legal/ayan-safety' },
		{ key: 'privacy', label: t('legal.links.privacy'), to: '/legal/privacy' },
		{ key: 'support', label: t('legal.links.support'), to: '/legal/support' }
	]
}

export function getUusLegalLinks(t: (key: string) => string): LegalLink[] {
	return [
		{ key: 'uusRules', label: t('legal.links.uusRules'), to: '/legal/uus-rules' },
		{ key: 'privacy', label: t('legal.links.privacy'), to: '/legal/privacy' },
		{ key: 'support', label: t('legal.links.support'), to: '/legal/support' }
	]
}

export function getTalLegalLinks(t: (key: string) => string): LegalLink[] {
	return [
		{ key: 'talRules', label: t('legal.links.talRules'), to: '/legal/tal-rules' },
		{ key: 'privacy', label: t('legal.links.privacy'), to: '/legal/privacy' },
		{ key: 'support', label: t('legal.links.support'), to: '/legal/support' }
	]
}

export function getAgalLegalLinks(t: (key: string) => string): LegalLink[] {
	return [
		{ key: 'agalRules', label: t('legal.links.agalRules'), to: '/legal/agal-rules' },
		{ key: 'privacy', label: t('legal.links.privacy'), to: '/legal/privacy' },
		{ key: 'support', label: t('legal.links.support'), to: '/legal/support' }
	]
}

export function getLegalLinksByScope(scope: LegalScope, t: (key: string) => string): LegalLink[] {
	if (scope === 'ayan') return getAyanLegalLinks(t)
	if (scope === 'uus') return getUusLegalLinks(t)
	if (scope === 'tal') return getTalLegalLinks(t)
	if (scope === 'agal') return getAgalLegalLinks(t)

	return getPlatformLegalLinks(t)
}

export function getLegalLinkGroups(t: (key: string) => string): LegalLinkGroup[] {
	return [
		{
			title: t('legal.center.groups.platform.title'),
			description: t('legal.center.groups.platform.description'),
			links: getPlatformLegalLinks(t)
		},
		{
			title: t('legal.center.groups.ayan.title'),
			description: t('legal.center.groups.ayan.description'),
			links: getAyanLegalLinks(t)
		},
		{
			title: t('legal.center.groups.uus.title'),
			description: t('legal.center.groups.uus.description'),
			links: getUusLegalLinks(t)
		},
		{
			title: t('legal.center.groups.tal.title'),
			description: t('legal.center.groups.tal.description'),
			links: getTalLegalLinks(t)
		},
		{
			title: t('legal.center.groups.agal.title'),
			description: t('legal.center.groups.agal.description'),
			links: getAgalLegalLinks(t)
		}
	]
}

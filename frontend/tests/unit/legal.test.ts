import { describe, expect, it } from 'vitest'

import { getLegalLinkGroups, getLegalLinksByScope } from '../../app/utils/legal'

const t = (key: string) => `label:${key}`

describe('legal links', () => {
	it('returns platform routes for platform scope', () => {
		expect(getLegalLinksByScope('platform', t)).toEqual([
			{ key: 'center', label: 'label:legal.links.center', to: '/legal' },
			{ key: 'userAgreement', label: 'label:legal.links.userAgreement', to: '/legal/user-agreement' },
			{ key: 'privacy', label: 'label:legal.links.privacy', to: '/legal/privacy' },
			{ key: 'dataConsent', label: 'label:legal.links.dataConsent', to: '/legal/data-consent' },
			{ key: 'support', label: 'label:legal.links.support', to: '/legal/support' }
		])
	})

	it('returns AYAN service rules for ayan scope', () => {
		expect(getLegalLinksByScope('ayan', t)).toEqual([
			{ key: 'ayanRules', label: 'label:legal.links.ayanRules', to: '/legal/ayan-terms' },
			{ key: 'safety', label: 'label:legal.links.safety', to: '/legal/ayan-safety' },
			{ key: 'privacy', label: 'label:legal.links.privacy', to: '/legal/privacy' },
			{ key: 'support', label: 'label:legal.links.support', to: '/legal/support' }
		])
	})

	it('builds legal center groups for platform and four services', () => {
		const groups = getLegalLinkGroups(t)

		expect(groups).toHaveLength(5)
		expect(groups.map((group) => group.title)).toEqual([
			'label:legal.center.groups.platform.title',
			'label:legal.center.groups.ayan.title',
			'label:legal.center.groups.uus.title',
			'label:legal.center.groups.tal.title',
			'label:legal.center.groups.agal.title'
		])
	})
})

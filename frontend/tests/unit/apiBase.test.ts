import { describe, expect, it } from 'vitest'

import { assertStaticApiBase, resolveRuntimeApiBase } from '../../app/utils/api-base'

describe('api base guards', () => {
	it('normalizes insecure runtime api base to same-origin on https pages', () => {
		expect(resolveRuntimeApiBase('http://89.22.226.34/api', 'https:')).toBe('/api')
	})

	it('keeps explicit runtime api base on non-https pages', () => {
		expect(resolveRuntimeApiBase('http://89.22.226.34/api', 'http:')).toBe('http://89.22.226.34/api')
	})

	it('keeps same-origin runtime api base unchanged', () => {
		expect(resolveRuntimeApiBase('/api', 'https:')).toBe('/api')
	})

	it('accepts generated html with same-origin api base', () => {
		expect(() =>
			assertStaticApiBase('<script>window.__NUXT__.config={public:{apiBase:"/api"}}</script>')
		).not.toThrow()
	})

	it('rejects generated html with insecure absolute api base', () => {
		expect(() =>
			assertStaticApiBase('<script>window.__NUXT__.config={public:{apiBase:"http://89.22.226.34/api"}}</script>')
		).toThrow(/same-origin \/api/)
	})
})

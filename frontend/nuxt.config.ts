// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	extends: ['./services/agal', './services/ayan', './services/tal', './services/uus'],
	modules: ['@nuxt/eslint', '@nuxt/ui', '@nuxtjs/i18n', '@nuxt/fonts'],
	ssr: false,

	// components: [
	// 	{
	// 		path: '~/components',
	// 		pathPrefix: false
	// 	}
	// ],

	app: {
		head: {
			// title: 'TMA catalog',
			charset: 'utf-8',
			viewport: 'width=device-width, initial-scale=1',
			script: [
				{
					src: 'https://telegram.org/js/telegram-web-app.js'
				}
			]
		},
		// for static deploy gh-pages need to baseURL: '/<repository-name>/'
		baseURL: '/',
		buildAssetsDir: 'assets'
	},

	css: ['~/assets/css/main.css'],

	colorMode: {
		preference: 'dark',
		fallback: 'dark'
	},

	ui: {
		theme: {
			colors: ['cyan', 'gray']
		}
	},

	// spa loader
	spaLoadingTemplate: 'spa-loader.html',

	runtimeConfig: {
		public: {
			apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api'
		}
	},

	compatibilityDate: '2026-01-19',
	hooks: {
		'prerender:routes'({ routes }) {
			routes.clear()
		}
	},

	eslint: {
		config: {
			stylistic: {
				commaDangle: 'never',
				braceStyle: '1tbs'
			}
		}
	},

	i18n: {
		locales: [
			{
				code: 'ru',
				name: 'Русский',
				file: 'ru.json'
			},
			{
				code: 'sah',
				name: 'Саха тыла',
				file: 'sah.json'
			}
		],
		defaultLocale: 'ru',
		strategy: 'no_prefix',
		detectBrowserLanguage: false
	}
})

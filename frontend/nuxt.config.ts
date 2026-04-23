export default defineNuxtConfig({
	extends: ['./services/ayan', './services/agal', './services/tal', './services/uus'],
	modules: ['@nuxt/eslint', '@nuxt/ui', '@nuxtjs/i18n', '@nuxt/fonts'],
	ssr: false,

	app: {
		head: {
			charset: 'utf-8',
			viewport: 'width=device-width, initial-scale=1',
			script: [
				{
					src: 'https://telegram.org/js/telegram-web-app.js'
				}
			]
		},
		baseURL: '/',
		buildAssetsDir: 'assets'
	},

	css: ['~/assets/css/main.css'],

	colorMode: {
		preference: 'dark',
		fallback: 'dark'
	},

	spaLoadingTemplate: 'spa-loader.html',

	runtimeConfig: {
		public: {
			apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
			telegramBotId: process.env.NUXT_PUBLIC_TELEGRAM_BOT_ID || '',
			devInitData: process.env.NUXT_PUBLIC_DEV_INIT_DATA || ''
		}
	},

	experimental: {
		defaults: {
			nuxtLink: {
				prefetchOn: { visibility: true, interaction: true }
			}
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

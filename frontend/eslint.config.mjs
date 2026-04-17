// @ts-check
import withNuxt from './.nuxt/eslint.config.mjs'
import eslintPluginPrettierRecommended from 'eslint-plugin-prettier/recommended'

export default withNuxt([
	eslintPluginPrettierRecommended,
	{
		rules: {
			'prettier/prettier': ['error', { printWidth: 120 }],
			// 'vue/max-len': ['error', { code: 120 }],
			'vue/no-v-html': 'off',
			'@typescript-eslint/no-explicit-any': 'off'
		}
	}
])

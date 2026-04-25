import { readFile } from 'node:fs/promises'

const htmlFiles = ['.output/public/index.html', '.output/public/200.html', '.output/public/404.html']

function assertStaticApiBase(html) {
	if (!html.includes('apiBase:"/api"')) {
		throw new Error('Static HTML must bake same-origin /api as public apiBase')
	}

	if (/apiBase:"http:\/\//.test(html)) {
		throw new Error('Static HTML must not bake insecure absolute apiBase values')
	}
}

for (const file of htmlFiles) {
	const html = await readFile(new URL(`../${file}`, import.meta.url), 'utf8')
	assertStaticApiBase(html)
}

console.log('STATIC_API_BASE_OK')

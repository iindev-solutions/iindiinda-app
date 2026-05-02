import { spawn } from 'node:child_process'

function run(command, args, env = process.env) {
	return new Promise((resolve, reject) => {
		const child = spawn(command, args, {
			stdio: 'inherit',
			shell: process.platform === 'win32',
			env
		})

		child.on('exit', (code) => {
			if (code === 0) {
				resolve()
				return
			}

			reject(new Error(`${command} ${args.join(' ')} exited with code ${code}`))
		})

		child.on('error', reject)
	})
}

const env = {
	...process.env,
	NUXT_PUBLIC_API_BASE: '/api'
}

await run('npx', ['nuxt', 'build', '--preset', 'github_pages'], env)
await run('node', ['scripts/verify-static-api-base.mjs'])

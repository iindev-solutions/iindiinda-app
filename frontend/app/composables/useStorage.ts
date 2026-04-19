/**
 * Storage Composable
 *
 * Wrapper для localStorage с TypeScript типами.
 * Автоматически сериализует/десериализует JSON.
 */

export const useStorage = () => {
	const get = <T>(key: string): T | null => {
		if (import.meta.server) return null

		try {
			const item = localStorage.getItem(key)
			return item ? JSON.parse(item) : null
		} catch (error) {
			console.error('[useStorage] Get failed:', error)
			return null
		}
	}

	const set = <T>(key: string, value: T): void => {
		if (import.meta.server) return

		try {
			localStorage.setItem(key, JSON.stringify(value))
		} catch (error) {
			console.error('[useStorage] Set failed:', error)
		}
	}

	const remove = (key: string): void => {
		if (import.meta.server) return

		try {
			localStorage.removeItem(key)
		} catch (error) {
			console.error('[useStorage] Remove failed:', error)
		}
	}

	const clear = (): void => {
		if (import.meta.server) return

		try {
			localStorage.clear()
		} catch (error) {
			console.error('[useStorage] Clear failed:', error)
		}
	}

	return { get, set, remove, clear }
}

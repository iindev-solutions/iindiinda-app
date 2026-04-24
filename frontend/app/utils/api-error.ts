export function getApiErrorMessage(error: unknown, fallback: string): string {
	if (error && typeof error === 'object') {
		const maybeError = error as {
			data?: { message?: string; errors?: Record<string, string[] | string> }
			message?: string
		}

		if (maybeError.data?.message) return maybeError.data.message

		const firstValidationError = maybeError.data?.errors && Object.values(maybeError.data.errors)[0]
		if (typeof firstValidationError === 'string') return firstValidationError
		if (Array.isArray(firstValidationError) && firstValidationError[0]) return firstValidationError[0]

		if (maybeError.message) return maybeError.message
	}

	return fallback
}

export function sanitizePriceInput(value: string): string {
	return value.replace(/\D+/g, '')
}

export function parsePriceInput(value: string): number {
	const sanitized = sanitizePriceInput(value)
	return sanitized ? Number.parseInt(sanitized, 10) : 0
}

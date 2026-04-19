/**
 * Validation Utilities
 *
 * Reusable validators for forms.
 */

/**
 * Validates phone number (10-15 digits, optional +)
 */
export const isValidPhone = (phone: string): boolean => {
	return /^\+?[0-9]{10,15}$/.test(phone)
}

/**
 * Validates price (positive, less than 1M)
 */
export const isValidPrice = (price: number): boolean => {
	return price > 0 && price < 1000000
}

/**
 * Validates address (min 3 chars)
 */
export const isValidAddress = (address: string): boolean => {
	return address.trim().length >= 3
}

/**
 * Validates date string (ISO or parseable)
 */
export const isValidDate = (date: string): boolean => {
	return !isNaN(Date.parse(date))
}

/**
 * Validates time string (HH:MM format)
 */
export const isValidTime = (time: string): boolean => {
	return /^([01]\d|2[0-3]):([0-5]\d)$/.test(time)
}

/**
 * Validates seats count (1-8)
 */
export const isValidSeats = (seats: number): boolean => {
	return Number.isInteger(seats) && seats >= 1 && seats <= 8
}

/**
 * Validates comment length (max 500 chars)
 */
export const isValidComment = (comment: string): boolean => {
	return comment.length <= 500
}

/**
 * Validates Telegram username (alphanumeric + underscore, 5-32 chars)
 */
export const isValidTelegramUsername = (username: string): boolean => {
	return /^[a-zA-Z0-9_]{5,32}$/.test(username)
}

// ==========================================
// Price formatting
// ==========================================

export function formatPrice(price: number, currency = '₽'): string {
	return `${price.toLocaleString('ru-RU')} ${currency}`
}

// ==========================================
// Date formatting
// ==========================================

export function formatDate(dateStr: string, options?: Intl.DateTimeFormatOptions): string {
	const date = new Date(dateStr)
	const today = new Date()
	const tomorrow = new Date(today)
	tomorrow.setDate(tomorrow.getDate() + 1)

	// Check if today
	if (date.toDateString() === today.toDateString()) {
		return 'Сегодня'
	}

	// Check if tomorrow
	if (date.toDateString() === tomorrow.toDateString()) {
		return 'Завтра'
	}

	// Format date
	return date.toLocaleDateString('ru-RU', {
		day: 'numeric',
		month: 'short',
		...options
	})
}

export function formatDateFull(dateStr: string): string {
	const date = new Date(dateStr)
	return date.toLocaleDateString('ru-RU', {
		day: 'numeric',
		month: 'long',
		year: 'numeric'
	})
}

// ==========================================
// Time formatting
// ==========================================

export function formatTime(timeStr: string): string {
	// Handle time strings like '06:00', '14:30'
	const [hours, minutes] = timeStr.split(':')
	return `${hours}:${minutes}`
}

// ==========================================
// Relative time formatting
// ==========================================

export function formatRelativeTime(dateStr: string): string {
	const date = new Date(dateStr)
	const now = new Date()
	const diffMs = now.getTime() - date.getTime()
	const diffMins = Math.floor(diffMs / 60000)
	const diffHours = Math.floor(diffMs / 3600000)
	const diffDays = Math.floor(diffMs / 86400000)

	if (diffMins < 1) {
		return 'только что'
	}

	if (diffMins < 60) {
		return `${diffMins} мин. назад`
	}

	if (diffHours < 24) {
		return `${diffHours} ч. назад`
	}

	if (diffDays === 1) {
		return 'вчера'
	}

	if (diffDays < 7) {
		return `${diffDays} дн. назад`
	}

	return formatDate(dateStr)
}

// ==========================================
// Name utilities
// ==========================================

export function getInitials(firstName: string, username?: string | null): string {
	if (firstName) {
		return firstName.charAt(0).toUpperCase()
	}
	if (username) {
		return `@${username.charAt(0)}`
	}
	return '?'
}

export function formatDriverName(driver: { first_name: string; username?: string | null }): string {
	if (driver.username) {
		return `@${driver.username}`
	}
	return driver.first_name
}

// ==========================================
// Random utilities
// ==========================================

export function randomId(): string {
	return Math.random().toString(36).substring(2, 9)
}

// ==========================================
// Debounce utility
// ==========================================

export function debounce<T extends (...args: any[]) => any>(fn: T, delay: number): (...args: Parameters<T>) => void {
	let timeoutId: ReturnType<typeof setTimeout>
	return (...args: Parameters<T>) => {
		clearTimeout(timeoutId)
		timeoutId = setTimeout(() => fn(...args), delay)
	}
}

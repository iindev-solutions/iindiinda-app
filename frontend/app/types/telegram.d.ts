/**
 * Telegram WebApp SDK type declarations
 */
interface TelegramWebAppUser {
	id: number
	is_bot?: boolean
	first_name: string
	last_name?: string
	username?: string
	language_code?: string
	is_premium?: boolean
	photo_url?: string
}

interface TelegramWebAppInitDataUnsafe {
	query_id?: string
	user?: TelegramWebAppUser
	receiver?: TelegramWebAppUser
	start_param?: string
	auth_date?: number
	hash?: string
}

interface TelegramWebAppThemeParams {
	bg_color?: string
	text_color?: string
	hint_color?: string
	link_color?: string
	button_color?: string
	button_text_color?: string
	secondary_bg_color?: string
}

interface TelegramWebAppButton {
	text: string
	isVisible: boolean
	isActive: boolean
	show(): void
	hide(): void
	setText(text: string): void
	onClick(callback: () => void): void
	offClick(callback: () => void): void
}

interface TelegramWebAppBackButton {
	isVisible: boolean
	show(): void
	hide(): void
	onClick(callback: () => void): void
	offClick(callback: () => void): void
}

interface TelegramHapticFeedback {
	impactOccurred(style: 'light' | 'medium' | 'heavy' | 'rigid' | 'soft'): void
	notificationOccurred(type: 'error' | 'success' | 'warning'): void
	selectionChanged(): void
}

interface TelegramWebApp {
	initData: string
	initDataUnsafe: TelegramWebAppInitDataUnsafe
	version: string
	platform: string
	colorScheme: 'light' | 'dark'
	themeParams: TelegramWebAppThemeParams
	isExpanded: boolean
	viewportHeight: number
	viewportStableHeight: number
	headerColor: string
	backgroundColor: string
	MainButton: TelegramWebAppButton
	BackButton: TelegramWebAppBackButton
	HapticFeedback: TelegramHapticFeedback
	isClosingConfirmationEnabled: boolean
	ready(): void
	expand(): void
	close(): void
	setHeaderColor(color: string): void
	setBackgroundColor(color: string): void
	enableClosingConfirmation(): void
	disableClosingConfirmation(): void
}

interface Window {
	Telegram?: {
		WebApp?: TelegramWebApp
	}
}

/**
 * UI Types
 *
 * Общие типы для UI компонентов.
 */

export type ButtonVariant = 'primary' | 'secondary' | 'ghost' | 'danger' | 'success'

export type ButtonSize = 'sm' | 'md' | 'lg'

export type LoadingSize = 'sm' | 'md' | 'lg'

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface Toast {
	id: string
	type: ToastType
	message: string
	duration?: number
}

export type ModalSize = 'sm' | 'md' | 'lg' | 'xl' | 'full'

export interface ModalProps {
	title?: string
	size?: ModalSize
	closable?: boolean
	onClose?: () => void
}

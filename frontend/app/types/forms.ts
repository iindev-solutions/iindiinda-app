/**
 * Form Types
 *
 * Общие типы для форм и валидации.
 */

export interface FormField<T = any> {
	value: T
	error: string | null
	touched: boolean
	validate: () => boolean
}

export interface FormState {
	isValid: boolean
	isSubmitting: boolean
	errors: Record<string, string>
}

export type ValidationRule<T = any> = (value: T) => string | null

export interface FormConfig<T = any> {
	initialValues: T
	validationRules?: Partial<Record<keyof T, ValidationRule[]>>
	onSubmit: (values: T) => Promise<void> | void
}

export type UusCategory = 'home' | 'repair' | 'delivery' | 'other'
export type UusDesiredWhen = 'today' | 'tomorrow' | 'date' | 'flexible'
export type UusBudgetType = 'fixed' | 'negotiable'
export type UusUrgency = 'urgent' | 'normal'
export type UusTaskStatus = 'open' | 'matched' | 'completed' | 'cancelled'
export type UusResponseStatus = 'pending' | 'accepted' | 'rejected'

export interface UusUserSummary {
	id: number
	name: string
	username: string | null
}

export interface UusTask {
	id: number
	customer: UusUserSummary
	category: UusCategory
	description: string
	location: string
	desired_when: UusDesiredWhen
	date: string | null
	budget: number | null
	budget_type: UusBudgetType
	urgency: UusUrgency
	response_limit: number
	status: UusTaskStatus
	created_at: string
}

export interface UusResponse {
	id: number
	task_id: number
	task: UusTask | null
	user: UusUserSummary
	message: string | null
	offered_price: number | null
	status: UusResponseStatus
	created_at: string
}

export interface UusFilters {
	category?: string
	location?: string
	urgency?: string
	desired_when?: string
	date?: string
}

export interface UusTaskCreate {
	category: UusCategory
	description: string
	location: string
	desired_when: UusDesiredWhen
	date?: string | null
	budget?: number | null
	budget_type: UusBudgetType
	urgency: UusUrgency
}

export interface UusTaskUpdate extends Partial<UusTaskCreate> {
	status?: UusTaskStatus
}

export interface UusResponseCreate {
	message?: string | null
	offered_price?: number | null
}

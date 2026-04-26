export type AgalStatus = 'open' | 'matched' | 'completed' | 'cancelled'
export type AgalResponseStatus = 'pending' | 'accepted' | 'rejected'
export type AgalSizeLabel = 'document' | 'small' | 'medium' | 'large'
export type AgalFragility = 'normal' | 'fragile'

export interface AgalUserSummary {
	id: number
	name: string
	username: string | null
}

export interface AgalRoute {
	id: number
	carrier: AgalUserSummary
	from_address: string
	to_address: string
	date: string
	time: string | null
	size_label: AgalSizeLabel
	weight_kg_max: number | null
	accepted_items: string | null
	restricted_items: string | null
	price: number | null
	notes: string | null
	status: AgalStatus
	created_at: string
}

export interface AgalRequest {
	id: number
	sender: AgalUserSummary
	from_address: string
	to_address: string
	date: string
	time: string | null
	size_label: AgalSizeLabel
	weight_kg: number | null
	contents_summary: string
	fragility: AgalFragility
	documents_required: boolean
	budget: number | null
	notes: string | null
	status: AgalStatus
	created_at: string
}

export interface AgalResponse {
	id: number
	route_id?: number | null
	request_id?: number | null
	route?: AgalRoute | null
	request?: AgalRequest | null
	user: AgalUserSummary
	message: string | null
	status: AgalResponseStatus
	created_at: string
}

export interface AgalRouteCreate {
	from_address: string
	to_address: string
	date: string
	time?: string | null
	size_label: AgalSizeLabel
	weight_kg_max?: number | null
	accepted_items?: string | null
	restricted_items?: string | null
	price?: number | null
	notes?: string | null
}

export interface AgalRequestCreate {
	from_address: string
	to_address: string
	date: string
	time?: string | null
	size_label: AgalSizeLabel
	weight_kg?: number | null
	contents_summary: string
	fragility: AgalFragility
	documents_required: boolean
	budget?: number | null
	notes?: string | null
}

export interface AgalResponseCreate {
	message?: string | null
}

export interface AgalRouteUpdate {
	status?: AgalStatus
	time?: string | null
	weight_kg_max?: number | null
	accepted_items?: string | null
	restricted_items?: string | null
	price?: number | null
	notes?: string | null
}

export interface AgalRequestUpdate {
	status?: AgalStatus
	time?: string | null
	weight_kg?: number | null
	contents_summary?: string
	fragility?: AgalFragility
	documents_required?: boolean
	budget?: number | null
	notes?: string | null
}

export interface AgalFilters {
	from?: string
	to?: string
	date?: string
}

export type AyanStatus = 'open' | 'closed'

export interface AyanTripDriver {
	id: number
	name: string
	username: string | null
}

export interface AyanTrip {
	id: number
	driver: AyanTripDriver
	from_address: string
	to_address: string
	date: string
	time: string
	seats: number
	price: number
	comment: string | null
	status: AyanStatus
	created_at: string
}

export interface AyanRequestPassenger {
	id: number
	name: string
	username: string | null
}

export interface AyanRequest {
	id: number
	passenger: AyanRequestPassenger
	from_address: string
	to_address: string
	date: string
	time: string | null
	description: string | null
	status: AyanStatus
	created_at: string
}

export interface AyanResponseUser {
	id: number
	name: string
	username: string | null
}

export interface AyanResponse {
	id: number
	user: AyanResponseUser
	message: string | null
	created_at: string
}

export interface AyanTripCreate {
	from_address: string
	to_address: string
	date: string
	time: string
	seats: number
	price: number
	comment?: string
}

export interface AyanRequestCreate {
	from_address: string
	to_address: string
	date: string
	time?: string
	description?: string
}

export interface AyanResponseCreate {
	message?: string
}

export interface AyanTripUpdate {
	seats?: number
	price?: number
	comment?: string
	status?: AyanStatus
}

export interface AyanFilters {
	from?: string
	to?: string
	date?: string
}

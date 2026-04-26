import type { Role } from '~/types/api'

export function isAgalPrimaryRole(role?: Role | null): role is 'carrier' | 'sender' {
	return role === 'carrier' || role === 'sender'
}

export function getAgalCreateMode(role?: Role | null): 'route' | 'request' | null {
	if (role === 'carrier') return 'route'
	if (role === 'sender') return 'request'
	return null
}

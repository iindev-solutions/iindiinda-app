import type { Role } from '~/types/api'

export function isAyanPrimaryRole(role?: Role | null): role is 'driver' | 'passenger' {
	return role === 'driver' || role === 'passenger'
}

export function getAyanCreateMode(role?: Role | null): 'trip' | 'request' | null {
	if (role === 'driver') return 'trip'
	if (role === 'passenger') return 'request'
	return null
}

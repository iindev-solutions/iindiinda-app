<script setup lang="ts">
import type { Role } from '~/types/api'

import { isAyanPrimaryRole } from '../utils/role'

const emit = defineEmits<{ changed: [role: 'driver' | 'passenger'] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback } = useTg()
const { user, switchRole } = useAuth()

const switchingRole = ref<Role | null>(null)

const currentRole = computed(() => (isAyanPrimaryRole(user.value?.role) ? user.value.role : null))

const roleItems = computed(() => [
	{ label: t('ayan.passenger'), value: 'passenger' as const, icon: 'i-lucide-user-round' },
	{ label: t('ayan.driver'), value: 'driver' as const, icon: 'i-lucide-car' }
])

async function handleSwitch(role: 'driver' | 'passenger') {
	if (currentRole.value === role || switchingRole.value) return

	switchingRole.value = role
	try {
		await switchRole(role)
		hapticFeedback('notification')
		toast.add({
			title: t('ayan.roleSwitcher.successTitle'),
			description: t(`ayan.roleSwitcher.successDesc.${role}`),
			color: 'success',
			icon: 'i-lucide-check-circle',
			duration: 2500
		})
		emit('changed', role)
	} catch {
		hapticFeedback('impact')
		toast.add({
			title: t('common.error'),
			description: t('ayan.roleSwitcher.error'),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	} finally {
		switchingRole.value = null
	}
}
</script>

<template>
	<div class="app-panel app-panel--soft role-switch">
		<div class="role-switch__head">
			<div>
				<p class="app-kicker">{{ t('ayan.roleSwitcher.title') }}</p>
				<p class="role-switch__copy">{{ t('ayan.roleSwitcher.desc') }}</p>
			</div>
			<UBadge v-if="currentRole" color="primary" variant="subtle" size="xs">
				{{ t(`ayan.roleSwitcher.current.${currentRole}`) }}
			</UBadge>
		</div>

		<div class="grid grid-cols-2 gap-2">
			<UButton
				v-for="item in roleItems"
				:key="item.value"
				:block="true"
				:icon="item.icon"
				:color="currentRole === item.value ? 'primary' : 'neutral'"
				:variant="currentRole === item.value ? 'soft' : 'outline'"
				:loading="switchingRole === item.value"
				@click="handleSwitch(item.value)"
			>
				{{ item.label }}
			</UButton>
		</div>
	</div>
</template>

<style scoped>
.role-switch {
	padding: 16px;
}

.role-switch__head {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 12px;
	margin-bottom: 12px;
}

.role-switch__copy {
	margin: 6px 0 0;
	font-size: 12px;
	line-height: 1.55;
	color: var(--text-secondary);
}
</style>

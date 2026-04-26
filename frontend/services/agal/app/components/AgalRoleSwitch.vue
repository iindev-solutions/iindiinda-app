<script setup lang="ts">
import type { Role } from '~/types/api'

import { isAgalPrimaryRole } from '../utils/role'

const emit = defineEmits<{ changed: [role: 'carrier' | 'sender'] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback } = useTg()
const { user, switchRole } = useAuth()

const switchingRole = ref<Role | null>(null)

const currentRole = computed(() => (isAgalPrimaryRole(user.value?.role) ? user.value.role : null))

const roleItems = computed(() => [
	{ label: t('agal.carrier'), value: 'carrier' as const, icon: 'i-lucide-briefcase-business' },
	{ label: t('agal.sender'), value: 'sender' as const, icon: 'i-lucide-package-open' }
])

async function handleSwitch(role: 'carrier' | 'sender') {
	if (currentRole.value === role || switchingRole.value) return

	switchingRole.value = role
	try {
		await switchRole(role)
		hapticFeedback('notification')
		toast.add({
			title: t('agal.roleSwitcher.successTitle'),
			description: t(`agal.roleSwitcher.successDesc.${role}`),
			color: 'success',
			icon: 'i-lucide-check-circle',
			duration: 2500
		})
		emit('changed', role)
	} catch {
		hapticFeedback('impact')
		toast.add({
			title: t('common.error'),
			description: t('agal.roleSwitcher.error'),
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
				<p class="app-kicker">{{ t('agal.roleSwitcher.title') }}</p>
				<p class="role-switch__copy">{{ t('agal.roleSwitcher.desc') }}</p>
			</div>
			<UBadge v-if="currentRole" color="primary" variant="subtle" size="xs">
				{{ t(`agal.roleSwitcher.current.${currentRole}`) }}
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

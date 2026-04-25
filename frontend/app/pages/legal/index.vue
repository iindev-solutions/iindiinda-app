<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t } = useI18n()
const groups = computed(() => getLegalLinkGroups(t))
</script>

<template>
	<div class="min-h-screen px-4 py-6">
		<div class="mx-auto max-w-[720px] space-y-6">
			<BackButton force-ui />
			<header class="space-y-2">
				<p class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500">{{ t('legal.badge') }}</p>
				<h1 class="text-2xl font-semibold text-cyan-50">{{ t('legal.center.title') }}</h1>
				<p class="text-sm leading-relaxed text-gray-400">{{ t('legal.center.intro') }}</p>
			</header>

			<UCard v-for="group in groups" :key="group.title" variant="outline">
				<div class="space-y-4">
					<div class="space-y-2">
						<h2 class="text-lg font-medium text-cyan-50">{{ group.title }}</h2>
						<p class="text-sm leading-relaxed text-gray-300">{{ group.description }}</p>
					</div>
					<div class="flex flex-wrap gap-2">
						<UButton
							v-for="link in group.links"
							:key="link.key"
							:to="link.to"
							color="neutral"
							variant="outline"
							size="sm"
						>
							{{ link.label }}
						</UButton>
					</div>
				</div>
			</UCard>
		</div>
	</div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t } = useI18n()

const cards = computed(() => [
	{
		icon: 'i-carbon-road',
		title: t('servicePages.agal.cards.route.title'),
		description: t('servicePages.agal.cards.route.description')
	},
	{
		icon: 'i-carbon-connection',
		title: t('servicePages.agal.cards.match.title'),
		description: t('servicePages.agal.cards.match.description')
	},
	{
		icon: 'i-carbon-package',
		title: t('servicePages.agal.cards.handoff.title'),
		description: t('servicePages.agal.cards.handoff.description')
	}
])
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('servicePages.agal.badge') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('servicePages.agal.title') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('servicePages.agal.intro') }}
				</p>
			</header>

			<section class="mb-8 space-y-3">
				<div
					v-for="(card, index) in cards"
					:key="card.title"
					class="rounded-2xl border bg-[#2a3a42]/30 p-5"
					:class="index === 0 ? 'border-cyan-500/15' : 'border-white/10'"
				>
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<UIcon :name="card.icon" class="text-xl text-cyan-400" />
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">{{ card.title }}</h3>
							<p class="text-xs text-gray-400">{{ card.description }}</p>
						</div>
					</div>
				</div>
			</section>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" disabled>{{ t('servicePages.agal.cta') }}</UButton>
				<p class="text-center text-xs text-gray-500">{{ t('servicePages.agal.status') }}</p>
			</div>
		</div>
	</div>
</template>

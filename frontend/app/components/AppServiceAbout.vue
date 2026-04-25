<script setup lang="ts">
interface ServiceAboutExample {
	title: string
	description: string
}

const props = defineProps<{
	label: string
	description: string
	examplesTitle: string
	examples: ServiceAboutExample[]
}>()

const items = computed(() => [
	{
		label: props.label,
		icon: 'i-lucide-circle-help',
		slot: 'about'
	}
])
</script>

<template>
	<UCard variant="outline">
		<UAccordion :items="items" :ui="{ item: 'border-none', trigger: 'px-0 py-0', body: 'px-0 pt-0' }">
			<template #about-body>
				<div class="space-y-4 pb-1">
					<p class="text-sm leading-relaxed text-gray-300">
						{{ description }}
					</p>

					<div class="space-y-2">
						<p class="text-[10px] font-medium uppercase tracking-[0.18em] text-gray-500">
							{{ examplesTitle }}
						</p>
						<div class="space-y-2">
							<div
								v-for="example in examples"
								:key="example.title"
								class="rounded-xl border border-gray-800 bg-gray-900/60 p-3"
							>
								<p class="text-sm font-medium text-cyan-50">{{ example.title }}</p>
								<p class="mt-1 text-xs leading-relaxed text-gray-400">{{ example.description }}</p>
							</div>
						</div>
					</div>
				</div>
			</template>
		</UAccordion>
	</UCard>
</template>

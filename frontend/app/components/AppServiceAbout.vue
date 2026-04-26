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
	<div class="app-panel app-panel--soft app-service-about">
		<UAccordion
			:items="items"
			:ui="{
				item: 'border-none',
				trigger: 'px-0 py-0 text-left',
				leadingIcon: 'text-cyan-400',
				label: 'text-sm font-semibold text-cyan-50',
				body: 'px-0 pt-0'
			}"
		>
			<template #about-body>
				<div class="app-service-about__body">
					<p class="app-copy">{{ description }}</p>

					<div class="app-service-about__group">
						<p class="app-kicker">{{ examplesTitle }}</p>
						<div class="app-service-about__examples">
							<div
								v-for="example in examples"
								:key="example.title"
								class="app-service-about__example"
							>
								<p class="app-service-about__example-title">{{ example.title }}</p>
								<p class="app-service-about__example-copy">{{ example.description }}</p>
							</div>
						</div>
					</div>
				</div>
			</template>
		</UAccordion>
	</div>
</template>

<style scoped>
.app-service-about {
	padding: 16px;
}

.app-service-about__body {
	display: flex;
	flex-direction: column;
	gap: 16px;
	padding-top: 14px;
}

.app-service-about__group {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.app-service-about__examples {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.app-service-about__example {
	padding: 14px;
	border-radius: 18px;
	border: 1px solid rgb(154 166 178 / 0.1);
	background: rgb(255 255 255 / 0.03);
}

.app-service-about__example-title {
	font-size: 14px;
	font-weight: 600;
	line-height: 1.35;
	color: var(--text-primary);
	margin: 0;
}

.app-service-about__example-copy {
	font-size: 12px;
	line-height: 1.6;
	color: var(--text-secondary);
	margin: 6px 0 0;
}
</style>

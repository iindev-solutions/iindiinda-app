<script setup lang="ts">
defineProps<{
	eyebrow?: string
	title: string
	description?: string
	icon?: string
}>()
</script>

<template>
	<section class="app-hero">
		<div class="app-hero__frame">
			<div class="app-hero__top">
				<p v-if="eyebrow" class="app-kicker">{{ eyebrow }}</p>
				<slot name="top" />
			</div>

			<div class="app-hero__headline">
				<div v-if="icon" class="app-hero__icon">
					<UIcon :name="icon" class="size-6" />
				</div>
				<div class="min-w-0 flex-1">
					<h1 class="app-heading">{{ title }}</h1>
					<p v-if="description" class="app-copy app-copy--hero">{{ description }}</p>
				</div>
			</div>

			<div v-if="$slots.default" class="app-hero__body">
				<slot />
			</div>
		</div>
	</section>
</template>

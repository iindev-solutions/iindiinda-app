<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">ТАКСИ</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">Бардыбыт</h1>
				<p class="text-sm leading-relaxed text-gray-300">Ты ставишь цену. Водитель принимает.</p>
			</header>

			<section class="mb-8 space-y-3">
				<div
					class="rounded-2xl border border-cyan-500/15 bg-[#2a3a42]/30 p-5"
				>
					<div class="mb-3 flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><circle cx="12" cy="12" r="10"/><path d="m16 12-4-4-4 4"/><path d="M12 16V8"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Создай заказ</h3>
							<p class="text-xs text-gray-400">Откуда, куда и твоя цена</p>
						</div>
					</div>
				</div>

				<div
					class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5"
				>
					<div class="mb-3 flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Водитель примет</h3>
							<p class="text-xs text-gray-400">Видит твой заказ и соглашается</p>
						</div>
					</div>
				</div>

				<div
					class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5"
				>
					<div class="mb-3 flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Поехали</h3>
							<p class="text-xs text-gray-400">Без комиссий. Честная цена.</p>
						</div>
					</div>
				</div>
			</section>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" disabled>
					Заказать поездку
				</UButton>
				<p class="text-center text-xs text-gray-500">Скоро запуск в Якутске</p>
			</div>
		</div>
	</div>
</template>

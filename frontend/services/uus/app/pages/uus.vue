<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">МАСТЕРА</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">Уус</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					Найди мастера на любую задачу. Муж на час, ремонт, монтаж, установка.
				</p>
			</header>

			<section class="mb-8 space-y-3">
				<div class="rounded-2xl border border-cyan-500/15 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Опиши задачу</h3>
							<p class="text-xs text-gray-400">Что нужно сделать и когда</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Мастера откликнутся</h3>
							<p class="text-xs text-gray-400">Выбери по рейтингу и цене</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Задача решена</h3>
							<p class="text-xs text-gray-400">Оцени работу мастера</p>
						</div>
					</div>
				</div>
			</section>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" disabled>
					Найти мастера
				</UButton>
				<p class="text-center text-xs text-gray-500">В разработке</p>
			</div>
		</div>
	</div>
</template>

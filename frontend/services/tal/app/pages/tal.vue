<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">БРОНИРОВАНИЕ</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">Тал</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					Онлайн-запись на услуги. Салоны, клиники, мастерские.
				</p>
			</header>

			<section class="mb-8 space-y-3">
				<div class="rounded-2xl border border-cyan-500/15 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Выбери услугу</h3>
							<p class="text-xs text-gray-400">Мастер, время и дата</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Забронируй слот</h3>
							<p class="text-xs text-gray-400">Мгновенное подтверждение</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Приходи</h3>
							<p class="text-xs text-gray-400">Без очередей и звонков</p>
						</div>
					</div>
				</div>
			</section>

			<div class="space-y-3">
				<NuxtLink to="/tal-showcase">
					<UButton block size="lg" variant="outline">
						Посмотреть демо
					</UButton>
				</NuxtLink>
				<p class="text-center text-xs text-gray-500">В разработке</p>
			</div>
		</div>
	</div>
</template>

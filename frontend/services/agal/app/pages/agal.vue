<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">ДОСТАВКА</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">Аҕал</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					Авиа-доставка посылок. Отправь или привези из любой точки мира.
				</p>
			</header>

			<section class="mb-8 space-y-3">
				<div class="rounded-2xl border border-cyan-500/15 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Создай посылку</h3>
							<p class="text-xs text-gray-400">Откуда, куда, вес и размер</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Найди попутчика</h3>
							<p class="text-xs text-gray-400">Кто летит в нужном направлении</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">Посылка доставлена</h3>
							<p class="text-xs text-gray-400">Быстро и недорого через авиа</p>
						</div>
					</div>
				</div>
			</section>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" disabled>
					Отправить посылку
				</UButton>
				<p class="text-center text-xs text-gray-500">В разработке</p>
			</div>
		</div>
	</div>
</template>

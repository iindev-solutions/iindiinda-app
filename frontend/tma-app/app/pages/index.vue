<script setup lang="ts"></script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-12 pt-4">
				<h1 class="mb-2 text-[32px] font-medium leading-tight tracking-wide">
					<span class="text-primary">iind</span>
					<span class="ml-0.5 text-xl text-gray-500">app</span>
				</h1>
				<p class="max-w-[320px] text-sm leading-relaxed text-gray-400">
					Единый доступ к городской среде. Быстро. Надежно.
				</p>
			</header>

			<section class="mb-10">
				<div class="mb-4 flex items-center justify-between px-1">
					<h2 class="text-base font-medium uppercase tracking-[0.08em] text-[#d9e5e9]">Сервисы</h2>
					<UBadge variant="soft" color="gray" class="text-[10px] font-medium tracking-wider">
						BETA v1.0
					</UBadge>
				</div>

				<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
					<NuxtLink
						to="/taxi"
						class="group relative flex items-center gap-4 overflow-hidden rounded-2xl border border-cyan-500/15 bg-[#2a3a42]/40 p-5 transition-all hover:bg-[#2a3a42]/50 active:scale-[0.98]"
					>
						<div
							class="absolute inset-0 bg-linear-to-br from-cyan-500/3 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
						></div>
						<div
							class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-white/5 bg-[#0f1113]/60"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="28"
								height="28"
								viewBox="0 0 24 24"
								fill="none"
								stroke="currentColor"
								stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round"
								class="text-[#b3cbd3]"
							>
								<path
									d="M19 17h2l.64-2.54c.24-.959.24-1.962 0-2.92l-1.07-4.27A3 3 0 0 0 17.66 5H4a2 2 0 0 0-2 2v4m0 6h2"
								/>
								<circle cx="7" cy="17" r="2" />
								<path d="M9 17h6" />
								<circle cx="17" cy="17" r="2" />
							</svg>
						</div>
						<div class="min-w-0 flex-1">
							<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
								УСЛУГИ
							</div>
							<h3 class="mb-0.5 text-lg font-medium tracking-tight text-[#eff3f5]">Bardybyt</h3>
							<p class="text-[13px] leading-snug text-gray-300">Поручения и помощь</p>
						</div>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="20"
							height="20"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
							class="shrink-0 text-gray-500 transition-transform group-hover:translate-x-1"
						>
							<path d="m9 18 6-6-6-6" />
						</svg>
					</NuxtLink>

					<NuxtLink
						to="/otpravka"
						class="group relative flex items-center gap-4 overflow-hidden rounded-2xl border border-white/10 bg-[#2a3a42]/40 p-5 transition-all hover:bg-[#2a3a42]/50 active:scale-[0.98]"
					>
						<div
							class="absolute inset-0 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
						></div>
						<div
							class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-white/5 bg-[#0f1113]/60"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="28"
								height="28"
								viewBox="0 0 24 24"
								fill="none"
								stroke="currentColor"
								stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round"
								class="text-[#b3cbd3]"
							>
								<path
									d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"
								/>
							</svg>
						</div>
						<div class="min-w-0 flex-1">
							<div class="mb-1 text-[10px] font-medium uppercase text-gray-400">РЕМОНТ</div>
							<h3 class="mb-0.5 text-lg font-medium tracking-tight text-[#eff3f5]">Masters</h3>
							<p class="text-[13px] leading-snug text-gray-300">Мастера профи</p>
						</div>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="20"
							height="20"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
							class="shrink-0 text-gray-500 transition-transform group-hover:translate-x-1"
						>
							<path d="m9 18 6-6-6-6" />
						</svg>
					</NuxtLink>

					<div
						class="relative flex cursor-not-allowed items-center gap-4 overflow-hidden rounded-2xl border border-white/5 bg-[#2a3a42]/20 p-5 opacity-50"
					>
						<div
							class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-white/5 bg-[#0f1113]/60"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="28"
								height="28"
								viewBox="0 0 24 24"
								fill="none"
								stroke="currentColor"
								stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round"
								class="text-[#b3cbd3]"
							>
								<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
								<path d="M3 6h18" />
								<path d="M16 10a4 4 0 0 1-8 0" />
							</svg>
						</div>
						<div class="min-w-0 flex-1">
							<div class="mb-1 text-[10px] font-medium uppercase text-gray-400">MARKET</div>
							<h3 class="mb-0.5 text-lg font-medium tracking-tight text-[#eff3f5]">Market</h3>
							<p class="text-[13px] leading-snug text-gray-300">Локальные бренды</p>
						</div>
						<UBadge variant="soft" color="gray" size="xs">Скоро</UBadge>
					</div>

					<div
						class="relative flex cursor-not-allowed items-center gap-4 overflow-hidden rounded-2xl border border-white/5 bg-[#2a3a42]/20 p-5 opacity-50"
					>
						<div
							class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-white/5 bg-[#0f1113]/60"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="28"
								height="28"
								viewBox="0 0 24 24"
								fill="none"
								stroke="currentColor"
								stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round"
								class="text-[#b3cbd3]"
							>
								<path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z" />
								<path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9" />
								<path d="M12 3v6" />
							</svg>
						</div>
						<div class="min-w-0 flex-1">
							<div class="mb-1 text-[10px] font-medium uppercase tracking-[0.1em] text-gray-400">ЕДА</div>
							<h3 class="mb-0.5 text-lg font-medium tracking-tight text-[#eff3f5]">Kelbar</h3>
							<p class="text-[13px] leading-snug text-[#8da3ad]">Доставка 30 мин</p>
						</div>
						<UBadge variant="soft" color="gray" size="xs">Скоро</UBadge>
					</div>
				</div>
			</section>

			<footer class="mt-8 border-t border-white/[0.08] pt-6">
				<div
					class="flex cursor-pointer items-center gap-4 rounded-2xl border border-white/[0.08] bg-[#2a3a42]/20 p-5 transition-all hover:border-white/[0.12] hover:bg-[#2a3a42]/30 active:scale-[0.98]"
				>
					<div
						class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-white/[0.08] bg-[#0f1113]/40 text-[#8da3ad]"
					>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="24"
							height="24"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						>
							<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
							<circle cx="9" cy="7" r="4" />
							<path d="M22 21v-2a4 4 0 0 0-3-3.87" />
							<path d="M16 3.13a4 4 0 0 1 0 7.75" />
						</svg>
					</div>
					<div class="min-w-0 flex-1">
						<h3 class="mb-0.5 text-base font-medium tracking-tight text-[#d9e5e9]">Стать партнером</h3>
						<p class="text-[13px] leading-snug text-gray-400">Присоединяйтесь к экосистеме</p>
					</div>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="20"
						height="20"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="shrink-0 text-gray-500"
					>
						<path d="m9 18 6-6-6-6" />
					</svg>
				</div>
			</footer>
		</div>
	</div>
</template>

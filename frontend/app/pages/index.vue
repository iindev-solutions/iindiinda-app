<script setup lang="ts">
const services = [
	{
		name: 'Бардыбыт',
		category: 'ТАКСИ',
		description: 'Ты ставишь цену',
		to: '/ayan',
		icon: 'car',
		active: true,
		badge: 'MVP'
	},
	{
		name: 'Уус',
		category: 'МАСТЕРА',
		description: 'Ремонт и помощь',
		to: '/uus',
		icon: 'wrench',
		active: false
	},
	{
		name: 'Аҕал',
		category: 'ДОСТАВКА',
		description: 'Авиа-посылки',
		to: '/agal',
		icon: 'plane',
		active: false
	},
	{
		name: 'Тал',
		category: 'ЗАПИСЬ',
		description: 'Бронирование услуг',
		to: '/tal',
		icon: 'calendar',
		active: false
	}
]
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-12 pt-4">
				<h1 class="mb-2 text-[32px] font-medium leading-tight tracking-wide">
					<span class="text-primary">iind</span>
					<span class="ml-0.5 text-xl text-gray-500">app</span>
				</h1>
				<p class="max-w-[320px] text-sm leading-relaxed text-gray-400">
					Городские сервисы в одном месте. Просто.
				</p>
			</header>

			<section class="mb-10">
				<div class="mb-4 flex items-center justify-between px-1">
					<h2 class="text-base font-medium uppercase tracking-[0.08em] text-[#d9e5e9]">Сервисы</h2>
					<UBadge variant="soft" color="gray" class="text-[10px] font-medium tracking-wider">
						BETA v0.1
					</UBadge>
				</div>

				<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
					<NuxtLink
						v-for="service in services"
						:key="service.name"
						:to="service.to"
						class="group relative flex items-center gap-4 overflow-hidden rounded-2xl p-5 transition-all active:scale-[0.98]"
						:class="
							service.active
								? 'border border-cyan-500/15 bg-[#2a3a42]/40 hover:bg-[#2a3a42]/50'
								: 'border border-white/5 bg-[#2a3a42]/20 hover:bg-[#2a3a42]/30'
						"
					>
						<div
							v-if="service.active"
							class="absolute inset-0 bg-linear-to-br from-cyan-500/3 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
						></div>

						<div
							class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-white/5 bg-[#0f1113]/60"
						>
							<svg
								v-if="service.icon === 'car'"
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
							<svg
								v-else-if="service.icon === 'wrench'"
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
							<svg
								v-else-if="service.icon === 'plane'"
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
									d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"
								/>
							</svg>
							<svg
								v-else-if="service.icon === 'calendar'"
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
								<rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
								<line x1="16" x2="16" y1="2" y2="6" />
								<line x1="8" x2="8" y1="2" y2="6" />
								<line x1="3" x2="21" y1="10" y2="10" />
							</svg>
						</div>

						<div class="min-w-0 flex-1">
							<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
								{{ service.category }}
							</div>
							<h3 class="mb-0.5 text-lg font-medium tracking-tight text-[#eff3f5]">
								{{ service.name }}
							</h3>
							<p class="text-[13px] leading-snug text-gray-300">{{ service.description }}</p>
						</div>

						<UBadge v-if="service.badge" variant="soft" color="primary" size="xs">
							{{ service.badge }}
						</UBadge>
						<UBadge v-else-if="!service.active" variant="soft" color="gray" size="xs">Скоро</UBadge>

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

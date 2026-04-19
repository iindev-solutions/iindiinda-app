<script setup lang="ts">
import { computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from '#vue-router'

const { t } = useI18n()

const props = defineProps<{
	/**
	 * Резервный маршрут: куда идти, если в истории некуда возвращаться
	 * (например, пользователь открыл страницу напрямую)
	 */
	fallbackRoute?: string

	/**
	 * Хук перед навигацией.
	 * Может вернуть Promise<boolean> или просто boolean.
	 * Если false — навигация отменится (удобно для подтверждений)
	 */
	beforeNavigate?: () => boolean | Promise<boolean>

	/**
	 * Хук для переопределения логики перехода.
	 * Если передан - стандартная логика не сработает.
	 */
	onNavigate?: () => void | Promise<void>

	/**
	 * Текст кнопки (только для UI-режима, в TMA не используется)
	 * @default 'Назад'
	 */
	label?: string

	/**
	 * Показывать ли UI-кнопку даже в Telegram (для отладки)
	 * @default false
	 */
	forceUi?: boolean

	/**
	 * Дополнительные классы для UI-кнопки
	 */
	uiClass?: string
}>()

const router = useRouter()
const route = useRoute()
const tg = useTg()

const isInTelegram = computed(() => tg.isInTelegram.value)
const showUiButton = computed(() => !isInTelegram.value || props.forceUi)

/**
 * Проверяем, находимся ли мы «внутри» одной секции приложения.
 * Например, если текущий и предыдущий путь начинаются с /ayan/...
 * Это помогает избежать «вылета» из раздела при частых переходах.
 */
const isSameSection = computed<boolean>(() => {
	const backRoute = router.options.history.state?.back

	if (!backRoute || typeof backRoute !== 'string') return false

	const currentSection = route.path.split('/')[1]
	const backSection = backRoute.split('/')[1]

	return currentSection === backSection && currentSection !== ''
})

const handleBack = async () => {
	// 1. Если передан кастомный обработчик — делегируем ему
	if (props.onNavigate) {
		await props.onNavigate()
		return
	}

	// 2. Сначала даём шанс отменить переход
	if (props.beforeNavigate) {
		const shouldProceed = await props.beforeNavigate()
		if (!shouldProceed) return
	}

	// 3. Основная логика
	if (isSameSection.value) {
		// Если мы «внутри» раздела — просто идём назад по истории
		router.back()
	} else if (props.fallbackRoute) {
		// Если есть запасной маршрут — используем его
		await navigateTo(props.fallbackRoute)
	} else {
		// Фолбэк на главную
		await navigateTo('/')
	}
}

// Telegram BackButton lifecycle
onMounted(() => {
	if (isInTelegram.value && !props.forceUi) {
		tg.showBackButton()
		tg.onBackButtonClicked(handleBack)
	}
})

onUnmounted(() => {
	if (isInTelegram.value && !props.forceUi) {
		tg.hideBackButton()
	}
})
</script>

<template>
	<UButton
		v-if="showUiButton"
		:label="label || t('backButton.label')"
		:aria-label="t('backButton.ariaLabel')"
		variant="ghost"
		color="primary"
		:ui="{ base: 'gap-1' }"
		:class="uiClass"
		@click="handleBack"
	>
		<template #leading>
			<svg
				xmlns="http://www.w3.org/2000/svg"
				width="18"
				height="18"
				viewBox="0 0 24 24"
				fill="none"
				stroke="currentColor"
				stroke-width="2"
				stroke-linecap="round"
				stroke-linejoin="round"
			>
				<path d="m15 18-6-6 6-6" />
			</svg>
		</template>
	</UButton>
</template>

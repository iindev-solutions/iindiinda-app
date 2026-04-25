<script setup lang="ts">
const { t } = useI18n()
</script>

<template>
	<footer class="border-t border-gray-900 px-4 pb-3 pt-6">
		<div class="mx-auto max-w-[720px] rounded-2xl border border-gray-800 bg-gray-950/60 p-4">
			<div class="space-y-2">
				<p class="text-[10px] font-medium uppercase tracking-[0.18em] text-gray-500">
					{{ t('legal.badge') }}
				</p>
				<h2 class="text-sm font-medium text-cyan-50">{{ t('legal.footer.title') }}</h2>
				<p class="text-xs leading-relaxed text-gray-400">{{ t('legal.footer.description') }}</p>
			</div>
			<div class="mt-4 flex flex-wrap gap-2">
				<UButton to="/legal" color="neutral" variant="outline" size="xs" icon="i-lucide-file-text">
					{{ t('legal.links.center') }}
				</UButton>
				<UButton to="/legal/privacy" color="neutral" variant="ghost" size="xs">
					{{ t('legal.links.privacy') }}
				</UButton>
				<UButton to="/legal/support" color="neutral" variant="ghost" size="xs">
					{{ t('legal.links.support') }}
				</UButton>
			</div>
		</div>
	</footer>
</template>

<script setup lang="ts">
const isModalOpen = ref(false)
const isSlideoverOpen = ref(false)
const sliderValue = ref(50)
const switchValue = ref(true)
const inputValue = ref('')
const selectValue = ref('')

const toast = useToast()

function showToast(type: 'primary' | 'neutral') {
	toast.add({
		title: type === 'primary' ? 'Success!' : 'Info',
		description: `This is a ${type} notification`,
		color: type,
		icon: type === 'primary' ? 'i-lucide-check-circle' : 'i-lucide-info',
		duration: 3000
	})
}
</script>

<template>
	<div class="min-h-screen bg-default pb-20">
		<div class="relative overflow-hidden bg-linear-to-br from-primary/5 via-transparent to-primary/5">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
				<div class="text-center space-y-6">
					<div class="inline-flex items-baseline gap-2">
						<app-title />
					</div>

					<p class="text-xl sm:text-2xl text-muted max-w-3xl mx-auto">Comprehensive UI Component Library</p>

					<div class="flex flex-wrap items-center justify-center gap-3">
						<UBadge color="primary" variant="soft" size="lg">
							<UIcon name="i-lucide-layers" class="size-4" />
							125+ Components
						</UBadge>
						<UBadge color="neutral" variant="soft" size="lg">
							<UIcon name="i-lucide-smartphone" class="size-4" />
							Responsive
						</UBadge>
						<UBadge color="primary" variant="soft" size="lg">
							<UIcon name="i-lucide-moon" class="size-4" />
							Dark Mode
						</UBadge>
					</div>

					<div class="flex flex-wrap items-center justify-center gap-3 pt-4">
						<UButton size="lg" color="primary" icon="i-lucide-sparkles" @click="showToast('primary')">
							Try Components
						</UButton>
						<UButton size="lg" variant="outline" trailing-icon="i-lucide-arrow-right" to="/">
							Back to Home
						</UButton>
					</div>
				</div>
			</div>
		</div>

		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">
			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-mouse-pointer-click" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Buttons</h2>
				</div>

				<div class="grid lg:grid-cols-2 gap-6">
					<UCard>
						<template #header>
							<h3 class="font-semibold">Variants</h3>
						</template>
						<div class="space-y-4">
							<div class="flex flex-wrap gap-2">
								<UButton color="primary">Primary</UButton>
								<UButton variant="outline">Outline</UButton>
								<UButton variant="soft">Soft</UButton>
								<UButton variant="ghost">Ghost</UButton>
								<UButton variant="link">Link</UButton>
							</div>
							<USeparator />
							<div class="flex flex-wrap gap-2">
								<UButton color="primary">Primary</UButton>
								<UButton color="neutral">Neutral</UButton>
								<UButton color="cyan">Cyan</UButton>
								<UButton color="gray">Gray</UButton>
							</div>
						</div>
					</UCard>

					<UCard>
						<template #header>
							<h3 class="font-semibold">Sizes & Icons</h3>
						</template>
						<div class="space-y-4">
							<div class="flex flex-wrap items-center gap-2">
								<UButton size="xs">XS</UButton>
								<UButton size="sm">Small</UButton>
								<UButton size="md">Medium</UButton>
								<UButton size="lg">Large</UButton>
								<UButton size="xl">XL</UButton>
							</div>
							<USeparator />
							<div class="flex flex-wrap gap-2">
								<UButton color="primary" icon="i-lucide-plus">Add</UButton>
								<UButton variant="outline" icon="i-lucide-download">Download</UButton>
								<UButton variant="soft" trailing-icon="i-lucide-arrow-right">Next</UButton>
								<UButton variant="ghost" icon="i-lucide-trash" />
								<UButton variant="ghost" icon="i-lucide-settings" />
							</div>
						</div>
					</UCard>
				</div>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-square" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Cards</h2>
				</div>

				<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
					<UCard>
						<template #header>
							<div class="flex items-center justify-between">
								<h3 class="font-semibold">Feature Card</h3>
								<UBadge color="primary" variant="soft">New</UBadge>
							</div>
						</template>
						<p class="text-muted">
							Beautiful cards with customizable headers, body, and footer sections for any use case.
						</p>
						<template #footer>
							<div class="flex items-center justify-between">
								<span class="text-xs text-dimmed">Updated today</span>
								<UButton size="xs" variant="ghost" trailing-icon="i-lucide-arrow-right">View</UButton>
							</div>
						</template>
					</UCard>

					<UCard>
						<template #header>
							<div class="flex items-center gap-2">
								<UIcon name="i-lucide-zap" class="size-5 text-primary" />
								<h3 class="font-semibold">Performance</h3>
							</div>
						</template>
						<p class="text-muted mb-4">Optimized for speed and efficiency with minimal bundle size.</p>
						<UProgress :value="92" color="primary" />
						<p class="text-xs text-dimmed mt-2">92% faster than average</p>
					</UCard>

					<UCard>
						<template #header>
							<div class="flex items-center gap-2">
								<UIcon name="i-lucide-shield-check" class="size-5 text-primary" />
								<h3 class="font-semibold">Security</h3>
							</div>
						</template>
						<p class="text-muted mb-4">Built with security best practices and regular audits.</p>
						<div class="flex flex-wrap gap-2">
							<UBadge color="primary" variant="soft" size="xs">Verified</UBadge>
							<UBadge color="neutral" variant="soft" size="xs">Encrypted</UBadge>
							<UBadge color="cyan" variant="soft" size="xs">Audited</UBadge>
						</div>
					</UCard>
				</div>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-form-input" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Form Inputs</h2>
				</div>

				<UCard>
					<div class="grid md:grid-cols-2 gap-8">
						<div class="space-y-6">
							<div>
								<label class="text-sm font-medium text-default mb-2 block">Text Input</label>
								<UInput v-model="inputValue" placeholder="Enter your name..." />
							</div>

							<div>
								<label class="text-sm font-medium text-default mb-2 block">With Icon</label>
								<UInput placeholder="Search..." icon="i-lucide-search" />
							</div>

							<div>
								<label class="text-sm font-medium text-default mb-2 block">Email</label>
								<UInput placeholder="your@email.com" type="email" trailing-icon="i-lucide-mail" />
							</div>

							<div>
								<label class="text-sm font-medium text-default mb-2 block">Select</label>
								<USelect
									v-model="selectValue"
									:items="[
										{ label: 'Option 1', value: '1' },
										{ label: 'Option 2', value: '2' },
										{ label: 'Option 3', value: '3' }
									]"
									placeholder="Choose an option"
								/>
							</div>
						</div>

						<div class="space-y-6">
							<div>
								<label class="text-sm font-medium text-default mb-2 block">Textarea</label>
								<UTextarea placeholder="Enter your message..." :rows="5" />
							</div>

							<div>
								<label class="text-sm font-medium text-default mb-2 block">
									Slider ({{ sliderValue }})
								</label>
								<USlider v-model="sliderValue" :min="0" :max="100" />
							</div>

							<div class="space-y-3">
								<div class="flex items-center justify-between">
									<label class="text-sm font-medium text-default">Enable notifications</label>
									<USwitch v-model="switchValue" />
								</div>

								<UCheckbox label="I agree to the terms and conditions" />
								<UCheckbox label="Subscribe to newsletter" />
							</div>
						</div>
					</div>
				</UCard>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-bell" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Alerts & Notifications</h2>
				</div>

				<div class="grid gap-6">
					<div class="grid md:grid-cols-2 gap-4">
						<UAlert
							title="Success"
							description="Your changes have been saved successfully"
							color="primary"
							icon="i-lucide-check-circle"
						/>
						<UAlert
							title="Warning"
							description="Please review your information before proceeding"
							color="neutral"
							icon="i-lucide-alert-triangle"
						/>
					</div>

					<div class="grid md:grid-cols-2 gap-4">
						<UAlert
							title="Error"
							description="Something went wrong. Please try again"
							color="neutral"
							icon="i-lucide-x-circle"
						/>
						<UAlert
							title="Information"
							description="New features are now available"
							color="cyan"
							icon="i-lucide-info"
						/>
					</div>

					<UCard>
						<template #header>
							<h3 class="font-semibold">Toast Notifications</h3>
						</template>
						<div class="flex flex-wrap gap-2">
							<UButton color="primary" variant="soft" @click="showToast('primary')">
								Success Toast
							</UButton>
							<UButton color="neutral" variant="soft" @click="showToast('neutral')">Info Toast</UButton>
						</div>
					</UCard>
				</div>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-layout" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Overlays</h2>
				</div>

				<div class="grid md:grid-cols-2 gap-6">
					<UCard>
						<template #header>
							<h3 class="font-semibold">Modal</h3>
						</template>
						<p class="text-muted mb-4">Open a centered modal dialog with customizable content</p>
						<UButton icon="i-lucide-square-dashed-mouse-pointer" @click="isModalOpen = true">
							Open Modal
						</UButton>
					</UCard>

					<UCard>
						<template #header>
							<h3 class="font-semibold">Slideover</h3>
						</template>
						<p class="text-muted mb-4">Slide-in panel from the side of the screen</p>
						<UButton variant="outline" icon="i-lucide-panel-right" @click="isSlideoverOpen = true">
							Open Slideover
						</UButton>
					</UCard>
				</div>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-tag" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Badges & Chips</h2>
				</div>

				<UCard>
					<div class="space-y-6">
						<div>
							<p class="text-sm font-medium text-default mb-3">Badge Variants</p>
							<div class="flex flex-wrap gap-2">
								<UBadge>Default</UBadge>
								<UBadge color="primary">Primary</UBadge>
								<UBadge color="neutral">Neutral</UBadge>
								<UBadge color="cyan">Cyan</UBadge>
								<UBadge color="gray">Gray</UBadge>
							</div>
						</div>

						<USeparator />

						<div>
							<p class="text-sm font-medium text-default mb-3">Badge Styles</p>
							<div class="flex flex-wrap gap-2">
								<UBadge variant="solid" color="primary">Solid</UBadge>
								<UBadge variant="outline" color="primary">Outline</UBadge>
								<UBadge variant="soft" color="primary">Soft</UBadge>
							</div>
						</div>

						<USeparator />

						<div>
							<p class="text-sm font-medium text-default mb-3">With Icons</p>
							<div class="flex flex-wrap gap-2">
								<UBadge color="primary">
									<UIcon name="i-lucide-check" class="size-3" />
									Verified
								</UBadge>
								<UBadge color="neutral">
									<UIcon name="i-lucide-clock" class="size-3" />
									Pending
								</UBadge>
								<UBadge color="cyan">
									<UIcon name="i-lucide-x" class="size-3" />
									Rejected
								</UBadge>
							</div>
						</div>
					</div>
				</UCard>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-users" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Avatars</h2>
				</div>

				<UCard>
					<div class="space-y-6">
						<div>
							<p class="text-sm font-medium text-default mb-3">Sizes</p>
							<div class="flex flex-wrap items-center gap-3">
								<UAvatar src="https://i.pravatar.cc/150?img=1" size="xs" />
								<UAvatar src="https://i.pravatar.cc/150?img=2" size="sm" />
								<UAvatar src="https://i.pravatar.cc/150?img=3" size="md" />
								<UAvatar src="https://i.pravatar.cc/150?img=4" size="lg" />
								<UAvatar src="https://i.pravatar.cc/150?img=5" size="xl" />
							</div>
						</div>

						<USeparator />

						<div>
							<p class="text-sm font-medium text-default mb-3">Types</p>
							<div class="flex flex-wrap items-center gap-3">
								<UAvatar src="https://i.pravatar.cc/150?img=6" alt="User" />
								<UAvatar text="AB" />
								<UAvatar text="CD" />
								<UAvatar icon="i-lucide-user" />
								<UAvatar icon="i-lucide-building" />
							</div>
						</div>

						<USeparator />

						<div>
							<p class="text-sm font-medium text-default mb-3">Avatar Group</p>
							<UAvatarGroup :max="4">
								<UAvatar src="https://i.pravatar.cc/150?img=7" />
								<UAvatar src="https://i.pravatar.cc/150?img=8" />
								<UAvatar src="https://i.pravatar.cc/150?img=9" />
								<UAvatar src="https://i.pravatar.cc/150?img=10" />
								<UAvatar src="https://i.pravatar.cc/150?img=11" />
								<UAvatar src="https://i.pravatar.cc/150?img=12" />
							</UAvatarGroup>
						</div>
					</div>
				</UCard>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-bar-chart" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Progress & Loading</h2>
				</div>

				<UCard>
					<div class="space-y-6">
						<div>
							<p class="text-sm font-medium text-default mb-3">Progress Bars</p>
							<div class="space-y-3">
								<div>
									<div class="flex justify-between text-xs text-muted mb-1">
										<span>Primary</span>
										<span>75%</span>
									</div>
									<UProgress :value="75" color="primary" />
								</div>
								<div>
									<div class="flex justify-between text-xs text-muted mb-1">
										<span>Neutral</span>
										<span>90%</span>
									</div>
									<UProgress :value="90" color="neutral" />
								</div>
								<div>
									<div class="flex justify-between text-xs text-muted mb-1">
										<span>Cyan</span>
										<span>60%</span>
									</div>
									<UProgress :value="60" color="cyan" />
								</div>
							</div>
						</div>

						<USeparator />

						<div>
							<p class="text-sm font-medium text-default mb-3">Skeleton Loaders</p>
							<div class="space-y-3">
								<USkeleton class="h-4 w-full" />
								<USkeleton class="h-4 w-3/4" />
								<USkeleton class="h-4 w-1/2" />
								<USkeleton class="h-32 w-full" />
							</div>
						</div>
					</div>
				</UCard>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-folder-tree" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Navigation</h2>
				</div>

				<div class="grid lg:grid-cols-2 gap-6">
					<UCard>
						<template #header>
							<h3 class="font-semibold">Tabs</h3>
						</template>
						<UTabs
							:items="[
								{ label: 'Overview', icon: 'i-lucide-home', slot: 'overview' },
								{ label: 'Analytics', icon: 'i-lucide-bar-chart', slot: 'analytics' },
								{ label: 'Settings', icon: 'i-lucide-settings', slot: 'settings' }
							]"
						>
							<template #overview>
								<div class="py-4">
									<p class="text-muted">Overview content goes here</p>
								</div>
							</template>
							<template #analytics>
								<div class="py-4">
									<p class="text-muted">Analytics content goes here</p>
								</div>
							</template>
							<template #settings>
								<div class="py-4">
									<p class="text-muted">Settings content goes here</p>
								</div>
							</template>
						</UTabs>
					</UCard>

					<UCard>
						<template #header>
							<h3 class="font-semibold">Breadcrumb</h3>
						</template>
						<div class="space-y-4">
							<UBreadcrumb
								:links="[
									{ label: 'Home', to: '/', icon: 'i-lucide-home' },
									{ label: 'Components', to: '/components' },
									{ label: 'UI Library', to: '/ui' }
								]"
							/>
							<USeparator />
							<div>
								<p class="text-sm font-medium text-default mb-3">Pagination</p>
								<UPagination :total="100" :page-count="10" />
							</div>
						</div>
					</UCard>
				</div>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-list" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Accordion</h2>
				</div>

				<UCard>
					<UAccordion
						:items="[
							{
								label: 'What is iindapp?',
								icon: 'i-lucide-help-circle',
								content:
									'iindapp is a comprehensive UI component library built with Nuxt UI, providing 125+ accessible and customizable components for modern web applications.'
							},
							{
								label: 'How do I get started?',
								icon: 'i-lucide-rocket',
								content:
									'Simply install the package, import the components you need, and start building beautiful interfaces with minimal configuration.'
							},
							{
								label: 'Is it responsive?',
								icon: 'i-lucide-smartphone',
								content:
									'Yes! All components are fully responsive and work seamlessly across desktop, tablet, and mobile devices.'
							},
							{
								label: 'Does it support dark mode?',
								icon: 'i-lucide-moon',
								content:
									'Absolutely! Dark mode is built-in and can be toggled easily with our color mode utilities.'
							}
						]"
					/>
				</UCard>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-table" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Table</h2>
				</div>

				<UCard>
					<UTable
						:columns="[
							{ accessorKey: 'id', header: 'ID' },
							{ accessorKey: 'name', header: 'Name' },
							{ accessorKey: 'email', header: 'Email' },
							{ accessorKey: 'status', header: 'Status' }
						]"
						:data="[
							{ id: 1, name: 'John Doe', email: 'john@example.com', status: 'Active' },
							{ id: 2, name: 'Jane Smith', email: 'jane@example.com', status: 'Active' },
							{ id: 3, name: 'Bob Johnson', email: 'bob@example.com', status: 'Inactive' },
							{ id: 4, name: 'Alice Brown', email: 'alice@example.com', status: 'Active' }
						]"
					/>
				</UCard>
			</section>

			<section>
				<div class="flex items-center gap-3 mb-8">
					<UIcon name="i-lucide-keyboard" class="size-6 text-primary" />
					<h2 class="text-3xl font-bold text-highlighted">Keyboard Shortcuts</h2>
				</div>

				<UCard>
					<div class="space-y-4">
						<div class="flex items-center justify-between">
							<span class="text-sm text-default">Search</span>
							<div class="flex gap-1">
								<UKbd>⌘</UKbd>
								<UKbd>K</UKbd>
							</div>
						</div>
						<USeparator />
						<div class="flex items-center justify-between">
							<span class="text-sm text-default">Save</span>
							<div class="flex gap-1">
								<UKbd>Ctrl</UKbd>
								<UKbd>S</UKbd>
							</div>
						</div>
						<USeparator />
						<div class="flex items-center justify-between">
							<span class="text-sm text-default">Copy</span>
							<div class="flex gap-1">
								<UKbd>Ctrl</UKbd>
								<UKbd>C</UKbd>
							</div>
						</div>
						<USeparator />
						<div class="flex items-center justify-between">
							<span class="text-sm text-default">Paste</span>
							<div class="flex gap-1">
								<UKbd>Ctrl</UKbd>
								<UKbd>V</UKbd>
							</div>
						</div>
					</div>
				</UCard>
			</section>
		</div>

		<UModal v-model:open="isModalOpen" title="Example Modal" description="This is a beautiful modal dialog">
			<template #body>
				<div class="space-y-4">
					<p class="text-muted">
						Modals are perfect for displaying important information or collecting user input without leaving
						the current page.
					</p>
					<UInput placeholder="Enter something..." />
					<UTextarea placeholder="Add a description..." :rows="3" />
				</div>
			</template>
			<template #footer>
				<div class="flex justify-end gap-2">
					<UButton variant="ghost" @click="isModalOpen = false">Cancel</UButton>
					<UButton color="primary" @click="isModalOpen = false">Confirm</UButton>
				</div>
			</template>
		</UModal>

		<USlideover v-model:open="isSlideoverOpen" title="Example Slideover" side="right">
			<template #body>
				<div class="space-y-6">
					<div>
						<h4 class="font-semibold mb-2">About Slideovers</h4>
						<p class="text-sm text-muted">
							Slideovers are great for displaying additional content or forms without disrupting the main
							page flow.
						</p>
					</div>

					<USeparator />

					<div class="space-y-3">
						<UInput placeholder="Name" />
						<UInput placeholder="Email" type="email" />
						<UTextarea placeholder="Message" :rows="4" />
					</div>

					<USeparator />

					<div class="space-y-2">
						<UCheckbox label="Subscribe to newsletter" />
						<UCheckbox label="Accept terms and conditions" />
					</div>
				</div>
			</template>
			<template #footer>
				<div class="flex justify-end gap-2">
					<UButton variant="ghost" @click="isSlideoverOpen = false">Close</UButton>
					<UButton color="primary">Submit</UButton>
				</div>
			</template>
		</USlideover>
	</div>
</template>

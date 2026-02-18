<script setup lang="ts"></script>

<template>
	<div class="min-h-screen bg-gray-950 pb-20">
		<div class="max-w-4xl mx-auto px-4 py-8 space-y-12">
			<header class="text-center border-b border-cyan-800 pb-8">
				<h1 class="text-4xl font-medium tracking-wide text-cyan-300 mb-2">
					<span>iind</span>
					<span class="text-2xl text-gray-400">app</span>
				</h1>
				<p class="text-sm text-gray-400 uppercase tracking-wider">UI Components</p>
			</header>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Buttons</h2>
				<div class="flex flex-wrap gap-3">
					<UButton>Default</UButton>
					<UButton color="primary">Primary</UButton>
					<UButton variant="outline">Outline</UButton>
					<UButton variant="soft">Soft</UButton>
					<UButton variant="ghost">Ghost</UButton>
					<UButton disabled>Disabled</UButton>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Button Sizes</h2>
				<div class="flex flex-wrap items-center gap-3">
					<UButton size="xs">Extra Small</UButton>
					<UButton size="sm">Small</UButton>
					<UButton size="md">Medium</UButton>
					<UButton size="lg">Large</UButton>
					<UButton size="xl">Extra Large</UButton>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Cards</h2>
				<div class="grid md:grid-cols-2 gap-4">
					<UCard>
						<template #header>
							<h3 class="text-lg font-medium">Card Header</h3>
						</template>
						<p class="text-gray-400">Card content goes here with some description text</p>
						<template #footer>
							<span class="text-sm text-gray-500">Footer text</span>
						</template>
					</UCard>

					<UCard>
						<template #header>
							<h3 class="text-lg font-medium">Simple Card</h3>
						</template>
						<p class="text-gray-400">Minimal card without footer</p>
					</UCard>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Form Inputs</h2>
				<div class="space-y-3 flex flex-col gap-1">
					<UInput placeholder="Default input" />
					<UInput placeholder="With icon" icon="i-heroicons-magnifying-glass" />
					<UInput placeholder="Disabled input" disabled />
					<UTextarea placeholder="Enter text here..." :rows="4" />
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Select & Menu</h2>
				<div class="space-y-3">
					<USelect
						:items="[
							{ label: 'Option 1', value: '1' },
							{ label: 'Option 2', value: '2' },
							{ label: 'Option 3', value: '3' }
						]"
						placeholder="Select option"
					/>
					<USelectMenu
						:items="[
							{ label: 'Apple', value: 'apple' },
							{ label: 'Banana', value: 'banana' },
							{ label: 'Orange', value: 'orange' }
						]"
						placeholder="Select with search"
					/>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Checkbox & Radio</h2>
				<div class="space-y-3">
					<UCheckbox label="Checkbox option" />
					<UCheckbox label="Checked option" :model-value="true" />
					<URadioGroup
						:options="[
							{ label: 'Radio option 1', value: '1' },
							{ label: 'Radio option 2', value: '2' },
							{ label: 'Radio option 3', value: '3' }
						]"
					/>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Switch & Toggle</h2>
				<div class="flex flex-wrap gap-4">
					<USwitch />
					<USwitch :model-value="true" />
					<USwitch disabled />
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Badges</h2>
				<div class="flex flex-wrap gap-3">
					<UBadge>Default</UBadge>
					<UBadge color="primary">Primary</UBadge>
					<UBadge color="cyan">Cyan</UBadge>
					<UBadge color="gray">Gray</UBadge>
					<UBadge color="neutral">Neutral</UBadge>
					<UBadge variant="outline">Outline</UBadge>
					<UBadge variant="soft">Soft</UBadge>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Alerts</h2>
				<div class="space-y-3">
					<UAlert title="Info Alert" description="This is an informational message" />
					<UAlert title="Primary Alert" description="Primary color alert" color="primary" />
					<UAlert title="Cyan Alert" description="Cyan color alert" color="cyan" />
					<UAlert title="Gray Alert" description="Gray color alert" color="gray" />
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Avatars</h2>
				<div class="flex flex-wrap items-center gap-4">
					<UAvatar src="https://i.pravatar.cc/150?img=1" alt="User" />
					<UAvatar src="https://i.pravatar.cc/150?img=2" alt="User" size="lg" />
					<UAvatar text="AB" />
					<UAvatar icon="i-heroicons-user" />
					<UAvatarGroup :max="3">
						<UAvatar src="https://i.pravatar.cc/150?img=3" alt="User 1" />
						<UAvatar src="https://i.pravatar.cc/150?img=4" alt="User 2" />
						<UAvatar src="https://i.pravatar.cc/150?img=5" alt="User 3" />
						<UAvatar src="https://i.pravatar.cc/150?img=6" alt="User 4" />
					</UAvatarGroup>
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Chips</h2>
				<div class="flex flex-wrap gap-3">
					<UChip text="Default" />
					<UChip text="Primary" color="primary" />
					<UChip text="5" />
					<UChip text="99+" color="cyan" />
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Progress</h2>
				<div class="space-y-3">
					<UProgress :value="25" />
					<UProgress :value="50" color="primary" />
					<UProgress :value="75" color="cyan" />
					<UProgress :value="100" color="gray" />
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Skeleton</h2>
				<div class="space-y-3">
					<USkeleton class="h-5 w-full" />
					<USkeleton class="h-5 w-3/4" />
					<USkeleton class="h-24 w-full" />
				</div>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Accordion</h2>
				<UAccordion
					:items="[
						{
							label: 'First Item',
							content: 'This is the content of the first accordion item'
						},
						{
							label: 'Second Item',
							content: 'This is the content of the second accordion item'
						},
						{
							label: 'Third Item',
							content: 'This is the content of the third accordion item'
						}
					]"
				/>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Tabs</h2>
				<UTabs
					:items="[
						{ label: 'Tab 1', content: 'Content for tab 1' },
						{ label: 'Tab 2', content: 'Content for tab 2' },
						{ label: 'Tab 3', content: 'Content for tab 3' }
					]"
				/>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Breadcrumb</h2>
				<UBreadcrumb
					:links="[
						{ label: 'Home', to: '/' },
						{ label: 'Components', to: '/components' },
						{ label: 'UI', to: '/ui' }
					]"
				/>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Pagination</h2>
				<UPagination :total="100" :page-count="10" />
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Table</h2>
				<UTable
					:columns="[
						{ accessorKey: 'id', header: 'ID' },
						{ accessorKey: 'name', header: 'Name' },
						{ accessorKey: 'status', header: 'Status' }
					]"
					:data="[
						{ id: 1, name: 'Item 1', status: 'Active' },
						{ id: 2, name: 'Item 2', status: 'Inactive' },
						{ id: 3, name: 'Item 3', status: 'Active' }
					]"
				/>
			</section>

			<section class="space-y-4">
				<h2 class="text-xl font-medium text-cyan-50">Kbd</h2>
				<div class="flex flex-wrap gap-2">
					<UKbd>⌘</UKbd>
					<UKbd>K</UKbd>
					<UKbd>Ctrl</UKbd>
					<UKbd>Shift</UKbd>
					<UKbd>Enter</UKbd>
				</div>
			</section>
		</div>
	</div>
</template>

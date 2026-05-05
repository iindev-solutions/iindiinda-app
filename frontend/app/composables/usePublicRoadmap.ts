export interface PublicRoadmapItem {
	title: string
	description: string
}

export interface PublicRoadmapService {
	id: 'ayan' | 'uus' | 'tal' | 'agal'
	name: string
	badge: string
	summary: string
	route: string
	icon: string
	live: PublicRoadmapItem[]
	building: PublicRoadmapItem[]
	planned: PublicRoadmapItem[]
}

export function usePublicRoadmap() {
	const { t } = useI18n()

	const services = computed<PublicRoadmapService[]>(() => [
		{
			id: 'ayan',
			name: t('services.ayan.name'),
			badge: t('ayan.category'),
			summary: t('roadmap.services.ayan.summary'),
			route: '/ayan',
			icon: 'i-carbon-car',
			live: [
				{
					title: t('roadmap.services.ayan.live.one.title'),
					description: t('roadmap.services.ayan.live.one.description')
				},
				{
					title: t('roadmap.services.ayan.live.two.title'),
					description: t('roadmap.services.ayan.live.two.description')
				}
			],
			building: [
				{
					title: t('roadmap.services.ayan.building.one.title'),
					description: t('roadmap.services.ayan.building.one.description')
				},
				{
					title: t('roadmap.services.ayan.building.two.title'),
					description: t('roadmap.services.ayan.building.two.description')
				}
			],
			planned: [
				{
					title: t('roadmap.services.ayan.planned.one.title'),
					description: t('roadmap.services.ayan.planned.one.description')
				},
				{
					title: t('roadmap.services.ayan.planned.two.title'),
					description: t('roadmap.services.ayan.planned.two.description')
				}
			]
		},
		{
			id: 'uus',
			name: t('services.uus.name'),
			badge: t('servicePages.uus.badge'),
			summary: t('roadmap.services.uus.summary'),
			route: '/uus',
			icon: 'i-carbon-tool-kit',
			live: [
				{
					title: t('roadmap.services.uus.live.one.title'),
					description: t('roadmap.services.uus.live.one.description')
				},
				{
					title: t('roadmap.services.uus.live.two.title'),
					description: t('roadmap.services.uus.live.two.description')
				}
			],
			building: [
				{
					title: t('roadmap.services.uus.building.one.title'),
					description: t('roadmap.services.uus.building.one.description')
				},
				{
					title: t('roadmap.services.uus.building.two.title'),
					description: t('roadmap.services.uus.building.two.description')
				}
			],
			planned: [
				{
					title: t('roadmap.services.uus.planned.one.title'),
					description: t('roadmap.services.uus.planned.one.description')
				},
				{
					title: t('roadmap.services.uus.planned.two.title'),
					description: t('roadmap.services.uus.planned.two.description')
				}
			]
		},
		{
			id: 'tal',
			name: t('services.tal.name'),
			badge: t('servicePages.tal.badge'),
			summary: t('roadmap.services.tal.summary'),
			route: '/tal',
			icon: 'i-carbon-calendar',
			live: [
				{
					title: t('roadmap.services.tal.live.one.title'),
					description: t('roadmap.services.tal.live.one.description')
				},
				{
					title: t('roadmap.services.tal.live.two.title'),
					description: t('roadmap.services.tal.live.two.description')
				}
			],
			building: [
				{
					title: t('roadmap.services.tal.building.one.title'),
					description: t('roadmap.services.tal.building.one.description')
				},
				{
					title: t('roadmap.services.tal.building.two.title'),
					description: t('roadmap.services.tal.building.two.description')
				}
			],
			planned: [
				{
					title: t('roadmap.services.tal.planned.one.title'),
					description: t('roadmap.services.tal.planned.one.description')
				},
				{
					title: t('roadmap.services.tal.planned.two.title'),
					description: t('roadmap.services.tal.planned.two.description')
				}
			]
		},
		{
			id: 'agal',
			name: t('services.agal.name'),
			badge: t('servicePages.agal.badge'),
			summary: t('roadmap.services.agal.summary'),
			route: '/agal',
			icon: 'i-carbon-box',
			live: [
				{
					title: t('roadmap.services.agal.live.one.title'),
					description: t('roadmap.services.agal.live.one.description')
				},
				{
					title: t('roadmap.services.agal.live.two.title'),
					description: t('roadmap.services.agal.live.two.description')
				}
			],
			building: [
				{
					title: t('roadmap.services.agal.building.one.title'),
					description: t('roadmap.services.agal.building.one.description')
				},
				{
					title: t('roadmap.services.agal.building.two.title'),
					description: t('roadmap.services.agal.building.two.description')
				}
			],
			planned: [
				{
					title: t('roadmap.services.agal.planned.one.title'),
					description: t('roadmap.services.agal.planned.one.description')
				},
				{
					title: t('roadmap.services.agal.planned.two.title'),
					description: t('roadmap.services.agal.planned.two.description')
				}
			]
		}
	])

	return {
		services
	}
}

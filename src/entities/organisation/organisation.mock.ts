import { Organisation } from './organisation'
import { TOrganisation } from './organisation.types'

export const mockOrganisationData = (): TOrganisation[] => [
	{ // full data
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this organisation',
		oin: '00000001836472635000',
		tooi: '7843432',
		rsin: '827342654',
		pki: '543573424',
	},
	{
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this organisation',
		oin: '',
		tooi: '',
		rsin: '',
		pki: '',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this organisation',
		oin: '5435',
		tooi: '5435',
		rsin: '54',
		pki: '6565',
	},
]

export const mockOrganisation = (data: TOrganisation[] = mockOrganisationData()): TOrganisation[] => data.map(item => new Organisation(item))

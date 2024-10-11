import { organization } from './organization'
import { Torganization } from './organization.types'

export const mockorganizationData = (): Torganization[] => [
	{ // full data
		id: '1',
		title: 'Decat',
		summary: 'a short form summary',
		description: 'a really really long description about this organization',
		oin: '00000001836472635000',
		tooi: '7843432',
		rsin: '827342654',
		pki: '543573424',
	},
	{
		id: '2',
		title: 'Woo',
		summary: 'a short form summary',
		description: 'a really really long description about this organization',
		oin: '',
		tooi: '',
		rsin: '',
		pki: '',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'a short form summary',
		description: 'a really really long description about this organization',
		oin: '5435',
		tooi: '5435',
		rsin: '54',
		pki: '6565',
	},
]

export const mockorganization = (data: Torganization[] = mockorganizationData()): Torganization[] => data.map(item => new organization(item))

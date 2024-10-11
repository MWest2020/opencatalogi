import { Publication } from './publication'
import { TPublication } from './publication.types'

export const mockPublicationsData = (): TPublication[] => [
	{ // full data
		id: '1',
		reference: 'ref1',
		title: 'test 1',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		image: 'https://example.com/image.jpg',
		category: 'category1',
		portal: 'https://google.com',
		catalogi: '2',
		metaData: '4',
		published: '2024-09-04T12:36:39Z',
		modified: '2024-09-04T12:36:39Z',
		featured: true,
		data: {
			key: 'anyvalue',
			streetNumber: 1,
			object: {
				blabla: 'bla',
			},
			array: ['appel', 'peer', 0, [], {}],
		},
		attachments: [],
		attachmentCount: 1,
		schema: 'https://schema.org',
		status: 'Concept',
		license: 'MIT',
		themes: ['theme1'],
		anonymization: {
			anonymized: true,
			results: '',
		},
		language: {
			code: 'en-us',
			level: 'A1',
		},
		archive: {
			date: new Date(2023, 2, 24).toISOString(),
		},
		geo: {
			type: 'Point',
			coordinates: [2, 23],
		},
	},
	{ // partial data
		id: '2',
		reference: 'ref2',
		title: 'test 2',
		summary: 'a short form summary',
		description: '',
		image: '',
		category: 'category2',
		portal: '',
		catalogi: '2',
		metaData: '4',
		published: '2024-09-04T12:36:39Z',
		modified: '2024-09-04T12:36:39Z',
		featured: true,
		data: {
			type: '',
		},
		attachments: [],
		attachmentCount: 1,
		schema: 'https://schema.org',
		status: 'Concept',
		license: 'MIT',
		themes: ['theme1'],
		anonymization: {
			anonymized: true,
			results: '',
		},
		language: {
			code: 'en-us',
			level: 'A1',
		},
		archive: {
			date: new Date(2023, 2, 24).toISOString(),
		},
		geo: {
			type: 'Point',
			coordinates: [2, 23],
		},
	},
	{ // invalid data
		id: '3',
		title: 'test 3',
		summary: 'a short form summary',
		description: 'a really really long description about this catalogus',
		reference: 'ref3',
		image: '',
		category: 'category3',
		portal: '',
		catalogi: 3,
		metaData: '3',
		published: '2024-09-04T12:36:39Z',
		modified: '2024-09-04T12:36:39Z',
		featured: true,
		data: {
			type: '',
		},
		attachments: [],
		attachmentCount: 1,
		schema: 'https://schema.org',
		// @ts-expect-error -- invalid data for testing
		status: true,
		license: 'MIT',
		themes: ['theme1'],
		anonymization: {
			anonymized: true,
			results: '',
		},
		language: {
			code: 'en-us',
			level: 'A1',
		},
		archive: {
			date: new Date(2023, 2, 24).toISOString(),
		},
		geo: {
			type: 'Point',
			coordinates: [2, 23],
		},
	},
]

export const mockPublications = (data: TPublication[] = mockPublicationsData()): TPublication[] => data.map(item => new Publication(item))

/* eslint-disable no-console */
import pinia from '../../pinia'
import { Attachment, Publication, TAttachment, TPublication } from '../../entities/index.js'
import { defineStore } from 'pinia'
import axios from 'axios'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/publication'

interface Options {
    /**
     * Do not save the received item to the store, this can be enabled if API calls get run in a loop
     */
    doNotSetStore?: boolean
}

interface PublicationStoreState {
    publicationItem: Publication;
    publicationPublicationType: string;
    publicationList: Publication[];
    publicationDataKey: string;
    attachmentItem: Attachment;
    attachmentFile: object;
    publicationAttachments: Attachment[];
    conceptPublications: Publication[];
    conceptAttachments: Attachment[];
}

export const usePublicationStore = defineStore('publication', {
	state: () => ({
		publicationItem: {
			'@self': {
				id: 8,
				uuid: '380822c1-0e49-4022-bebd-55851e1e5693',
				uri: 'http://nextcloud.local/index.php/apps/openregister/api/objects/380822c1-0e49-4022-bebd-55851e1e5693',
				version: '0.0.1',
				register: '1',
				schema: '1',
				files: [],
				relations: {
					'data.tooiCategorieUri': 'https://identifier.overheid.nl/tooi/def/thes/kern/c_3baef532',
					catalog: '054640bb-a694-4a33-ad87-d8d7f05edabd',
					publicationType: 'd42840c5-e935-4079-95fa-81a7274a41a6',
				},
				locked: null,
				owner: '',
				updated: '2025-02-18T13:51:58+00:00',
				created: '2025-02-18T13:51:58+00:00',
				folder: 'Open Registers/OpenCatalogi Register/Publicatie/380822c1-0e49-4022-bebd-55851e1e5693',
			},
			objectType: 'publication',
			title: 'afew',
			summary: 'awe',
			description: '',
			reference: '',
			image: '',
			category: '',
			portal: '',
			featured: false,
			schema: '',
			organization: '',
			status: 'Concept',
			attachments: [],
			attachmentCount: 0,
			themes: [],
			data: {
				tooiCategorieNaam: 'Woo-verzoeken en -besluiten',
				tooiCategorieId: 'c_3baef532',
				tooiCategorieUri: 'https://identifier.overheid.nl/tooi/def/thes/kern/c_3baef532',
			},
			anonymization: {
				anonymized: false,
				results: '',
			},
			language: {
				code: '',
				level: '',
			},
			published: '2025-02-18T13:51:54.718Z',
			modified: '',
			license: '',
			archive: {
				date: '',
			},
			geo: {
				type: 'Point',
				coordinates: [
					0,
					0,
				],
			},
			catalog: '054640bb-a694-4a33-ad87-d8d7f05edabd',
			publicationType: 'd42840c5-e935-4079-95fa-81a7274a41a6',
			validation: {
				errors: [
					'The required properties (datumBesluit, datumOntvangst) are missing',
				],
				valid: false,
			},
			id: '380822c1-0e49-4022-bebd-55851e1e5693',
		},
		publicationPublicationType: null,
		publicationList: [],
		publicationDataKey: null,
		attachmentItem: null,
		attachmentFile: null,
		publicationAttachments: null,
		conceptPublications: [],
		conceptAttachments: [],
	} as unknown as PublicationStoreState),
	actions: {
		setPublicationItem(publicationItem: Publication | TPublication) {
			// To prevent forms etc from braking we alway use a default/skeleton object
			this.publicationItem = publicationItem && new Publication(publicationItem)
			console.log('Active publication item set to ' + publicationItem && publicationItem.id)
		},
		setPublicationList(publicationList: Publication[] | TPublication[]) {
			this.publicationList = publicationList.map((publicationItem) => new Publication(publicationItem))
			console.log('Lenght of publication list set to ' + publicationList.length)
		},
		async refreshPublicationList(
			// eslint-disable-next-line @typescript-eslint/no-explicit-any
			normalSearch: { [key: string]: any }[] = [],
			advancedSearch: string = null,
			sortField: string = null,
			sortDirection: string = null,
		) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
			const params = new URLSearchParams()
			for (const item of normalSearch) {
				if (item.key && item.value !== undefined) {
					params.append(item.key, item.value)
				}
			}
			if (advancedSearch !== null && advancedSearch !== '') {
				params.append('_search', advancedSearch)
			}
			if (sortField !== null && sortField !== '' && sortDirection !== null && sortDirection !== '') {
				if (sortField === 'Titel') {
					sortField = 'title'
				}
				if (sortField === 'Datum gepubliceerd') {
					sortField = 'published'
				}
				if (sortField === 'Datum aangepast') {
					sortField = 'modified'
				}
				params.append('_order[' + sortField + ']', sortDirection)
			}
			if (params.toString()) {
				endpoint += '?' + params.toString()
			}

			return fetch(endpoint,
				{ method: 'GET' },
			)
				.then((response) => {
					return response.json().then((data) => {
						this.setPublicationList(data?.results)
						return data
					})

				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		/* istanbul ignore next */
		async getOnePublication(id: number, options: Options = {}) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Publication(await response.json())

			options.doNotSetStore !== true && this.setPublicationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addPublication(item: Publication) {
			if (!(item instanceof Publication)) {
				throw Error('Please pass a Publication item from the Publication class')
			}

			const validateResult = item.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(apiEndpoint,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Publication(await response.json())

			this.refreshPublicationList()
			this.setPublicationItem(data)

			// dynamic import the navigationStore to avoid circular imports
			const { useNavigationStore } = await import('../modules/navigation')
			const navigationStore = useNavigationStore(pinia)
			navigationStore.setSelectedCatalogus(data?.catalog ?? data?.catalog)

			return { response, data }
		},
		/* istanbul ignore next */
		async editPublication(item: Publication) {
			if (!(item instanceof Publication)) {
				throw Error('Please pass a Publication item from the Publication class')
			}

			const validateResult = item.validate()

			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${item.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Publication(await response.json())

			this.refreshPublicationList()
			this.setPublicationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deletePublication(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshPublicationList()
			this.setPublicationItem(false)

			return { response }
		},
		/* istanbul ignore next */
		async downloadPublication(id: number, title: string, type: 'pdf' | 'zip') {
			if (!id) {
				throw Error('Passed id is falsy')
			}
			if (!type) {
				throw Error('Passed type is pdf or zip')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}/download`,
				{
					method: 'GET',
					headers: {
						Accept: `application/${type}`,
					},
				},
			)

			const blob = await response.blob()

			const download = () => {
				const url = window.URL.createObjectURL(new Blob([blob]))
				const link = document.createElement('a')
				link.href = url

				link.setAttribute('download', `${title}.${type.toLowerCase()}`)
				document.body.appendChild(link)
				link.click()
			}

			return { response, blob, download }
		},
		// ################################
		// ||                            ||
		// ||        ATTACHMENTS         ||
		// ||                            ||
		// ################################
		/* istanbul ignore next */
		async getPublicationAttachments(id: number) {
			if (!id) {
				throw Error('Passed publication id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}/files`,
				{ method: 'GET' },
			)

			const rawData = await response.json()

			// const data = rawData.results.map(
			//	(attachmentItem: TAttachment) => new Attachment(attachmentItem),
			// )

			this.publicationAttachments = rawData

			return { response, rawData }
		},
		getConceptPublications() { // @todo this might belong in a service?
			fetch('/index.php/apps/opencatalogi/api/publications?status=Concept',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.conceptPublications = data.results.map((publicationItem: TPublication) => new Publication(publicationItem))
						return data
					})
				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		async getConceptAttachments() { // @todo this might belong in a service?
			const response = await fetch('/index.php/apps/opencatalogi/api/attachments?status=Concept', {
				method: 'GET',
			})

			const data = (await response.json()).results

			const entities = data.map((attachmentItem: TAttachment) => new Attachment(attachmentItem))

			this.conceptAttachments = entities

			return { response, data, entities }
		},
		setPublicationDataKey(publicationDataKey: string) {
			this.publicationDataKey = publicationDataKey
			console.log('Active publication data key set to ' + publicationDataKey)
		},
		setAttachmentItem(attachmentItem: Attachment | TAttachment) {
			this.attachmentItem = attachmentItem && new Attachment(attachmentItem)
			console.log('Active attachment item set to ' + attachmentItem)
		},
		setAttachmentFile(files: object) {
			this.attachmentFile = files
			console.log('Active attachment files set to ' + files)
		},
		setPublicationPublicationType(publicationType: string) {
			this.publicationPublicationType = publicationType
		},
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		importFiles(files: any, reset: any) {

			console.log({ files })

			if (!files) {
				throw Error('No files to import')
			}
			if (!reset) {
				throw Error('No reset function to call')
			}

			console.log({ files })

			return axios.post(`/index.php/apps/opencatalogi/api/objects/publication/${this.publicationItem.id}/filesMultipart`, {
				files: files.value,
			}, {
				headers: {
					'Content-Type': 'multipart/form-data',
				},
			})
				.then((response) => {

					console.info('Importing files:', response.data)

				// Wait for the user to read the feedback then close the model
				})
				.catch((err) => {
					console.error('Error importing files:', err)
					throw err
				})

		},
	},
})

/* eslint-disable no-console */
import { Attachment, Publication, TAttachment, TPublication } from '../../entities/index.js'
import { defineStore } from 'pinia'
import { navigationStore } from '../store.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/publications'

interface PublicationStoreState {
    publicationItem: Publication;
    publicationMetaData: string;
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
		publicationItem: null,
		publicationMetaData: null,
		publicationList: [],
		publicationDataKey: null,
		attachmentItem: null,
		attachmentFile: null,
		publicationAttachments: null,
		conceptPublications: [],
		conceptAttachments: [],
	} as PublicationStoreState),
	actions: {
		setPublicationItem(publicationItem: Publication | TPublication) {
			// To prevent forms etc from braking we alway use a default/skeleton object
			this.publicationItem = publicationItem && new Publication(publicationItem)
			console.log('Active publication item set to ' + publicationItem && publicationItem.id)
		},
		setPublicationList(publicationList: Publication[] | TPublication[]) {
			this.publicationList = publicationList.map((publicationItem) => new Publication(publicationItem))
			console.log('Active publication item set to ' + publicationList.length)
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
					response.json().then((data) => {
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
		async getOnePublication(id: number) {
			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Publication(await response.json())

			this.setPublicationItem(data)

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
			navigationStore.setSelectedCatalogus(data?.catalogi?.id)

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
			this.setPublicationItem(null)

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
		getPublicationAttachments(publicationId: number) {
			fetch(`${apiEndpoint}/${publicationId}/attachments`,
				{ method: 'GET' },
			)
				.then((response) => {
					response.json().then((data) => {
						this.publicationAttachments = data.results.map(
							(attachmentItem: TAttachment) => new Attachment(attachmentItem),
						)
						return data
					})
				})
				.catch(
					(err) => {
						console.error(err)
						return err
					},
				)
		},
		getConceptPublications() { // @todo this might belong in a service?
			fetch(
				'/index.php/apps/opencatalogi/api/publications?status=concept',
				{
					method: 'GET',
				},
			)
				.then(
					(response) => {
						response.json().then(
							(data) => {
								this.conceptPublications = data
								return data
							},
						)
					},
				)
				.catch(
					(err) => {
						console.error(err)
						return err
					},
				)
		},
		getConceptAttachments() { // @todo this might belong in a service?
			fetch(
				'/index.php/apps/opencatalogi/api/attachments?status=concept',
				{
					method: 'GET',
				},
			)
				.then(
					(response) => {
						response.json().then(
							(data) => {
								this.conceptAttachments = data
								return data
							},
						)
					},
				)
				.catch(
					(err) => {
						console.error(err)
						return err
					},
				)
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
		setPublicationMetaData(metaData: string) {
			this.publicationMetaData = metaData
		},
	},
})

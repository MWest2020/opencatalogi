/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Metadata, TMetadata } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/metadata'

interface MetadataStoreState {
    metaDataItem: Metadata;
    metaDataList: Metadata[];
    metadataDataKey: string;
}

export const useMetadataStore = defineStore('metadata', {
	state: () => ({
		metaDataItem: null,
		metaDataList: [],
		metadataDataKey: null,
	} as MetadataStoreState),
	actions: {
		setMetaDataItem(metaDataItem: TMetadata) {
			// for backward compatibility
			if (!!metaDataItem && typeof metaDataItem?.properties === 'string') {
				metaDataItem.properties = JSON.parse(metaDataItem.properties)
			}

			this.metaDataItem = metaDataItem && new Metadata(metaDataItem)

			console.log('Active metadata object set to ' + metaDataItem && metaDataItem.id)
		},
		setMetaDataList(metaDataList: TMetadata[]) {
			this.metaDataList = metaDataList.map(
				(metadataItem) => new Metadata(metadataItem),
			)
			console.log('Active metadata lest set')
		},
		setMetadataDataKey(metadataDataKey: string) {
			this.metadataDataKey = metadataDataKey
			console.log('Active metadata data key set to ' + metadataDataKey)
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
		async refreshMetaDataList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, { method: 'GET' })
				.then((response) => {
					response.json().then((data) => {
						this.setMetaDataList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		/* istanbul ignore next */
		async getOneMetadata(id: number) {
			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Metadata(await response.json())

			this.setMetaDataItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addMetadata(metadataItem: Metadata) {
			if (!(metadataItem instanceof Metadata)) {
				throw Error('Please pass a Metadata item from the Metadata class')
			}

			const validateResult = metadataItem.validate()
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

			const data = new Metadata(await response.json())

			this.refreshMetaDataList()
			this.setMetaDataItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editMetadata(metadataItem: Metadata) {
			if (!(metadataItem instanceof Metadata)) {
				throw Error('Please pass a Metadata item from the Metadata class')
			}

			const validateResult = metadataItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(`${apiEndpoint}/${metadataItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Metadata(await response.json())

			this.refreshMetaDataList()
			this.setMetaDataItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteMetadata(id: number) {
			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshMetaDataList()
			this.setMetaDataItem(null)

			return { response }
		},
	},
},
)

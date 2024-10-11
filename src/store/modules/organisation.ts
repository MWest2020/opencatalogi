/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { organization, Torganization } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/organizations'

interface organizationStoreState {
    organizationItem: organization;
    organizationList: organization[];
}

export const useorganizationStore = defineStore('organization', {
	state: () => ({
		organizationItem: null,
		organizationList: [],
	} as organizationStoreState),
	actions: {
		setorganizationItem(organizationItem: Torganization | organization) {
			this.organizationItem = organizationItem && new organization(organizationItem)
			console.log('Active organization item set to ' + organizationItem && organizationItem?.id)
		},
		setorganizationList(organizationList: Torganization[] | organization[]) {
			this.organizationList = organizationList.map(
				(organizationItem) => new organization(organizationItem),
			)
			console.log('organization list set to ' + organizationList.length + ' items')
		},
		/* istanbul ignore next */
		async refreshorganizationList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.setorganizationList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
		/* istanbul ignore next */
		async getAllorganization() {
			const response = await fetch(
				`${apiEndpoint}`,
				{ method: 'get' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((organization: Torganization) => new organization(organization))

			this.organizationList = data

			return { response, data }
		},
		/* istanbul ignore next */
		async getOneorganization(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new organization(await response.json())

			this.setorganizationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addorganization(organizationItem: organization) {
			if (!(organizationItem instanceof organization)) {
				throw Error('Please pass a organization item from the organization class')
			}

			const validateResult = organizationItem.validate()
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

			const data = new organization(await response.json())

			this.refreshorganizationList()
			this.setorganizationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editorganization(organizationItem: organization) {
			if (!(organizationItem instanceof organization)) {
				throw Error('Please pass a organization item from the organization class')
			}

			const validateResult = organizationItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${organizationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new organization(await response.json())

			this.refreshorganizationList()
			this.setorganizationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteorganization(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshorganizationList()
			this.setorganizationItem(null)

			return { response }
		},
	},
})

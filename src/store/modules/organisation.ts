/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Organisation, TOrganisation } from '../../entities/index.js'

const apiEndpoint = '/index.php/apps/opencatalogi/api/organisations'

interface OrganisationStoreState {
    organisationItem: Organisation;
    organisationList: Organisation[];
}

export const useOrganisationStore = defineStore('organisation', {
	state: () => ({
		organisationItem: null,
		organisationList: [],
	} as OrganisationStoreState),
	actions: {
		setOrganisationItem(organisationItem: TOrganisation | Organisation) {
			this.organisationItem = organisationItem && new Organisation(organisationItem)
			console.log('Active organisation item set to ' + organisationItem && organisationItem?.id)
		},
		setOrganisationList(organisationList: TOrganisation[] | Organisation[]) {
			this.organisationList = organisationList.map(
				(organisationItem) => new Organisation(organisationItem),
			)
			console.log('Organisation list set to ' + organisationList.length + ' items')
		},
		/* istanbul ignore next */
		async refreshOrganisationList(search: string = null) {
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
						this.setOrganisationList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
		/* istanbul ignore next */
		async getOneOrganisation(id: number) {
			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Organisation(await response.json())

			this.setOrganisationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addOrganisation(organisationItem: Organisation) {
			if (!(organisationItem instanceof Organisation)) {
				throw Error('Please pass a Organisation item from the Organisation class')
			}

			const validateResult = organisationItem.validate()
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

			const data = new Organisation(await response.json())

			this.refreshOrganisationList()
			this.setOrganisationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editOrganisation(organisationItem: Organisation) {
			if (!(organisationItem instanceof Organisation)) {
				throw Error('Please pass a Organisation item from the Organisation class')
			}

			const validateResult = organisationItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${organisationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Organisation(await response.json())

			this.refreshOrganisationList()
			this.setOrganisationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteOrganisation(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshOrganisationList()
			this.setOrganisationItem(null)

			return { response }
		},
	},
})

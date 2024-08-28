/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Catalogi, TCatalogi } from '../../entities/index.js'

interface CatalogiStoreState {
    catalogiItem: Catalogi;
    catalogiList: Catalogi[];
}

export const useCatalogiStore = defineStore('catalogi', {
	state: () => ({
		catalogiItem: null,
		catalogiList: [],
	} as CatalogiStoreState),
	actions: {
		setCatalogiItem(catalogiItem: TCatalogi) {
			this.catalogiItem = catalogiItem && new Catalogi(catalogiItem)
			console.log('Active catalog item set to ' + catalogiItem && catalogiItem?.id)
		},
		setCatalogiList(catalogiList: TCatalogi[]) {
			this.catalogiList = catalogiList.map(
				(catalogiItem) => new Catalogi(catalogiItem),
			)
			console.log('Catalogi list set to ' + catalogiList.length + ' item')
		},
		/* istanbul ignore next */
		async refreshCatalogiList(search: string = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/catalogi'
			if (search !== null && search !== '') {
				endpoint = endpoint + '?_search=' + search
			}
			return fetch(endpoint, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.setCatalogiList(data.results)
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
		/* istanbul ignore next */
		async getOneCatalogi(id: number) {
			const response = await fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${id}`,
				{ method: 'get' },
			)

			const data = new Catalogi(await response.json())

			this.setCatalogiItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addCatalogi(catalogiItem: Catalogi) {
			if (!(catalogiItem instanceof Catalogi)) {
				throw Error('Please pass a Catalogi item from the Catalogi class')
			}

			const validateResult = catalogiItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				'/index.php/apps/opencatalogi/api/catalogi',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Catalogi(await response.json())

			this.refreshCatalogiList()
			this.setCatalogiItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async editCatalogi(catalogiItem: Catalogi) {
			if (!(catalogiItem instanceof Catalogi)) {
				throw Error('Please pass a Catalogi item from the Catalogi class')
			}

			const validateResult = catalogiItem.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${catalogiItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Catalogi(await response.json())

			this.refreshCatalogiList()
			this.setCatalogiItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deleteCatalogi(id: number) {
			const response = await fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${id}`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)

			this.refreshCatalogiList()
			this.setCatalogiItem(false)

			return { response }
		},
	},
})

import { defineStore } from 'pinia'
import { Catalogi } from '../../entities/catalogi/catalogi.ts'

/** @typedef {import('../../entities/catalogi/catalogi.ts').Catalogi} CatalogEntity */
/** @typedef {{id: string, title: string, [key: string]: any}} ObjectEntity */

/**
 * Store for managing catalogs and their publications in OpenCatalogi.
 * @module Store
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */
export const useCatalogStore = defineStore('catalog', {
	state: () => ({
		/** @type {import('../../entities/catalogi/catalogi.ts').Catalogi|null} */
		activeCatalog: null,

		/** @type {{id: string, title: string, [key: string]: any}|null} */
		activePublication: null,

		/** @type {{results: Array<any>, total: number, page: number, pages: number, limit: number, offset: number}} */
		publications: {
			results: [],
			total: 0,
			page: 1,
			pages: 0,
			limit: 20,
			offset: 0,
		},

		/** @type {boolean} */
		loading: false,
	}),

	actions: {
		/**
		 * Set the active catalog and fetch its publications
		 * @param {CatalogEntity} catalog The catalog to set as active
		 * @return {Promise<void>}
		 */
		async setActiveCatalog(catalog) {
			this.activeCatalog = new Catalogi(catalog)
			await this.fetchPublications()
		},

		/**
		 * Set the active publication
		 * @param {ObjectEntity} publication The publication to set as active
		 * @return {void}
		 */
		setActivePublication(publication) {
			this.activePublication = publication
		},

		/**
		 * Fetch publications for the active catalog
		 * @return {Promise<void>}
		 */
		async fetchPublications() {
			if (!this.activeCatalog) {
				return
			}

			this.loading = true

			try {
				const response = await fetch(`/index.php/apps/opencatalogi/api/catalogi/${this.activeCatalog.id}`)
				const data = await response.json()

				this.publications = {
					...data,
					results: data.results || [],
				}
			} catch (error) {
				console.error('Error fetching publications:', error)
				this.publications = {
					results: [],
					total: 0,
					page: 1,
					pages: 0,
					limit: 20,
					offset: 0,
				}
			} finally {
				this.loading = false
			}
		},

		/**
		 * Clear the active catalog and its publications
		 * @return {void}
		 */
		clearActiveCatalog() {
			this.activeCatalog = null
			this.activePublication = null
			this.publications = {
				results: [],
				total: 0,
				page: 1,
				pages: 0,
				limit: 20,
				offset: 0,
			}
		},
	},

	getters: {
		/**
		 * Get the list of available registers from the active catalog
		 * @return {Array<string>} List of register IDs
		 */
		availableRegisters() {
			return this.activeCatalog?.registers || []
		},

		/**
		 * Get the list of available schemas from the active catalog
		 * @return {Array<string>} List of schema IDs
		 */
		availableSchemas() {
			return this.activeCatalog?.schemas || []
		},

		/**
		 * Check if a catalog is currently active
		 * @return {boolean} True if a catalog is active
		 */
		hasActiveCatalog() {
			return this.activeCatalog !== null
		},

		/**
		 * Check if a publication is currently active
		 * @return {boolean} True if a publication is active
		 */
		hasActivePublication() {
			return this.activePublication !== null
		},
	},
})

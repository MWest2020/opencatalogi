<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'editPublicationData'"
		ref="modalRef"
		class="editPublicationPropertyModal"
		label-id="editPublicationPropertyModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Bewerk publicatie eigenschappen</h2>
			<p>{{ publicationStore.publicationDataKey }}</p>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie eigenschap succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Publicatie eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<NcNoteCard v-if="getActiveMetadataProperty?.deprecated || false" type="warning">
				<p>Deze eigenschap staat gemarkeerd als afgeschaft, hij zal bij een komende versie van het onderliggende publicatie type waarschijnlijk komen te vervallen.</p>
			</NcNoteCard>
			<div v-if="success === null" class="form-group">
				<!-- check if value exists and rules have been received -->
				<div v-if="publication.data[publicationStore.publicationDataKey] !== undefined && !!getActiveMetadataProperty && !publicationLoading">
					<!-- TYPE : STRING -->
					<div v-if=" getActiveMetadataProperty.type === 'string'">
						<NcDateTimePicker v-if="getActiveMetadataProperty.format === 'date'"
							v-model="publication.data[publicationStore.publicationDataKey]"
							type="date"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getActiveMetadataProperty.format === 'time'"
							v-model="publication.data[publicationStore.publicationDataKey]"
							type="time"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getActiveMetadataProperty.format === 'date-time'"
							v-model="publication.data[publicationStore.publicationDataKey]"
							type="datetime"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getActiveMetadataProperty.format === 'email'"
							:value.sync="publication.data[publicationStore.publicationDataKey]"
							label="Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getActiveMetadataProperty.format === 'idn-email'"
							:value.sync="publication.data[publicationStore.publicationDataKey]"
							label="IDN-Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else-if="getActiveMetadataProperty.format === 'regex'"
							:value.sync="publication.data[publicationStore.publicationDataKey]"
							label="Waarde (regex)"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getActiveMetadataProperty.format === 'password'"
							:value.sync="publication.data[publicationStore.publicationDataKey]"
							type="password"
							label="Wachtwoord"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getActiveMetadataProperty.format === 'telephone'"
							:value.sync="publication.data[publicationStore.publicationDataKey]"
							type="tel"
							label="Telefoon nummer"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else
							:value.sync="publication.data[publicationStore.publicationDataKey]"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />
					</div>

					<!-- TYPE : NUMBER -->
					<NcInputField v-else-if="getActiveMetadataProperty.type === 'number'"
						:value.sync="publication.data[publicationStore.publicationDataKey]"
						type="number"
						step="any"
						label="Nummer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : INTEGER -->
					<NcInputField v-else-if="getActiveMetadataProperty.type === 'integer'"
						:value.sync="publication.data[publicationStore.publicationDataKey]"
						type="number"
						step="1"
						label="Integer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : OBJECT -->
					<NcTextArea v-else-if="getActiveMetadataProperty.type === 'object'"
						:value.sync="publication.data[publicationStore.publicationDataKey]"
						label="Object"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : ARRAY -->
					<NcTextArea v-else-if="getActiveMetadataProperty.type === 'array'"
						:value.sync="publication.data[publicationStore.publicationDataKey]"
						label="Waarde lijst (splitst op ,)"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : BOOLEAN -->
					<NcCheckboxRadioSwitch v-else-if="getActiveMetadataProperty.type === 'boolean'"
						:checked.sync="publication.data[publicationStore.publicationDataKey]"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading">
						Waarde
					</NcCheckboxRadioSwitch>
				</div>

				<!-- When loading, show this -->
				<div v-else-if="publicationLoading">
					<NcTextField
						:disabled="true"
						:loading="true"
						:label="'Loading...'"
						value="" />
				</div>

				<!-- when not loading and data doesn't exist show this -->
				<div v-else-if="publication.data[publicationStore.publicationDataKey] === undefined && !publicationLoading">
					<NcNoteCard v-if="error" type="error">
						<p>Something very serious went wrong when loading data</p>
					</NcNoteCard>
				</div>
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="!verifyInput.success || loading"
					type="primary"
					@click="updatePublication(publication.id)">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<ContentSaveOutline v-if="!loading" :size="20" />
						</span>
					</template>
					Opslaan
				</NcButton>
				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success !== null ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<!-- eslint-disable no-console -->
<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcInputField,
	NcDateTimePicker,
	NcCheckboxRadioSwitch,
	NcLoadingIcon,
	NcNoteCard,
} from '@nextcloud/vue'
import { verifyInput as _verifyInput } from './verifyInput.js'
import { setDefaultValue as _setDefaultValue } from './setDefaultValue.js'

import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// icons
		ContentSaveOutline,
	},
	data() {
		return {
			publication: {
				data: {},
			},
			catalogi: {
				value: [],
				options: [],
			},
			metaData: {
				value: [],
				options: [],
			},
			metaDataList: [],
			publicationLoading: true,
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		/**
		 * Finds the metadata and metadata property from metaDataList which equals to the selected metadata key
		 * it will return the full metadata property of the selected property, containing the rules for the data.
		 *
		 * It will return `null` if no property is found
		 * @return {object | null} A single metadata properties object or null
		 */
		getActiveMetadataProperty() {
			if (this.metaDataList.length === 0) return null
			if (Object.keys(this.publication.data).length === 0) return null

			// get the metadata linked to this publication
			const activeMetadata = this.metaDataList.find((metadata) => metadata.source === this.publication.metaData)
			// get all the properties as an array of values (key is not needed as the comparison is done by title)
			const metadataProperties = Object.values(activeMetadata.properties)
			// get the metadata properties with the same title as the publicationDataKey
			const activeMetadataProperty = metadataProperties.find((property) => property.title === publicationStore.publicationDataKey)

			return activeMetadataProperty || null
		},
		/**
		 * Takes the value the user types in and tests it against various rules from `getActiveMetadataProperty`.
		 * Which then returns a success boolean and a helper text containing the error message when success is false.
		 *
		 * @see getActiveMetadataProperty
		 */
		verifyInput() {
			return _verifyInput(this.getActiveMetadataProperty, this.publication.data[publicationStore.publicationDataKey])
		},
	},
	watch: {
		// this should not work.. but it does. so im not gonna touch it.
		getActiveMetadataProperty(newVal) {
			console.log('Loaded metadata property:', newVal)
			this.setDefaultValue(newVal)
		},
	},
	updated() {
		if (navigationStore.modal === 'editPublicationData' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.fetchData(publicationStore.publicationItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		/**
		 * Accepts the selected metadata property or nothing, and changes the value property in `data()` to the default value from the property.
		 *
		 * Depending on the property.type, it will put in specialized data, such as `object` or 'boolean'.
		 *
		 * This function only runs when the selected metadata property changes
		 * @param {object} SelectedMetadataProperty The metadata property Object containing the rules
		 * @see getActiveMetadataProperty
		 */
		setDefaultValue(SelectedMetadataProperty = null) {
			this.publication.data[publicationStore.publicationDataKey] = _setDefaultValue(SelectedMetadataProperty, this.publication.data[publicationStore.publicationDataKey])
		},
		fetchData(id) {
			this.publicationLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publication = data
						// this.publication.data = JSON.stringify(data.data)
						this.catalogi.value = [data.catalogi?.id]
						this.metaData.value = [data.metaData?.id]
					})
					this.publicationLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.publicationLoading = false
				})
		},
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.catalogi = {
							value: this.catalogi.value,
							inputLabel: 'Catalogi',
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[1].id,
								label: catalog[1].name,
							})),

						}
					})
					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchMetaData() {
			this.metaDataLoading = true
			fetch('/index.php/apps/opencatalogi/api/metadata', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.metaDataList = data.results
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		updatePublication(id) {
			this.loading = true

			if (this.publication.data[publicationStore.publicationDataKey] instanceof Date) {
				this.publication.data[publicationStore.publicationDataKey] = this.publication.data[publicationStore.publicationDataKey].toISOString()
			}

			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.publication,
						catalogi: this.publication.catalogi.id,
						metaData: this.publication.metaData,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok

					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})

					const self = this
					setTimeout(() => {
						self.success = null
						this.hasUpdated = false
						navigationStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
					this.hasUpdated = false
					console.error(err)
				})
		},
	},
}
</script>

<style>
.modal__content {
  margin: var(--OC-margin-50);
  text-align: center;
}

.zaakDetailsContainer {
  margin-block-start: var(--OC-margin-20);
  margin-inline-start: var(--OC-margin-20);
  margin-inline-end: var(--OC-margin-20);
}

.success {
  color: green;
}

.flex-horizontal {
    display: flex;
    gap: 4px;
}

.editPublicationPropertyModal .mx-datepicker {
    margin-top: 0rem;
    transition: margin 400ms;
}
.editPublicationPropertyModal .mx-datepicker:has(.mx-datepicker-popup) {
    margin-top: 12rem;
}
</style>

<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'addPublicationData'"
		ref="modalRef"
		class="addPublicationPropertyModal"
		label-id="addPublicationPropertyModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie eigenschap toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie eigenschap succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Publicatie eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<NcNoteCard v-if="getSelectedMetadataProperty?.deprecated || false" type="warning">
				<p>Deze eigenschap staat gemarkeerd als afgeschaft, hij zal bij een komende versie van het onderliggende publicatie type waarschijnlijk komen te vervallen.</p>
			</NcNoteCard>
			<div v-if="success === null" class="form-group">
				<NcSelect v-bind="mapMetadataEigenschappen"
					v-model="eigenschappen.value"
					required />

				<div v-if="!!getSelectedMetadataProperty">
					<!-- TYPE : STRING -->
					<div v-if="getSelectedMetadataProperty.type === 'string'">
						<NcDateTimePicker v-if="getSelectedMetadataProperty.format === 'date'"
							v-model="value"
							type="date"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedMetadataProperty.format === 'time'"
							v-model="value"
							type="time"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedMetadataProperty.format === 'date-time'"
							v-model="value"
							type="datetime"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'email'"
							:value.sync="value"
							label="Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'idn-email'"
							:value.sync="value"
							label="IDN-Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else-if="getSelectedMetadataProperty.format === 'regex'"
							:value.sync="value"
							label="Waarde (regex)"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'password'"
							:value.sync="value"
							type="password"
							label="Wachtwoord"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'telephone'"
							:value.sync="value"
							type="tel"
							label="Telefoon nummer"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else
							:value.sync="value"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />
					</div>

					<!-- TYPE : NUMBER -->
					<NcInputField v-else-if="getSelectedMetadataProperty.type === 'number'"
						:value.sync="value"
						type="number"
						step="any"
						label="Nummer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : INTEGER -->
					<NcInputField v-else-if="getSelectedMetadataProperty.type === 'integer'"
						:value.sync="value"
						type="number"
						step="1"
						label="Integer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : OBJECT -->
					<NcTextArea v-else-if="getSelectedMetadataProperty.type === 'object'"
						:value.sync="value"
						label="Object"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : ARRAY -->
					<NcTextArea v-else-if="getSelectedMetadataProperty.type === 'array'"
						:value.sync="value"
						label="Waarde lijst (splitst op ,)"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : BOOLEAN -->
					<NcCheckboxRadioSwitch v-else-if="getSelectedMetadataProperty.type === 'boolean'"
						:checked.sync="value"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading">
						Waarde
					</NcCheckboxRadioSwitch>
				</div>
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="loading
						|| !eigenschappen.value?.id
						|| !verifyInput.success
					"
					type="primary"
					@click="AddPublicatieEigenschap()">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Plus v-if="!loading" :size="20" />
						</span>
					</template>
					Toevoegen
				</NcButton>

				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
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
	NcTextArea,
	NcDateTimePicker,
	NcCheckboxRadioSwitch,
	NcInputField,
	NcNoteCard,
	NcLoadingIcon,
	NcSelect,
} from '@nextcloud/vue'
import { verifyInput as _verifyInput } from './verifyInput.js'
import { setDefaultValue as _setDefaultValue } from './setDefaultValue.js'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcDateTimePicker,
		NcCheckboxRadioSwitch,
		NcInputField,
		NcSelect,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			eigenschappen: {},
			metaData: {},
			value: '',
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		// I write documentation to help me understand what I need to do.

		/**
		 * Takes the properties from the metadata in the store and loops through them, returning only the items not in the publication data
		 * @return {Array<object> | []} list of metadata properties NOT in the publication data
		 */
		getFilteredMetadataProperties() {
			if (!publicationStore.publicationMetaData?.properties) return []
			return Object.values(publicationStore.publicationMetaData?.properties)
				.filter((prop) => !Object.keys(publicationStore.publicationItem?.data).includes(prop.title))
		},
		/**
		 * based on the result `getFilteredMetadataProperties` gives AND the selected value in the eigenschappen dropdown,
		 * it will return the full metadata property of the selected property, containing the rules for the data.
		 *
		 * It will return `null` if no property is selected
		 * @see getFilteredMetadataProperties
		 * @return {object | null} A single metadata properties object or null
		 */
		getSelectedMetadataProperty() {
			return this.getFilteredMetadataProperties.find((prop) => prop?.title === this.eigenschappen.value?.label) || null
		},
		mapMetadataEigenschappen() {
			if (publicationStore.publicationMetaData) {
				const incomingUrl = new URL(publicationStore.publicationMetaData.source)
				if (incomingUrl?.host !== window.location.host) {
					return {
						inputLabel: 'Publicatie type eigenschap',
						options: Object.keys(publicationStore.publicationMetaData?.properties)
							.filter((prop) => !Object.keys(publicationStore.publicationItem?.data).includes(prop))
							.map((prop) => ({
								id: prop,
								label: prop,
							})),
					}
				}
			}

			return {
				inputLabel: 'Publicatie type eigenschap',
				options: this.getFilteredMetadataProperties
					.map((prop) => ({
						id: prop.title,
						label: prop.title,
					})),
			}
		},
		/**
		 * Takes the value the user types in and tests it against various rules from `getSelectedMetadataProperty`.
		 * Which then returns a success boolean and a helper text containing the error message when success is false.
		 *
		 * @see getSelectedMetadataProperty
		 */
		verifyInput() {
			return _verifyInput(this.getSelectedMetadataProperty, this.value)
		},
	},
	watch: {
		getSelectedMetadataProperty(newVal) {
			console.log('new selected metadata property', newVal)
			this.setDefaultValue(newVal)
		},
	},
	methods: {
		/**
		 * Accepts the selected metadata property, and changes the value property in `data()` to the default value from the property.
		 *
		 * Depending on the property.type, it will put in specialized data, such as `object` or `boolean`.
		 *
		 * This function only runs when the selected metadata property changes
		 * @param {object} SelectedMetadataProperty The metadata property Object containing the rules
		 * @see getSelectedMetadataProperty
		 */
		setDefaultValue(SelectedMetadataProperty) {
			this.value = _setDefaultValue(SelectedMetadataProperty)
		},
		AddPublicatieEigenschap() {
			this.loading = true

			const bodyData = publicationStore.publicationItem
			bodyData.data[this.eigenschappen.value?.label] = this.value
			delete bodyData.publicationDate

			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...bodyData,
						catalogi: bodyData.catalogi.id,
						metaData: bodyData.metaData.id,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok

					// Lets refresh the publicationList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)

					// reset modal form
					this.eigenschappen = {}
					this.data = ''
				})
				.catch((err) => {
					this.loading = false
					this.error = err
				})
		},
		fetchMetaData(metaDataUrl, loading) {

			if (loading) { this.metaDataLoading = true }

			fetch(`/index.php/apps/opencatalogi/api/metadata?source=${metaDataUrl}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.metadata = data.results[0]
					})
					if (loading) { this.metaDataLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.metaDataLoading = false }
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

.form-group .group {
    margin-block-end: 2rem;
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

.addPublicationPropertyModal .mx-datepicker {
    margin-top: 0rem;
    transition: margin 400ms;
}
.addPublicationPropertyModal .mx-datepicker:has(.mx-datepicker-popup) {
    margin-top: 12rem;
}
</style>

<script setup>
import { navigationStore, organisationStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'editOrganisation'"
		ref="modalRef"
		label-id="addOrganisationModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Organisatie Bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Organisatie succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Organisatie</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div class="formContainer">
				<div v-if="success === null" class="form-group">
					<NcTextField
						:disabled="loading"
						label="Titel"
						:value.sync="organisation.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Samenvatting"
						:value.sync="organisation.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea
						:disabled="loading"
						label="Beschrijving"
						:value.sync="organisation.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="OIN (organisatie-identificatienummer)"
						:value.sync="organisation.oin"
						:error="!!inputValidation.fieldErrors?.['oin']"
						:helper-text="inputValidation.fieldErrors?.['oin']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="TOOI"
						:value.sync="organisation.tooi"
						:error="!!inputValidation.fieldErrors?.['tooi']"
						:helper-text="inputValidation.fieldErrors?.['tooi']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="RSIN"
						:value.sync="organisation.rsin"
						:error="!!inputValidation.fieldErrors?.['rsin']"
						:helper-text="inputValidation.fieldErrors?.['rsin']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="PKI"
						:value.sync="organisation.pki"
						:error="!!inputValidation.fieldErrors?.['pki']"
						:helper-text="inputValidation.fieldErrors?.['pki']?.[0]" />
				</div>
			</div>
			<NcButton
				v-if="success === null"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="editOrganisation()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import { Organisation } from '../../entities/index.js'

export default {
	name: 'EditOrganisationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			organisation: {
				title: '',
				summary: '',
				description: '',
				oin: '',
				tooi: '',
				rsin: '',
				pki: '',
			},
			loading: false,
			success: null,
			error: false,
			hasUpdated: false,
		}
	},
	computed: {
		inputValidation() {
			const item = new Organisation({
				...this.organisation,
			})

			const result = item.validate()

			return {
				success: result.success,
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		organisationStore.organisationItem && (this.organisation = organisationStore.organisationItem)
	},
	updated() {
		if (navigationStore.modal === 'editOrganisation' && this.hasUpdated) {
			if (this.organisation.id === organisationStore.organisationItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editOrganisation' && !this.hasUpdated) {
			organisationStore.organisationItem && (this.organisation = organisationStore.organisationItem)
			this.fetchData(organisationStore.organisationItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true

			organisationStore.getOneOrganisation(id)
				.then(({ response, data }) => {
					this.organisation = data
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		editOrganisation() {
			this.loading = true
			this.error = false

			const organisationItem = new Organisation({
				...this.organisation,
			})

			organisationStore.editOrganisation(organisationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('organisations')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
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

.formContainer > * {
  margin-block-end: 10px;
}

.selectGrid {
  display: grid;
  grid-gap: 5px;
  grid-template-columns: 1fr 1fr;
}

.zaakDetailsContainer {
  margin-block-start: var(--OC-margin-20);
  margin-inline-start: var(--OC-margin-20);
  margin-inline-end: var(--OC-margin-20);
}

.success {
  color: green;
}

.APM-horizontal {
  display: flex;
  gap: 4px;
  flex-direction: row;
  align-items: center;
}
</style>

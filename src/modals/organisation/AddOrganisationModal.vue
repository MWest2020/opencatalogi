<script setup>
import { navigationStore, organizationStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'organizationAdd'"
		ref="modalRef"
		label-id="addorganizationModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Organisatie toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Organisatie succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Organisatie</p>
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
						:value.sync="organization.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Samenvatting"
						:value.sync="organization.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea
						:disabled="loading"
						label="Beschrijving"
						:value.sync="organization.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="OIN (organisatie-identificatienummer)"
						:value.sync="organization.oin"
						:error="!!inputValidation.fieldErrors?.['oin']"
						:helper-text="inputValidation.fieldErrors?.['oin']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="TOOI"
						:value.sync="organization.tooi"
						:error="!!inputValidation.fieldErrors?.['tooi']"
						:helper-text="inputValidation.fieldErrors?.['tooi']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="RSIN"
						:value.sync="organization.rsin"
						:error="!!inputValidation.fieldErrors?.['rsin']"
						:helper-text="inputValidation.fieldErrors?.['rsin']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="PKI"
						:value.sync="organization.pki"
						:error="!!inputValidation.fieldErrors?.['pki']"
						:helper-text="inputValidation.fieldErrors?.['pki']?.[0]" />
				</div>
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="addorganization()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
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
import Plus from 'vue-material-design-icons/Plus.vue'
import { organization } from '../../entities/index.js'

export default {
	name: 'AddorganizationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {
			organization: {
				title: '',
				summary: '',
				description: '',
				oin: '',
				tooi: '',
				rsin: '',
				pki: '',
			},

			errorCode: '',
			organizationLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		inputValidation() {
			const item = new organization({
				...this.organization,
			})

			const result = item.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'organizationAdd' && !this.hasUpdated) {
			this.hasUpdated = true
		}
	},
	methods: {
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addorganization() {
			this.loading = true
			this.error = false

			const organizationItem = new organization({
				...this.organization,
			})

			organizationStore.addorganization(organizationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('organizations')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
						self.organization = {
							title: '',
							summary: '',
							description: '',
							oin: '',
							tooi: '',
							rsin: '',
							pki: '',
						}
						self.hasUpdated = false
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					self.hasUpdated = false
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

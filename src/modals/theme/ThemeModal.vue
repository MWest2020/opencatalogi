<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'theme'"
		ref="modalRef"
		:label-id="isEdit ? 'editThemeModal' : 'addThemeModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Thema {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>
			<div v-if="objectStore.getState('theme').success !== null || objectStore.getState('theme').error">
				<NcNoteCard v-if="objectStore.getState('theme').success" type="success">
					<p>{{ isEdit ? 'Thema succesvol bewerkt' : 'Thema succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('theme').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van het thema' : 'Er is iets fout gegaan bij het toevoegen van thema' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('theme').error" type="error">
					<p>{{ objectStore.getState('theme').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('theme').success === null" class="formContainer">
				<div class="form-group">
					<NcTextField
						:disabled="objectStore.isLoading('theme')"
						label="Titel"
						:value.sync="theme.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField
						:disabled="objectStore.isLoading('theme')"
						label="Samenvatting"
						:value.sync="theme.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea
						:disabled="objectStore.isLoading('theme')"
						label="Beschrijving"
						:value.sync="theme.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
					<NcTextField
						:disabled="objectStore.isLoading('theme')"
						label="Image"
						:value.sync="theme.image"
						:error="!!inputValidation.fieldErrors?.['image']"
						:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
				</div>
			</div>
			<NcButton v-if="objectStore.getState('theme').success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('theme')"
				type="primary"
				@click="saveTheme">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('theme')" :size="20" />
					<ContentSaveOutline v-if="!objectStore.isLoading('theme')" :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
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

import { Theme } from '../../entities/index.js'

export default {
	name: 'ThemeModal',
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
			theme: {
				title: '',
				summary: '',
				description: '',
				image: '',
			},
			hasUpdated: false,
		}
	},
	computed: {
		isEdit() {
			return !!objectStore.getActiveObject('theme')
		},
		inputValidation() {
			const themeItem = new Theme({
				...this.theme,
			})

			const result = themeItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'theme' && !this.hasUpdated) {
			if (this.isEdit) {
				const activeTheme = objectStore.getActiveObject('theme')
				this.theme = { ...activeTheme }
			}
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.theme = {
				title: '',
				summary: '',
				description: '',
				image: '',
			}
			// Reset the object store state
			objectStore.setState('theme', { success: null, error: null })
		},
		saveTheme() {
			const themeItem = new Theme({
				...this.theme,
			})

			if (this.isEdit) {
				objectStore.updateObject('theme', themeItem.id, themeItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('theme', themeItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			}
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

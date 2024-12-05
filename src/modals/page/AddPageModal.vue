<script setup>
import { navigationStore, pageStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
</script>

<template>
	<NcModal
		v-if="navigationStore.modal === 'pageAdd'"
		ref="modalRef"
		label-id="addPageModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Pagina toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Pagina succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Pagina</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div class="formContainer">
				<div v-if="success === null" class="form-group">
					<NcTextField
						:disabled="loading"
						label="Naam"
						:value.sync="page.name"
						:error="!!inputValidation.fieldErrors?.['name']"
						:helper-text="inputValidation.fieldErrors?.['name']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Slug"
						:value.sync="page.slug"
						:error="!!inputValidation.fieldErrors?.['slug']"
						:helper-text="inputValidation.fieldErrors?.['slug']?.[0]" />

					<div :class="`codeMirrorContainer ${getTheme()}`">
						<CodeMirror
							v-model="page.contents"
							:basic="true"
							placeholder="{ &quot;key&quot;: &quot;value&quot; }"
							:dark="getTheme() === 'dark'"
							:tab="true"
							:gutter="true"
							:linter="jsonParseLinter()"
							:lang="json()" />
						<NcButton class="prettifyButton" :disabled="!page.contents || !verifyJsonValidity(page.contents)" @click="prettifyJson">
							<template #icon>
								<AutoFix :size="20" />
							</template>
							Prettify
						</NcButton>
					</div>
				</div>
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading || !verifyJsonValidity(page.contents)"
				type="primary"
				@click="addPage()">
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
	NcTextField,
} from '@nextcloud/vue'
import CodeMirror from 'vue-codemirror6'
import { json, jsonParseLinter } from '@codemirror/lang-json'

import Plus from 'vue-material-design-icons/Plus.vue'
import AutoFix from 'vue-material-design-icons/AutoFix.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'AddPageModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		CodeMirror,
		// Icons
		Plus,
		AutoFix,
	},
	data() {
		return {
			page: {
				name: '',
				slug: '',
				contents: '',
			},
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		inputValidation() {
			const pageItem = new Page({
				...this.page,
			})

			const result = pageItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'pageAdd' && !this.hasUpdated) {
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
		prettifyJson() {
			this.page.contents = JSON.stringify(JSON.parse(this.page.contents), null, 2)
		},
		addPage() {
			this.loading = true
			this.error = false

			const pageItem = new Page({
				...this.page,
				contents: JSON.stringify(JSON.parse(this.page.contents), null, 2),
			})

			pageStore.addPage(pageItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						navigationStore.setModal(false)
						self.success = null
						self.hasUpdated = false
						self.page = {
							name: '',
							slug: '',
							contents: '',
						}
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					self.hasUpdated = false
				})
		},
		verifyJsonValidity(jsonInput) {
			if (jsonInput === '') return true
			try {
				JSON.parse(jsonInput)
				return true
			} catch (e) {
				return false
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

.success {
  color: green;
}
</style>

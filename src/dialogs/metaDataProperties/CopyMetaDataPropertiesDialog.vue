<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'copyMetaDataProperty'"
		name="Publicatietype eigenschap verwijderen"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatietype eigenschap succesvol gekopieerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het kopiëren van publicatietype eigenschap</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="success === null">
			Wil je <b>{{ metadataStore.metadataDataKey }}</b> kopiëren?
		</p>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success !== null ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="success === null"
				:disabled="loading"
				icon="Delete"
				type="error"
				@click="CopyProperty()">
				<template #icon>
					<Cancel v-if="loading" :size="20" />
					<ContentCopy v-if="!loading" :size="20" />
				</template>
				Kopiëren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

import { Metadata } from '../../entities/index.js'

export default {
	name: 'CopyMetaDataPropertiesDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		// Icons
		Cancel,
		ContentCopy,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: false,
		}
	},
	methods: {
		CopyProperty() {
			this.loading = true

			const metadataItemClone = { ...metadataStore.metaDataItem }

			const name = 'kopie_' + metadataStore.metadataDataKey
			metadataItemClone.properties[name] = metadataItemClone.properties[metadataStore.metadataDataKey]

			const newMetadataItem = new Metadata({
				...metadataItemClone,
			})

			metadataStore.editMetadata(newMetadataItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('metaData')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = false
						navigationStore.setDialog(false)
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

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>

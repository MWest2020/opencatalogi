<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'copyPublication'"
		name="Publicatie kopiëren"
		:can-close="false">
		<div v-if="objectStore.getState('publication').success !== null || objectStore.getState('publication').error">
			<NcNoteCard v-if="objectStore.getState('publication').success" type="success">
				<p>Publicatie succesvol gekopieerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('publication').success" type="error">
				<p>Er is iets fout gegaan bij het kopiëren van publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('publication').error" type="error">
				<p>{{ objectStore.getState('publication').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('publication')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Publicatie wordt gekopieerd...</span>
		</div>
		<p v-if="objectStore.getState('publication').success === null && !objectStore.isLoading('publication')">
			Wil je <b>{{ objectStore.getActiveObject('publication')?.title }}</b> kopiëren? Dit zal een nieuwe publicatie maken met dezelfde gegevens.
		</p>
		<template v-if="objectStore.getState('publication').success === null && !objectStore.isLoading('publication')" #actions>
			<NcButton 
				:disabled="objectStore.isLoading('publication')" 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('publication')"
				icon="Copy"
				type="primary"
				@click="copyPublication()">
				<template #icon>
					<Copy :size="20" />
				</template>
				Kopiëren
			</NcButton>
		</template>
		<template v-else #actions>
			<NcButton 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Sluiten
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Copy from 'vue-material-design-icons/ContentCopy.vue'
import { Publication } from '../../entities/index.js'

/**
 * Copy publication dialog component
 *
 * @category Dialogs
 * @package
 * @author Your Name
 * @copyright 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/your-repo
 */
export default {
	name: 'CopyPublicationDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Copy,
	},
	methods: {
		/**
		 * Copy the active publication
		 *
		 * @return {void}
		 */
		copyPublication() {
			const activePublication = objectStore.getActiveObject('publication')
			if (!activePublication?.id) return

			const publicationClone = { ...activePublication }
			publicationClone.title = 'KOPIE: ' + publicationClone.title
			if (Object.keys(publicationClone.data).length === 0) {
				delete publicationClone.data
			}
			delete publicationClone.id
			delete publicationClone._id
			publicationClone.status = 'Concept'

			const publicationItem = new Publication({
				...publicationClone,
				catalog: publicationClone.catalog.id ?? publicationClone.catalog,
				publicationType: publicationClone.publicationType,
			})

			objectStore.createObject('publication', publicationItem)
				.then(() => {
					navigationStore.setSelected('publication')
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('publication', { success: null, error: null })
						navigationStore.setDialog(false)
					}, 2000)
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

.loading-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 1rem 0;
    color: var(--color-text-lighter);
}
</style>

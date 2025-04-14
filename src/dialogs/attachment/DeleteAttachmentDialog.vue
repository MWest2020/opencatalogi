<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deleteAttachment'"
		name="Bijlage verwijderen"
		:can-close="false">
		<div v-if="objectStore.getState('attachment').success !== null || objectStore.getState('attachment').error">
			<NcNoteCard v-if="objectStore.getState('attachment').success" type="success">
				<p>Bijlage succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('attachment').success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van bijlage</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('attachment').error" type="error">
				<p>{{ objectStore.getState('attachment').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('attachment')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Bijlage wordt verwijderd...</span>
		</div>
		<p v-if="objectStore.getState('attachment').success === null && !objectStore.isLoading('attachment')">
			Wil je <b>{{ objectStore.getActiveObject('attachment')?.name }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<template v-if="objectStore.getState('attachment').success === null && !objectStore.isLoading('attachment')" #actions>
			<NcButton 
				:disabled="objectStore.isLoading('attachment')" 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('attachment')"
				icon="Delete"
				type="error"
				@click="deleteAttachment()">
				<template #icon>
					<Delete :size="20" />
				</template>
				Verwijderen
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
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Delete attachment dialog component
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
	name: 'DeleteAttachmentDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	methods: {
		/**
		 * Delete the active attachment
		 *
		 * @return {void}
		 */
		deleteAttachment() {
			const activeAttachment = objectStore.getActiveObject('attachment')
			if (!activeAttachment?.id) return

			objectStore.deleteObject('attachment', activeAttachment.id)
				.then(() => {
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('attachment', { success: null, error: null })
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

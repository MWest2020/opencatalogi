<script setup>
import { publicationStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		name="Bijlagen verwijderen"
		:can-close="false">
		<template v-if="!succes">
			<p>
				Weet je zeker dat je {{ `${attachmentsToDelete.length === 1 ? '' : 'deze'} ${attachmentsToDelete.length} ${attachmentsToDelete.length === 1 ? 'bijlage' : 'bijlagen'}` }}
				definitief wilt verwijderen?
				Deze actie kan niet ongedaan worden gemaakt.
			</p>
			<ul>
				<li
					v-for="attachment in attachmentsToDelete"
					:key="attachment.id">
					<strong>{{ attachment.title }}</strong>
				</li>
			</ul>
		</template>
		<NcNoteCard v-if="succes" type="success">
			<p>De geselecteerde bijlagen zijn succesvol verwijderd.</p>
		</NcNoteCard>

		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>

		<template #actions>
			<NcButton
				:disabled="loading"
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>

			<NcButton
				v-if="!succes"
				:disabled="loading"
				icon="Delete"
				type="error"
				@click="deleteAttachments">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Delete v-if="!loading" :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

export default {
	name: 'DeleteMultipleAttachmentsDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		Cancel,
		Delete,
	},
	props: {
		attachmentsToDelete: {
			type: Array,
			required: true,
		},
	},
	data() {
		return {
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		async deleteAttachments() {
			this.loading = true
			this.error = false
			const promises = this.attachmentsToDelete.map((attachment) => {
				return publicationStore.deleteFile(
					publicationStore.publicationItem.id,
					attachment.title,
				)
			})

			const results = await Promise.allSettled(promises)

			let successCount = 0
			let failCount = 0

			for (const result of results) {
				if (result.status === 'fulfilled') {
					const response = result.value
					if (response.status === 200) {
						successCount++
					} else {
						failCount++
					}
				} else {
					failCount++
				}
			}

			if (successCount > 0) {
				this.succes = true
			}
			if (failCount > 0) {
				this.error = 'Niet alle bijlagen konden worden verwijderd.'
			}

			publicationStore.getPublicationAttachments(
				publicationStore.publicationItem.id,
				{
					page: 1,
					limit: publicationStore.limit,
				},
			)

			this.loading = false

			setTimeout(() => {
				this.$emit('done')
			}, 2000)
		},
		handleClose() {
			this.$emit('cancel')
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
</style>

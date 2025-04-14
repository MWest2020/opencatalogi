<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="glossary-details-container">
		<div v-if="objectStore.isLoading('glossary')" class="loading-container">
			<NcLoadingIcon :size="32" />
		</div>

		<div v-else-if="!objectStore.getActiveObject('glossary')" class="empty-state">
			<p>Selecteer een term om de details te bekijken.</p>
		</div>

		<div v-else class="glossary-details">
			<div class="glossary-details-header">
				<h2>{{ objectStore.getActiveObject('glossary').title }}</h2>
				<div class="glossary-details-actions">
					<NcButton type="tertiary" @click="navigationStore.setModal('glossary')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcButton>
					<NcButton type="tertiary" @click="navigationStore.setSelected('glossary')">
						<template #icon>
							<ArrowLeft :size="20" />
						</template>
						Terug
					</NcButton>
				</div>
			</div>

			<div class="glossary-details-content">
				<div class="glossary-details-section">
					<h3>Beschrijving</h3>
					<p>{{ objectStore.getActiveObject('glossary').description }}</p>
				</div>

				<div class="glossary-details-section">
					<h3>Definitie</h3>
					<p>{{ objectStore.getActiveObject('glossary').definition }}</p>
				</div>

				<div class="glossary-details-section">
					<h3>Gerelateerde termen</h3>
					<div v-if="objectStore.getActiveObject('glossary').relatedTerms?.length" class="related-terms">
						<NcButton v-for="term in objectStore.getActiveObject('glossary').relatedTerms"
							:key="term.id"
							type="secondary"
							@click="selectTerm(term)">
							{{ term.title }}
						</NcButton>
					</div>
					<p v-else>Geen gerelateerde termen</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { NcButton, NcLoadingIcon } from '@nextcloud/vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue'

/**
 * Glossary details component
 * 
 * @category Views
 * @package OpenCatalogi
 * @author Your Name
 * @copyright 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/your-repo
 */
export default {
	name: 'GlossaryDetails',
	components: {
		NcButton,
		NcLoadingIcon,
		Pencil,
		ArrowLeft,
	},
	methods: {
		/**
		 * Select a related term
		 * 
		 * @param {Object} term - The term to select
		 * @returns {void}
		 */
		selectTerm(term) {
			objectStore.setActiveObject('glossary', term)
		},
	},
}
</script>

<style scoped>
.glossary-details-container {
	padding: var(--OC-margin-20);
}

.loading-container {
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 200px;
}

.empty-state {
	text-align: center;
	padding: var(--OC-margin-20);
	color: var(--color-text-lighter);
}

.glossary-details-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: var(--OC-margin-20);
}

.glossary-details-actions {
	display: flex;
	gap: var(--OC-margin-10);
}

.glossary-details-content {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-20);
}

.glossary-details-section {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-10);
}

.related-terms {
	display: flex;
	flex-wrap: wrap;
	gap: var(--OC-margin-10);
}
</style> 
/**
 * GlossaryIndex.vue
 * Component for displaying glossary items and their details
 * @category Views
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<GlossaryList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen Glossary Items"
				description="Nog geen glossary items geselecteerd">
				<template #icon>
					<FormatListBulleted />
				</template>
				<template #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('glossary'); navigationStore.setModal('glossary')">
						Glossary Item toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<GlossaryDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import GlossaryList from './GlossaryList.vue'
import GlossaryDetails from './GlossaryDetails.vue'
import FormatListBulleted from 'vue-material-design-icons/FormatListBulleted.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activeGlossary = computed(() => objectStore.getActiveObject('glossary'))

const showEmptyContent = computed(() => {
	const hasActiveGlossary = activeGlossary.value
	const isGlossarySelected = selected.value === 'glossary'
	return !hasActiveGlossary && isGlossarySelected
})

export default {
	name: 'GlossaryIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		GlossaryList,
		GlossaryDetails,
		FormatListBulleted,
	},
}
</script>

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<CatalogiList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen Catalogi"
				description="Nog geen catalogi geselecteerd">
				<template #icon>
					<DatabaseOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('catalog '); navigationStore.setModal('catalog')">
						Catalogi toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<CatalogiDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import CatalogiList from './CatalogiList.vue'
import CatalogiDetails from './CatalogiDetails.vue'

// eslint-disable-next-line n/no-missing-import
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activeCatalog = computed(() => objectStore.getActiveObject('catalog'))

const showEmptyContent = computed(() => {
	const hasActiveCatalog = activeCatalog.value
	const isCatalogSelected = selected.value === 'catalogi'
	return !hasActiveCatalog || !isCatalogSelected
})

export default {
	name: 'CatalogiIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		CatalogiList,
		CatalogiDetails,
		DatabaseOutline,
	},
}
</script>

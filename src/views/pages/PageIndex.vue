/**
 * PageIndex.vue
 * Component for displaying pages and their details
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
			<PageList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen Pages"
				description="Nog geen pagina's geselecteerd">
				<template #icon>
					<Web />
				</template>
				<template #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('page'); navigationStore.setModal('page')">
						Pagina toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PageDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PageList from './PageList.vue'
import PageDetails from './PageDetail.vue'
import Web from 'vue-material-design-icons/Web.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activePage = computed(() => objectStore.getActiveObject('page'))

const showEmptyContent = computed(() => {
	const hasActivePage = activePage.value
	const isPageSelected = selected.value === 'pages'
	return !hasActivePage && isPageSelected
})

export default {
	name: 'PageIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		PageList,
		PageDetails,
		Web,
	},
}
</script>

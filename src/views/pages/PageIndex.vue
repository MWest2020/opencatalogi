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
				name="Geen pagina"
				description="Nog geen pagina geselecteerd">
				<template #icon>
					<Web />
				</template>
				<template #action>
					<NcButton type="primary" @click="pageStore.setPageItem(null); navigationStore.setModal('pageForm')">
						Pagina toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PageDetail v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PageList from './PageList.vue'
import PageDetail from './PageDetail.vue'
import Web from 'vue-material-design-icons/Web.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activePage = computed(() => objectStore.getActiveObject('page'))

const showEmptyContent = computed(() => {
	const hasActivePage = activePage.value
	const isPageSelected = selected.value === 'pages'
	return !hasActivePage || !isPageSelected
})

export default {
	name: 'PageIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		PageList,
		PageDetail,
		NcButton,
		// Icons
		Web,
	},
	data() {
		return {

		}
	},
}
</script>

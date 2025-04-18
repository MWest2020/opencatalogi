<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<MenuList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen menu"
				description="Nog geen menu geselecteerd">
				<template #icon>
					<MenuIcon />
				</template>
				<template #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('menu'); navigationStore.setModal('menu')">
						Menu toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<MenuDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import MenuList from './MenuList.vue'
import MenuDetails from './MenuDetail.vue'
import Menu from 'vue-material-design-icons/Menu.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activeMenu = computed(() => objectStore.getActiveObject('menu'))

const showEmptyContent = computed(() => {
	const hasActiveMenu = activeMenu.value
	const isMenuSelected = selected.value === 'menus'
	return !hasActiveMenu && isMenuSelected
})

export default {
	name: 'MenuIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		MenuList,
		MenuDetails,
		// Menu is reserved in HTML, so we use MenuIcon instead
		MenuIcon: Menu,
	},
}
</script>

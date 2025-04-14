<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<MenuList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!objectStore.getActiveObject('menu') || navigationStore.selected !== 'menu'"
				class="detailContainer"
				name="Geen menu"
				description="Nog geen menu geselecteerd">
				<template #icon>
					<Menu />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('addMenu')">
						Menu toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<MenuDetails v-if="objectStore.getActiveObject('menu') && navigationStore.selected === 'menu'" :menu-item="objectStore.getActiveObject('menu')" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import MenuList from './MenuList.vue'
import MenuDetails from './MenuDetail.vue'
import Menu from 'vue-material-design-icons/Menu.vue'

export default {
	name: 'MenuIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		MenuList,
		MenuDetails,
		Menu,
	},
}
</script>

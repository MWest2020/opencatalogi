/* eslint-disable no-console */
import { defineStore } from 'pinia'

interface NavigationStoreState {
    selected: 'dashboard' | 'publication' | 'catalogi' | 'metaData' | 'organizations' | 'themes' | 'search';
    selectedCatalogus: string;
    modal: string;
    dialog: string;
    transferData: string;
}

export const useNavigationStore = defineStore('ui', {
	state: () => ({
		// The currently active menu item, defaults to '' which triggers the dashboard
		selected: 'dashboard',
		// The currently selected catalogi within 'publications'
		selectedCatalogus: null,
		// The currently active modal, managed trough the state to ensure that only one modal can be active at the same time
		modal: null,
		// The currently active dialog
		dialog: null,
		// Any data needed in various models, dialogs, views which cannot be transferred through normal means or without writing crappy/excessive code
		transferData: null,
	} as NavigationStoreState),
	actions: {
		setSelected(selected: NavigationStoreState['selected']) {
			this.selected = selected
			console.log('Active menu item set to ' + selected)
		},
		setSelectedCatalogus(selectedCatalogus: string) {
			this.selectedCatalogus = selectedCatalogus
			console.log('Active catalogus menu set to ' + selectedCatalogus)
		},
		setModal(modal: string) {
			this.modal = modal
			console.log('Active modal set to ' + modal)
		},
		setDialog(dialog: string) {
			this.dialog = dialog
			console.log('Active dialog set to ' + dialog)
		},
		setTransferData(transferData: string) {
			this.transferData = transferData
		},
		getTransferData(): string {
			const tempData = this.transferData
			this.transferData = null
			return tempData
		},
	},
})

<template>
	<div>
		<auto-names-settings
			v-model="enabledValue"
			:can-edit="canEdit"
		></auto-names-settings>
		<cdx-message
			v-if="!canEdit"
			class="ext-cite-autoname-notice-footer"
			inline>
			{{ $i18n( 'communityconfiguration-editor-client-notice-footer-message' ).text() }}
		</cdx-message>
		<cdx-button
			:disabled="!canEdit"
			action="progressive"
			weight="primary"
			@click="onSubmit"
		>
			{{ $i18n( 'communityconfiguration-editor-form-submit-button-text' ).text() }}
		</cdx-button>
		<p v-if="message.show">
			<cdx-message
				v-if="message.show"
				:type="message.type"
				:fade-in="true"
				:allow-user-dismiss="true"
				:auto-dismiss="message.type !== 'error' && true"
				:display-time="4000"
				@user-dismissed="message.show = false"
				@auto-dismissed="message.show = false"
			>
				<span v-i18n-html="message.text"></span>
			</cdx-message>
		</p>
	</div>
</template>

<script>
const { ref, reactive, defineComponent } = require( 'vue' );
const { CdxButton, CdxMessage } = require( '../codex.js' );
const AutoNamesSettings = require( './AutoNamesSettings.vue' );

module.exports = defineComponent( {
	name: 'AutoNamesCommunityConfiguration',
	components: { AutoNamesSettings, CdxButton, CdxMessage },
	setup() {
		const enabledValue = ref( mw.config.get( 'wgCiteAutoNamesEnabled' ) );
		const message = reactive( {
			message: '',
			type: 'success',
			show: false
		} );
		const canEdit = mw.config.get( 'wgCiteAutoNamesCanEdit' );
		const providerId = mw.config.get( 'wgCiteAutoNamesProviderId' );
		const mwApi = new mw.Api();

		function onSubmit() {
			mwApi.postWithToken( 'csrf', {
				action: 'communityconfigurationedit',
				provider: providerId,
				content: JSON.stringify( {
					AutoNamesEnabled: enabledValue.value
				} ),
				// TODO add option to set summary by the user
				summary: 'CommunityConfig Edit',
				formatversion: 2,
				errorformat: 'html'
			} ).then( ( result ) => {
				if ( result && result.communityconfigurationedit && result.communityconfigurationedit.result === 'success' ) {
					message.text = mw.message( 'communityconfiguration-editor-client-success-message', mw.user );
					message.type = 'success';

				} else {
					message.text = mw.message( 'communityconfiguration-editor-client-data-submission-error', mw.user );
					message.type = 'error';
				}
				message.show = true;
			} ).catch( () => {
				Object.assign( message, {
					text: mw.message( 'communityconfiguration-editor-client-data-submission-error', mw.user ),
					type: 'error',
					show: true
				} );
			} );

		}

		return { enabledValue, canEdit, onSubmit, message };
	}
} );
</script>

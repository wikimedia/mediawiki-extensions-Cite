<template>
	<!-- eslint-disable vue/no-v-html -->
	<div>
		<p>{{ $i18n( 'cite-configuration-autoname-desc-intro-1' ).text() }}</p>
		<p v-html="$i18n( 'cite-configuration-autoname-desc-intro-2' ).parse()"></p>

		<cdx-message
			v-if="!canEdit"
			type="notice">
			{{ $i18n( "communityconfiguration-editor-client-notice-message" ).text() }}
		</cdx-message>

		<h3>{{ $i18n( 'cite-configuration-autoname-heading' ).text() }}</h3>

		<cdx-checkbox
			v-model="localEnabled"
			:disabled="!canEdit">
			<span v-html="$i18n( 'cite-configuration-autoname-checkbox-label' )"></span>
		</cdx-checkbox>

		<br>

		<p>{{ $i18n( 'cite-configuration-autoname-desc-pretext' ).text() }}</p>

		<h3>{{ $i18n( 'cite-configuration-autoname-desc-templates-heading' ).text() }}</h3>
		<p v-html="$i18n( 'cite-configuration-autoname-desc-templates' )"></p>

		<h3>{{ $i18n( 'cite-configuration-autoname-desc-citation-type-heading' ).text() }}</h3>
		<p v-html="$i18n( 'cite-configuration-autoname-desc-citation-type-text-1' )"></p>
		<p v-html="$i18n( 'cite-configuration-autoname-desc-citation-type-text-2' ).parse()"></p>

		<h3>{{ $i18n( 'cite-configuration-autoname-desc-default-heading' ).text() }}</h3>
		<p v-html="$i18n( 'cite-configuration-autoname-desc-default-text-1' )"></p>
		<p v-html="$i18n( 'cite-configuration-autoname-desc-default-text-2' )"></p>

		<hr>

		<br>
	</div>
</template>

<script>
const { computed, defineComponent } = require( 'vue' );
const { CdxCheckbox, CdxMessage } = require( '../codex.js' );

module.exports = defineComponent( {
	components: { CdxCheckbox, CdxMessage },
	props: {
		modelValue: { type: Boolean, required: true },
		canEdit: { type: Boolean, required: true }
	},
	emits: [ 'update:modelValue' ],
	setup( props, { emit } ) {
		const localEnabled = computed( {
			get: () => props.modelValue,
			set: ( value ) => emit( 'update:modelValue', value )
		} );
		return {
			localEnabled
		};
	}
} );
</script>

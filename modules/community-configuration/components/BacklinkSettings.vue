<template>
	<cdx-field>
		<template #label>
			{{ $i18n( 'cite-configuration-backlink-marker-title' ).text() }}
		</template>
		<template #description>
			<p>{{ $i18n( 'cite-configuration-backlink-marker-description' ).text() }}</p>
			<p>{{ $i18n( 'cite-configuration-backlink-marker-help' ).text() }}</p>
		</template>
		<cdx-text-area
			v-model="backlinkAlphabetText"
			placeholder="a b c d e f g h i j k l m n o p q r s t u v w x y z"
			@input="newValue"
		></cdx-text-area>
	</cdx-field>
</template>

<script>

const { ref } = require( 'vue' );
const { CdxField, CdxTextArea } = require( '../codex.js' );

// @vue/component
module.exports = exports = {
	components: {
		CdxField,
		CdxTextArea
	},
	props: {
		backlinkAlphabet: { type: String, required: true }
	},
	emits: [ 'update' ],
	setup( props ) {
		return {
			backlinkAlphabetText: ref( props.backlinkAlphabet )
		};
	},
	methods: {
		newValue() {
			this.$emit( 'update', this.backlinkAlphabetText );
		}
	}
};
</script>

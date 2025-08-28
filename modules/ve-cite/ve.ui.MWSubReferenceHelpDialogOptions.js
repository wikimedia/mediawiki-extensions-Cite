class Options {

	/**
	 * @param {string} optionName
	 * @return {boolean}
	 */
	static loadBoolean( optionName ) {
		let value;
		if ( mw.user.isNamed() ) {
			value = mw.user.options.get( 'userjs-cite-' + optionName );
		} else {
			value = mw.storage.get( 'mw-cite-' + optionName ) ||
				mw.cookie.get( '-cite-' + optionName );
		}
		return value === '1';
	}

	/**
	 * @param {string} optionName
	 * @param {boolean} value
	 */
	static saveBoolean( optionName, value ) {
		value = value ? '1' : null;
		if ( mw.user.isNamed() ) {
			( new mw.Api() ).saveOption( 'userjs-cite-' + optionName, value );
		} else {
			// eslint-disable-next-line no-unused-expressions
			mw.storage.set( 'mw-cite-' + optionName, value ) ||
				// Fall back to a cookie when localStorage is not available
				mw.cookie.set( '-cite-' + optionName, value );
		}
	}

}

module.exports = Options;

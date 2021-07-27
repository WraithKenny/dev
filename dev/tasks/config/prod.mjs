import { merge } from 'webpack-merge';
import common from './common.mjs';
import s from '../util/settings.mjs';

export default merge( common, {
	mode: 'production',
	entry: {
		// The entry names have paths so that they are emitted to the right folders!
		[ s.folder + '/js/bundle.min' ]: [
			'./' + s.folder + '/es6/main.js',
		],
		[ s.folder + '/js/bootstrap.min' ]: [ 'bootstrap' ],
	},
} );

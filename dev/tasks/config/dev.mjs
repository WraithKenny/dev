import { merge } from 'webpack-merge';
import common from './common.mjs';
import s from '../util/settings.mjs';

export default merge( common, {
	mode: 'development',
	entry: {
		[ s.folder + '/js/bundle.min' ]: [
			'./' + s.folder + '/es6/main.js',
		],
	},
	devtool: 'source-map',
} );

import path from 'path';
import process from 'process';

const cwd = process.cwd();

const common = {
	target: 'web',
	output: {
		filename: '[name].js',
		path: path.resolve( cwd ),
		publicPath: '/wp-content/themes',
	},
	context: path.resolve( cwd ),
	module: {
		rules: [
			{
				test: /\.m?js$/,
				exclude: /(node_modules|bower_components)/,
				use: {
					loader: 'babel-loader',
				},
			},
		],
	},
	externals: {
		jquery: 'jQuery',
	},
};

export default common;

import gulp from 'gulp';
import path from 'path';
import webpack from 'webpack';
import BrowserSync from 'browser-sync';
import webpackDevMiddleware from 'webpack-dev-middleware';
import webpackHotMiddleware from 'webpack-hot-middleware';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import gulpSass from 'gulp-sass';
import nodeSass from 'node-sass';
import through2 from 'through2';
gulpSass.compiler = nodeSass;

// On 'webpack-hot-middleware/client', `?reload=true` tells client to reload if HMR fails.
const devServer = [ 'webpack/hot/dev-server', 'webpack-hot-middleware/client?reload=true' ];

let config = {
	entry: {

		// The entry names have paths so that they are emitted to the right folders!
		'sometheme/js/bundle.min': [ './sometheme/es6/main.js' ]
	},
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname ),
		publicPath: '/wp-content/themes'
	},
	context: path.resolve( __dirname ),
	mode: 'production',
	devtool: false,
	module: {
		rules: [
			{
				test: /\.m?js$/,
				exclude: /(node_modules|bower_components)/,
				use: {
					loader: 'babel-loader'
				}
			}
		]
	},
	plugins: [
		new webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
			'window.$': 'jquery'
		})
	]
};

let devConfig = {
	entry: {
		'sometheme/js/bundle.min': [ ...devServer, './sometheme/es6/main.js' ]
	},
	output: config.output,
	context: config.context,
	mode: 'development',
	devtool: 'source-map',
	module: config.module,
	plugins: [
		...config.plugins,
		new webpack.HotModuleReplacementPlugin()
	]
};

const server = BrowserSync.create();
const compiler = webpack( config );
const devCompiler = webpack( devConfig );

function reload( done ) {
	server.reload();
	done();
}

function compile( done ) {
	compiler.run( () => {
		done();
	});
}

function serve( done ) {
	server.init({

		proxy: 'https://www.wordpress.org.test',
		host: 'www.wordpress.org.test',
		open: 'external',
		logLevel: 'debug',
		https: {
			key: './ssl/localhost.key',
			cert: './ssl/localhost.crt'
		},

		middleware: [
			webpackDevMiddleware( devCompiler, {
				publicPath: devConfig.output.publicPath,
				stats: { colors: true }
			}),
			webpackHotMiddleware( devCompiler )
		]
	}, done );
}

let sassFiles = [
	'sometheme/sass/style.scss',
	'sometheme/sass/editor-style.scss'
];
let sassDest = 'sometheme/css';

function sass() {
	return gulp.src( sassFiles )
		.pipe( gulpSass({
			includePaths: [ 'node_modules' ]
		}).on( 'error', gulpSass.logError ) )
		.pipe( postcss() )
		.pipe( through2.obj( function( file, enc, cb ) {
			var date = new Date();
			file.stat.atime = date;
			file.stat.mtime = date;
			cb( null, file );
		}) )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}

function sassDev() {
	return gulp.src( sassFiles )
		.pipe( sourcemaps.init() )
		.pipe( gulpSass({
			includePaths: [ 'node_modules' ]
		}).on( 'error', gulpSass.logError ) )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( through2.obj( function( file, enc, cb ) {
			var date = new Date();
			file.stat.atime = date;
			file.stat.mtime = date;
			cb( null, file );
		}) )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}

function watchSass( done ) {
	gulp.watch([
		'sometheme/sass/**/*.{scss,sass}'
	], sassDev );
	done();
}

function watchPhp( done ) {
	gulp.watch([ 'sometheme/**/*.php' ], reload );
	done();
}

const watch = gulp.parallel( watchPhp, watchSass );

const build = gulp.series( compile, sass );
const dev = gulp.series( build, sassDev, serve, watch );

export {
	compile,
	sassDev,
	sass,
	serve,
	watch,
	build,
	dev
};
export default dev;

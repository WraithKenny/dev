import gulp from 'gulp';
import path from 'path';
import Fiber from 'fibers';
import webpack from 'webpack';
import BrowserSync from 'browser-sync';
import webpackDevMiddleware from 'webpack-dev-middleware';
import webpackHotMiddleware from 'webpack-hot-middleware';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import gulpSass from 'gulp-sass';
import dartSass from 'sass';
import through2 from 'through2';
gulpSass.compiler = dartSass;

const pipeSass = () => gulpSass({
	fiber: Fiber,
	includePaths: [ 'node_modules' ]
}).on( 'error', gulpSass.logError );

const touch = () => through2.obj( function( file, enc, cb ) {
	var date = new Date();
	file.stat.atime = date;
	file.stat.mtime = date;
	cb( null, file );
});

// On 'webpack-hot-middleware/client', `?reload=true` tells client to reload if HMR fails.
const devServer = [ 'webpack/hot/dev-server', 'webpack-hot-middleware/client?reload=true' ];

let config = {
	entry: {

		// The entry names have paths so that they are emitted to the right folders!
		'some-theme/js/bundle.min': [ './some-theme/es6/main.js' ]
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
		'some-theme/js/bundle.min': [ ...devServer, './some-theme/es6/main.js' ]
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

let sassDest = 'some-theme/css';
let sassInlineFiles = 'some-theme/sass/inline.scss';
let sassThemeFiles = 'some-theme/sass/style.scss';
let sassEditorFiles = 'some-theme/sass/editor-style.scss';
let sassFiles = [ sassInlineFiles, sassThemeFiles, sassEditorFiles ];

function sass() {
	return gulp.src( sassFiles )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( touch() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}

function sassDev() {
	return gulp.src( sassThemeFiles )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( touch() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}
function sassDevEditor() {
	return gulp.src( sassEditorFiles )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( touch() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}
function sassDevInline() {
	return gulp.src( sassInlineFiles )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( touch() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}

function watchSass( done ) {
	gulp.watch([
		'some-theme/sass/common/**/*.{scss,sass}',
		'some-theme/sass/inline.scss',
		'some-theme/sass/inline/**/*.{scss,sass}'
	], sassDevInline );
	gulp.watch([
		'some-theme/sass/common/**/*.{scss,sass}',
		'some-theme/sass/style.scss',
		'some-theme/sass/theme/**/*.{scss,sass}'
	], sassDev );
	gulp.watch([
		'some-theme/sass/common/**/*.{scss,sass}',
		'some-theme/sass/editor-style.scss',
		'some-theme/sass/editor/**/*.{scss,sass}'
	], sassDevEditor );
	done();
}

function watchPhp( done ) {
	gulp.watch([ 'some-theme/**/*.php' ], reload );
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

import gulp from 'gulp';
import path from 'path';
import webpack from 'webpack';
import { merge } from 'webpack-merge';
import BrowserSync from 'browser-sync';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import gulpSassC from 'gulp-sass';
import sass from 'sass';
import conf from 'pkg-conf';
import { fileURLToPath } from 'url';
import del from 'del';

const __filename = fileURLToPath( import.meta.url );
const __dirname = path.dirname( __filename );

const gulpSass = gulpSassC( sass );
const settings = conf.sync( 'dev' );

// Sync is faster than Async, and Fibers is obsolete.
const pipeSass = () =>
	gulpSass
		.sync( {
			includePaths: [ 'node_modules' ],
		} )
		.on( 'error', gulpSass.logError );

const common = {
	target: 'web',
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname ),
		publicPath: '/wp-content/themes',
	},
	context: path.resolve( __dirname ),
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

// BrowserSnyc.
const server = BrowserSync.create();
function serve( cb ) {
	server.init(
		{
			proxy: 'https://' + settings.domain + '.test',
			host: settings.domain + '.test',
			open: 'external',
			ghostMode: false,
			logLevel: 'info',
			https: {
				key: './dev/files/ssl/localhost.key',
				cert: './dev/files/ssl/localhost.crt',
			},
		},
		cb
	);
}
function reload( cb ) {
	server.reload();
	cb();
}

// For JS Build.
const config = merge( common, {
	mode: 'production',
	entry: {
		// The entry names have paths so that they are emitted to the right folders!
		[ settings.folder + '/js/bundle.min' ]: [
			'./' + settings.folder + '/es6/main.js',
		],
		[ settings.folder + '/js/bootstrap.min' ]: [ 'bootstrap' ],
	},
} );
const compiler = webpack( config );
function compile( cb ) {
	compiler.run( () => {
		cb();
	} );
}

// For JS Dev.
const devConfig = merge( common, {
	mode: 'development',
	entry: {
		[ settings.folder + '/js/bundle.min' ]: [
			'./' + settings.folder + '/es6/main.js',
		],
	},
	devtool: 'source-map',
} );
const devCompiler = webpack( devConfig );
function compileReload( cb ) {
	devCompiler.run( () => {
		server.reload();
		cb();
	} );
}

const sassDest = settings.folder + '/css/';
const sassFolder = settings.folder + '/sass/';
const sassFiles = sassFolder + '**/*.{scss,sass}';

function sassCompile() {
	return gulp
		.src( sassFiles )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( gulp.dest( sassDest ) );
}

function watchPhp( cb ) {
	gulp.watch( settings.folder + '/**/*.php', reload );
	cb();
}

function watchJs( cb ) {
	gulp.watch( settings.folder + '/es6/**/*.js', compileReload );
	cb();
}


function sassDevCompile() {
	return gulp
		.src( sassFiles, {
			allowEmpty: true,
			since: gulp.lastRun( sassDevCompile )
		} )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}
function watchSass( cb ) {
	gulp.watch( sassFiles, { ignoreInitial: false }, sassDevCompile );
	cb();
}

const build = gulp.series( compile, sassCompile );
const dev = gulp.series( serve, gulp.parallel( watchPhp, watchJs, watchSass ) );

export { build, dev };
export default dev;

import gulp from 'gulp';
import path from 'path';
import webpack from 'webpack';
import { merge } from 'webpack-merge';
import BrowserSync from 'browser-sync';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import gulpSass from 'gulp-sass';
import dartSass from 'sass';
import conf from 'pkg-conf';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath( import.meta.url );
const __dirname = path.dirname( __filename );

const sassGulp = gulpSass( dartSass );
const settings = conf.sync( 'dev' );

// Sync is faster than Async, and Fibers is obsolete.
const pipeSass = () =>
	sassGulp
		.sync( {
			includePaths: [ 'node_modules' ],
		} )
		.on( 'error', sassGulp.logError );

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
function serve( done ) {
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
		done
	);
}
function reload( done ) {
	server.reload();
	done();
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
function compile( done ) {
	compiler.run( () => {
		done();
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
function compileReload( done ) {
	devCompiler.run( () => {
		server.reload();
		done();
	} );
}

const sassDest = settings.folder + '/css';
const sassInlineFiles = settings.folder + '/sass/inline.scss';
const sassThemeFiles = settings.folder + '/sass/style.scss';
const sassEditorFiles = settings.folder + '/sass/editor-style.scss';
const sassFiles = [ sassInlineFiles, sassThemeFiles, sassEditorFiles ];

function sass() {
	return gulp
		.src( sassFiles )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}

function sassDev() {
	return gulp
		.src( sassThemeFiles )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}
function sassDevEditor() {
	return gulp
		.src( sassEditorFiles )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}
function sassDevInline() {
	return gulp
		.src( sassInlineFiles )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( sassDest ) )
		.pipe( server.stream() );
}

function watchSass() {
	gulp.watch(
		[
			settings.folder + '/sass/common/**/*.{scss,sass}',
			settings.folder + '/sass/inline.scss',
			settings.folder + '/sass/inline/**/*.{scss,sass}',
		],
		sassDevInline
	);
	gulp.watch(
		[
			settings.folder + '/sass/common/**/*.{scss,sass}',
			settings.folder + '/sass/style.scss',
			settings.folder + '/sass/theme/**/*.{scss,sass}',
		],
		sassDev
	);
	gulp.watch(
		[
			settings.folder + '/sass/common/**/*.{scss,sass}',
			settings.folder + '/sass/editor-style.scss',
			settings.folder + '/sass/editor/**/*.{scss,sass}',
		],
		sassDevEditor
	);
}

function watchPhp() {
	gulp.watch( [ settings.folder + '/**/*.php' ], reload );
}

function watchJs() {
	gulp.watch( [ settings.folder + '/es6/**/*.js' ], compileReload );
}

const watch = gulp.parallel( watchPhp, watchJs, watchSass );

const build = gulp.series( compile, sass );
const dev = gulp.series( build, sassDev, serve, watch );

export { build, dev };
export default dev;

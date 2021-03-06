import gulp from 'gulp';
import path from 'path';
import webpack from 'webpack';
import { merge } from 'webpack-merge';
import BrowserSync from 'browser-sync';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import gulpSass from 'gulp-dart-sass';
import dartSass from 'sass';
gulpSass.compiler = dartSass;

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

const config = merge( common, {
	mode: 'production',
	entry: {
		// The entry names have paths so that they are emitted to the right folders!
		'some-theme/js/bundle.min': [ './some-theme/es6/main.js' ],
		'some-theme/js/bootstrap.min': [ 'bootstrap' ],
	},
} );

const devConfig = merge( common, {
	mode: 'development',
	entry: {
		'some-theme/js/bundle.min': [ './some-theme/es6/main.js' ],
	},
	devtool: 'source-map',
} );

const server = BrowserSync.create();
const compiler = webpack( config );
const devCompiler = webpack( devConfig );

function reload( done ) {
	server.reload();
	done();
}
function reloadJs( done ) {
	devCompiler.run( () => {
		server.reload();
		done();
	} );
}

function compile( done ) {
	compiler.run( () => {
		done();
	} );
}

function serve( done ) {
	server.init(
		{
			proxy: 'https://www.wordpress.org.test',
			host: 'www.wordpress.org.test',
			open: 'external',
			logLevel: 'debug',
			https: {
				key: './dev/files/ssl/localhost.key',
				cert: './dev/files/ssl/localhost.crt',
			},
		},
		done
	);
}

const sassDest = 'some-theme/css';
const sassInlineFiles = 'some-theme/sass/inline.scss';
const sassThemeFiles = 'some-theme/sass/style.scss';
const sassEditorFiles = 'some-theme/sass/editor-style.scss';
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

function watchSass( done ) {
	gulp.watch(
		[
			'some-theme/sass/common/**/*.{scss,sass}',
			'some-theme/sass/inline.scss',
			'some-theme/sass/inline/**/*.{scss,sass}',
		],
		sassDevInline
	);
	gulp.watch(
		[
			'some-theme/sass/common/**/*.{scss,sass}',
			'some-theme/sass/style.scss',
			'some-theme/sass/theme/**/*.{scss,sass}',
		],
		sassDev
	);
	gulp.watch(
		[
			'some-theme/sass/common/**/*.{scss,sass}',
			'some-theme/sass/editor-style.scss',
			'some-theme/sass/editor/**/*.{scss,sass}',
		],
		sassDevEditor
	);
	done();
}

function watchPhp( done ) {
	gulp.watch( [ 'some-theme/**/*.php' ], reload );
	done();
}

function watchJs( done ) {
	gulp.watch( [ 'some-theme/es6/**/*.js' ], reloadJs );
	done();
}

const watch = gulp.parallel( watchPhp, watchJs, watchSass );

const build = gulp.series( compile, sass );
const dev = gulp.series( build, sassDev, serve, watch );

export { compile, sassDev, sass, serve, watch, build, dev };
export default dev;

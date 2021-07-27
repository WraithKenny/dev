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
import through2 from 'through2';
import lodash from 'lodash';
import del from 'del';

const { isEqual } = lodash;

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
// Save a collection of partials/dependencies.
let partials = {};
const collectPartials = () =>
	through2.obj( function( file, _, cb ) {
		let sassStats;
		let sassName;

		// Until `gulpSass` passes the `stats` obj.
		if ( file.sassStats ) {
			sassStats = file.sassStats;
			sassName = sassStats.entry;
		} else {
			// Without changes to gulp-sass, this doubles the compile time.
			const results = sass.renderSync({
				file: file.history[0],
				includePaths: [
					file.base,
					'node_modules',
				]
			});
			sassStats = results.stats;
			sassName = file.history[0];
		}

		sassName = sassName.replace( file.base + path.sep, sassFolder );

		let files = sassStats.includedFiles
			.filter( item => item.includes( sassFolder ) )
			.map( item => item.replace( file.base + path.sep, sassFolder ) );

		const watchfunction = function() {
			return gulp
				.src( sassName, { allowEmpty: true } )
				.pipe( sourcemaps.init() )
				.pipe( pipeSass() )
				.pipe( collectPartials() )
				.pipe( postcss() )
				.pipe( sourcemaps.write() )
				.pipe( gulp.dest( sassDest ) )
				.pipe( server.stream() );
		}
		watchfunction.displayName = 'compile ' + sassName;

		if ( partials.hasOwnProperty( sassName ) ) {
			// Check for dependency changes.
			if ( ! isEqual( files, partials[ sassName ].files ) ) {
				// Close old.
				partials[ sassName ].watcher.close();

				// Updated watcher.
				const watcher = gulp.watch( files, watchfunction );
				partials[ sassName ] = {
					files,
					watcher
				};
			}
		} else {
			// New.
			const watcher = gulp.watch( files, watchfunction );
			partials[ sassName ] = {
				files,
				watcher
			};
		}

		cb( null, file );
	} );

function watchSassFolder( cb ) {
	// We need to account for added and deleted files.
	const watcher = gulp.watch( sassFiles, { events: [ 'add', 'unlink' ] } );
	watcher.on( 'add', function( sassName ) {

		if ( 0 === path.basename( sassName ).indexOf( '_' ) ) {
			return;
		}

		if ( path.extname( sassName ) !== '.scss' && path.extname( sassName ) !== '.sass' ) {
			return;
		}

		// Create a watcher.
		const watchfunction = function() {
			return gulp
				.src( sassName, { allowEmpty: true } )
				.pipe( sourcemaps.init() )
				.pipe( pipeSass() )
				.pipe( collectPartials() )
				.pipe( postcss() )
				.pipe( sourcemaps.write() )
				.pipe( gulp.dest( sassDest ) )
				.pipe( server.stream() );
		}
		watchfunction.displayName = 'compile ' + sassName;

		const watcher = gulp.watch( sassName, { ignoreInitial: false }, watchfunction );
		partials[ sassName ] = {
			sassName,
			watcher
		};
	} );
	watcher.on( 'unlink', function( sassName ) {

		if ( 0 === path.basename( sassName ).indexOf( '_' ) ) {
			return;
		}

		if ( path.extname( sassName ) !== '.scss' && path.extname( sassName ) !== '.sass' ) {
			return;
		}

		partials[ sassName ].watcher.close();
		delete partials[ sassName ];

		const cssName = sassName.replace( sassFolder, sassDest ).replace( path.extname( sassName ), '.css' );
		del.sync( cssName );
	} );
	cb();
}

function primePartials() {
	return gulp
		.src( sassFiles )
		.pipe( pipeSass() )
		.pipe( collectPartials() );
}

const watchSass = gulp.series( primePartials, watchSassFolder );

const build = gulp.series( compile, sassCompile );
const dev = gulp.series( serve, gulp.parallel( watchPhp, watchJs, watchSass ) );

export { build, dev };
export default dev;

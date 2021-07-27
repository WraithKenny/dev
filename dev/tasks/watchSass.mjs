import process from 'process';
import gulp from 'gulp';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import server from './util/server.mjs';
import pipeSass from './util/pipeSass.mjs';
import s from './util/settings.mjs';

const cwd = process.cwd();

function compileSassDev() {
	return gulp
		.src( s.sass.src, {
			allowEmpty: true
		} )
		.pipe( sourcemaps.init() )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( s.sass.dest ) )
		.pipe( server.stream() );
}

export default function watchSass( cb ) {
	gulp.watch( s.sass.src, { ignoreInitial: false, cwd }, compileSassDev );
	cb();
};

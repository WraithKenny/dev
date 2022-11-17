import gulp from 'gulp';
import postcss from 'gulp-postcss';
import pipeSass from './util/pipeSass.mjs';
import s from './util/settings.mjs';

export default function compileSass() {
	return gulp
		.src( s.sass.src )
		.pipe( pipeSass() )
		.pipe( postcss() )
		.pipe( gulp.dest( s.sass.dest ) );
}
